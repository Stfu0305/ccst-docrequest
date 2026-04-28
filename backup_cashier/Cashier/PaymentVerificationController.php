<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\User;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;

class PaymentVerificationController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        $payments = DocumentRequest::whereNotNull('payment_method')
            ->with(['paymentProof', 'officialReceipt'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount = DocumentRequest::whereIn('status', ['payment_uploaded', 'payment_method_set'])
            ->whereNotNull('payment_method')
            ->count();

        $verifiedToday = DocumentRequest::where('status', 'payment_verified')
            ->whereHas('officialReceipt', function($q) {
                $q->whereDate('issued_at', today());
            })
            ->count();

        return view('cashier.payments.index', compact('payments', 'pendingCount', 'verifiedToday'));
    }

    public function show($id)
    {
        $payment = DocumentRequest::with(['items.documentType', 'paymentProof', 'officialReceipt'])
            ->whereNotNull('payment_method')
            ->findOrFail($id);

        return view('cashier.payments.show', compact('payment'));
    }

    public function verify($id)
    {
        $payment = DocumentRequest::findOrFail($id);
        
        if (!in_array($payment->payment_method, ['gcash', 'bank_transfer'])) {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'Use "Mark as Paid" for cash payments.');
        }
        
        if ($payment->status !== 'payment_uploaded') {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'This payment cannot be verified at this stage.');
        }

        $payment->update(['status' => 'payment_verified']);

        if ($payment->paymentProof) {
            $payment->paymentProof->update([
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }

        // Send notification to STUDENT
        $student = User::find($payment->user_id);
        if ($student) {
            $studentMessage = '✅ Your payment for request ' . $payment->reference_number . ' has been verified. Your documents are now being processed.';
            $studentUrl = route('student.requests.history');
            $this->sendNotification($student, $studentMessage, $studentUrl);
        }

        // Send notification to CASHIER (current user)
        $cashierMessage = '✅ You verified payment for request ' . $payment->reference_number . ' - Amount: ₱' . number_format($payment->total_fee, 2);
        $cashierUrl = route('cashier.payments.show', $payment->id);
        $this->sendNotificationToCurrentUser($cashierMessage, $cashierUrl);

        session()->flash('check_notifications', true);

        return redirect()->route('cashier.payments.index');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $payment = DocumentRequest::findOrFail($id);
        
        if (!in_array($payment->payment_method, ['gcash', 'bank_transfer'])) {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'Cash payments cannot be rejected. Use "Mark as Paid" instead.');
        }
        
        if ($payment->status !== 'payment_uploaded') {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'This payment cannot be rejected at this stage.');
        }

        $payment->update(['status' => 'payment_rejected']);
        
        if ($payment->paymentProof) {
            $payment->paymentProof->update([
                'rejection_reason' => $request->rejection_reason,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }

        // Send notification to STUDENT
        $student = User::find($payment->user_id);
        if ($student) {
            $studentMessage = '❌ Your payment proof for request ' . $payment->reference_number . ' was rejected. Reason: ' . $request->rejection_reason . '. Please re-upload a valid proof.';
            $studentUrl = route('student.payments.showUpload', $payment->id);
            $this->sendNotification($student, $studentMessage, $studentUrl);
        }

        // Send notification to CASHIER (current user)
        $cashierMessage = '❌ You rejected payment for request ' . $payment->reference_number . ' - Reason: ' . $request->rejection_reason;
        $cashierUrl = route('cashier.payments.show', $payment->id);
        $this->sendNotificationToCurrentUser($cashierMessage, $cashierUrl);

        session()->flash('check_notifications', true);

        return redirect()->route('cashier.payments.index');
    }

    public function markCashPaid($id)
    {
        $payment = DocumentRequest::findOrFail($id);
        
        if ($payment->payment_method !== 'cash') {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'This payment method does not use cash marking.');
        }
        
        if ($payment->status !== 'payment_method_set') {
            return redirect()
                ->route('cashier.payments.show', $id)
                ->with('error', 'This cash payment has already been processed.');
        }

        $payment->update(['status' => 'payment_verified']);

        // Send notification to STUDENT
        $student = User::find($payment->user_id);
        if ($student) {
            $studentMessage = '✅ Your cash payment for request ' . $payment->reference_number . ' has been recorded. Your documents are now being processed.';
            $studentUrl = route('student.requests.history');
            $this->sendNotification($student, $studentMessage, $studentUrl);
        }

        // Send notification to CASHIER (current user)
        $cashierMessage = '💰 You marked cash payment as paid for request ' . $payment->reference_number . ' - Amount: ₱' . number_format($payment->total_fee, 2);
        $cashierUrl = route('cashier.payments.show', $payment->id);
        $this->sendNotificationToCurrentUser($cashierMessage, $cashierUrl);

        session()->flash('check_notifications', true);

        return redirect()->route('cashier.payments.index');
    }

    public function serveProof($id)
    {
        $payment = DocumentRequest::findOrFail($id);
        
        if (!$payment->paymentProof || !$payment->paymentProof->file_path) {
            abort(404);
        }

        $path = storage_path('app/private/' . $payment->paymentProof->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);
        
        return response()->file($path, ['Content-Type' => $mime]);
    }
}