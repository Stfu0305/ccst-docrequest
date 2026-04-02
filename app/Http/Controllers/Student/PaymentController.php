<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\OfficialReceipt;
use App\Models\PaymentProof;
use App\Models\StatusLog;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    use SendsDatabaseNotifications;

    // ─────────────────────────────────────────────────────────────────────────
    // SET PAYMENT METHOD
    // PATCH /student/requests/{id}/payment-method
    // ─────────────────────────────────────────────────────────────────────────
    public function setMethod(Request $request, $id)
    {
        $docRequest = DocumentRequest::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($docRequest->status, ['pending', 'payment_method_set'])) {
            return back()->with('error', 'Payment method cannot be changed at this stage.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:gcash,bank_transfer,cash',
        ]);

        $oldStatus = $docRequest->status;

        $docRequest->update([
            'payment_method' => $validated['payment_method'],
            'status'         => 'payment_method_set',
        ]);

        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => Auth::id(),
            'old_status'          => $oldStatus,
            'new_status'          => 'payment_method_set',
            'notes'               => 'Payment method set to: ' . $validated['payment_method'],
        ]);

        $methodLabel = match ($validated['payment_method']) {
            'gcash'         => 'GCash',
            'bank_transfer' => 'Bank Transfer',
            'cash'          => 'Over-the-Counter Cash',
            default         => $validated['payment_method'],
        };

        // Send notification instead of flash message
        $message = $methodLabel . ' selected as your payment method. ' . (
            $validated['payment_method'] === 'cash'
                ? 'Please visit the cashier office to pay in person.'
                : 'Please proceed to upload your proof of payment.'
        );
        $url = route('student.payments.showUpload', $docRequest->id);
        $this->sendNotificationToCurrentUser($message, $url);

        return redirect()->route('student.requests.show', $docRequest->id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW UPLOAD PAGE
    // GET /student/requests/{id}/upload
    // ─────────────────────────────────────────────────────────────────────────
    public function showUpload($id)
    {
        $user = Auth::user();

        $docRequest = DocumentRequest::with(['paymentProof'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        // Cash payments have no upload step
        if ($docRequest->payment_method === 'cash') {
            return redirect()
                ->route('student.requests.show', $id)
                ->with('error', 'Cash payments do not require an upload. Please visit the cashier office.');
        }

        // ── Guard: must have a payment method set first ──────────────────────
        // If the student somehow reaches this URL without selecting a method,
        // send them back to the summary page to choose one first.
        if (is_null($docRequest->payment_method)) {
            return redirect()
                ->route('student.requests.show', $id)
                ->with('error', 'Please select a payment method first.');
        }

        // Allow access if status is any of these AND cashier hasn't verified yet
        $canUpload = in_array($docRequest->status, [
                'payment_method_set',
                'payment_uploaded',
                'payment_rejected',
            ])
            && is_null($docRequest->paymentProof?->verified_at);

        if (! $canUpload) {
            return redirect()
                ->route('student.requests.show', $id)
                ->with('error', 'You cannot upload a proof for this request at this stage.');
        }

        return view('student.payments.upload', compact('docRequest'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE PROOF UPLOAD (first upload)
    // POST /student/requests/{id}/upload
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request, $id)
    {
        return $this->handleUpload($request, $id, isReupload: false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RE-UPLOAD REJECTED PROOF
    // POST /student/requests/{id}/reupload
    // ─────────────────────────────────────────────────────────────────────────
    public function reupload(Request $request, $id)
    {
        return $this->handleUpload($request, $id, isReupload: true);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHARED UPLOAD LOGIC
    // ─────────────────────────────────────────────────────────────────────────
    private function handleUpload(Request $request, $id, bool $isReupload)
    {
        // ── 1. Validate incoming data ────────────────────────────────────────
        $validated = $request->validate([
            'proof_file'       => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'reference_number' => 'nullable|string|max:100',
            'amount_declared'  => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();

        // ── 2. Load request with ownership check ─────────────────────────────
        $docRequest = DocumentRequest::with(['paymentProof'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        // ── 3. Status guard ───────────────────────────────────────────────────
        // For re-upload: status must be payment_uploaded or payment_rejected
        //                AND cashier has not yet acted (verified_at is null)
        //
        // For first upload: status must be payment_method_set OR payment_uploaded
        //                   AND cashier has not yet acted (verified_at is null)
        //
        // Why allow payment_uploaded on first upload path?
        // The student may have submitted the form, hit back, and submitted again.
        // The showUpload page lets them in for payment_uploaded — the POST handler
        // must be consistent and allow it too, otherwise they get a 403.
        if ($isReupload) {
            $allowed = in_array($docRequest->status, ['payment_uploaded', 'payment_rejected'])
                && is_null($docRequest->paymentProof?->verified_at);
        } else {
            $allowed = in_array($docRequest->status, ['payment_method_set', 'payment_uploaded'])
                && is_null($docRequest->paymentProof?->verified_at);
        }

        if (! $allowed) {
            abort(403, 'You are not allowed to upload a proof at this stage.');
        }

        // ── 4. Duplicate reference number check ──────────────────────────────
        $refNumber = $validated['reference_number'] ?? null;

        if (! empty($refNumber)) {
            $duplicate = PaymentProof::where('reference_number', $refNumber)
                ->whereHas('documentRequest', function ($q) use ($docRequest) {
                    $q->where('status', 'payment_verified')
                      ->where('id', '!=', $docRequest->id);
                })
                ->exists();

            if ($duplicate) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'reference_number' => 'This reference number has already been used for a previous verified payment.',
                    ]);
            }
        }

        // ── 5. Delete the old proof if re-uploading ───────────────────────────
        // Also delete on the non-reupload path if a previous proof exists
        // (handles the case where student uploads twice on the same request)
        if ($docRequest->paymentProof) {
            Storage::disk('local')->delete($docRequest->paymentProof->file_path);
            $docRequest->paymentProof->delete();
        }

        // ── 6. Store the new file ─────────────────────────────────────────────
        $file     = $validated['proof_file'];
        $filename = time() . '_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('payments', $filename, 'local');

        // ── 7. Create the payment_proofs row ──────────────────────────────────
        PaymentProof::create([
            'document_request_id' => $docRequest->id,
            'file_path'           => $filePath,
            'original_filename'   => $file->getClientOriginalName(),
            'file_size_kb'        => (int) round($file->getSize() / 1024),
            'amount_declared'     => $validated['amount_declared'] ?? $docRequest->total_fee,
            'reference_number'    => $refNumber,
            'is_resubmission'     => $isReupload,
        ]);

        // ── 8. Update request status ──────────────────────────────────────────
        $oldStatus = $docRequest->status;
        $docRequest->update(['status' => 'payment_uploaded']);

        // ── 9. Log the change ─────────────────────────────────────────────────
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => $user->id,
            'old_status'          => $oldStatus,
            'new_status'          => 'payment_uploaded',
            'notes'               => $isReupload
                ? 'Student re-uploaded payment proof after rejection.'
                : 'Student uploaded payment proof.',
        ]);

        // ── 10. Send notification instead of flash message ────────────────────
        $message = $isReupload
            ? 'Your proof has been re-uploaded successfully. The cashier will review it shortly.'
            : 'Payment proof uploaded successfully! The cashier will verify it soon.';
        $url = route('student.requests.history');
        $this->sendNotificationToCurrentUser($message, $url);

        // ── 11. Redirect ──────────────────────────────────────────────────────
        return redirect()->route('student.requests.history');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD OFFICIAL RECEIPT PDF
    // GET /student/receipts/{id}/download
    // ─────────────────────────────────────────────────────────────────────────
    public function downloadReceipt($id)
    {
        $user = Auth::user();

        $receipt = OfficialReceipt::whereHas('documentRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('document_request_id', $id)
            ->firstOrFail();

        $fullPath = storage_path('app/private/' . $receipt->file_path);

        if (! file_exists($fullPath)) {
            return back()->with('error', 'Receipt file not found. Please contact the registrar office.');
        }

        return response()->download(
            $fullPath,
            'Official-Receipt-' . $receipt->receipt_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}