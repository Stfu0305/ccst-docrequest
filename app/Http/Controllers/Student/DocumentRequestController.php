<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestItem;
use App\Models\DocumentType;
use App\Models\PaymentSetting;
use App\Models\StatusLog;
use App\Traits\SendsDatabaseNotifications; // ← ADD THIS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    use SendsDatabaseNotifications; // ← ADD THIS

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW REQUEST FORM
    // GET /student/requests/create
    // ─────────────────────────────────────────────────────────────────────────
    public function create()
    {
        // Load only active document types so deactivated ones don't appear
        $documentTypes = DocumentType::where('is_active', true)->get();

        return view('student.requests.create', compact('documentTypes'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE NEW REQUEST
    // POST /student/requests
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // ── STEP 1: Strip out empty rows BEFORE validation ───────────────────────
        $rawDocuments = collect($request->input('documents', []))
            ->filter(function ($doc) {
                return !empty($doc['document_type_id'])
                    && isset($doc['copies'])
                    && (int) $doc['copies'] > 0;
            })
            ->values()
            ->toArray();

        $request->merge(['documents' => $rawDocuments]);

        // ── STEP 2: Now validate ─────────────────────────────────────────────────
        $validated = $request->validate([
            'contact_number'               => 'required|string|max:20',
            'course_program'               => 'required|string|max:255',
            'year_level'                   => 'required|string|max:20',
            'section'                      => 'required|string|max:50',
            'documents'                    => 'required|array|min:1',
            'documents.*.document_type_id' => 'required|integer|exists:document_types,id',
            'documents.*.copies'           => 'required|integer|min:1|max:99',
            'documents.*.assessment_year'  => 'nullable|string|max:20',
            'documents.*.semester'         => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        // Step 1: Create the DocumentRequest row.
        $docRequest = DocumentRequest::create([
            'reference_number' => 'TEMP',
            'user_id'          => $user->id,
            'student_number'   => $user->student_number ?? 'N/A',
            'full_name'        => $user->name,
            'contact_number'   => $validated['contact_number'],
            'course_program'   => $validated['course_program'],
            'year_level'       => $validated['year_level'],
            'section'          => $validated['section'],
            'total_fee'        => 0,
            'status'           => 'pending',
        ]);

        // Step 2: Generate the reference number
        $docRequest->update([
            'reference_number' => 'DQST-' . date('Y') . '-' . str_pad($docRequest->id, 5, '0', STR_PAD_LEFT),
        ]);

        // Step 3: Create one DocumentRequestItem per selected document.
        $totalFee = 0;
        foreach ($validated['documents'] as $doc) {
            $docType = DocumentType::findOrFail($doc['document_type_id']);

            DocumentRequestItem::create([
                'document_request_id' => $docRequest->id,
                'document_type_id'    => $doc['document_type_id'],
                'copies'              => $doc['copies'],
                'assessment_year'     => $doc['assessment_year'] ?? null,
                'semester'            => $doc['semester'] ?? null,
                'fee'                 => $docType->fee,
            ]);

            $totalFee += $docType->fee * $doc['copies'];
        }

        // Step 4: Save the calculated total.
        $docRequest->update(['total_fee' => $totalFee]);

        // Step 5: Write the initial status log entry.
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => $user->id,
            'old_status'          => null,
            'new_status'          => 'pending',
            'notes'               => 'Request submitted by student.',
        ]);

        // ✅ CHANGED: Send notification instead of flash message
        $message = 'Your request has been submitted! Reference: ' . $docRequest->reference_number . '. Please choose a payment method below.';
        $url = route('student.requests.show', $docRequest->id);
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('new_notification', true);

        // Step 6: Redirect to the Request Summary page (NO flash message)
        return redirect()->route('student.requests.show', $docRequest->id);

        // Step 6: Redirect to the Request Summary page.
        // The student will choose a payment method there.

        // ✅ CHANGED: Send notification instead of flash message
        $message = 'Your request has been submitted! Reference: ' . $docRequest->reference_number . '. Please choose a payment method below.';
        $url = route('student.requests.show', $docRequest->id);

        // DEBUG: Log before sending
        \Log::info('Attempting to send notification', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'message' => $message,
            'url' => $url
        ]);

        $this->sendNotificationToCurrentUser($message, $url);

        // DEBUG: Check after sending
        \Log::info('Notification sent, checking database');
        $checkCount = $user->notifications()->count();
        \Log::info('User now has ' . $checkCount . ' notifications');

        // Step 6: Redirect to the Request Summary page (NO flash message)
        return redirect()->route('student.requests.show', $docRequest->id);

    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW REQUEST SUMMARY + PAYMENT METHOD SELECTOR
    // GET /student/requests/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $docRequest = DocumentRequest::with(['items.documentType'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $paymentSettings = PaymentSetting::where('is_active', true)
            ->get()
            ->keyBy('method');

        return view('student.requests.show', compact('docRequest', 'paymentSettings'));
    }

    // CANCEL REQUEST
    // DELETE /student/requests/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function cancel($id)
    {
        $docRequest = DocumentRequest::where('user_id', Auth::id())->findOrFail($id);

        // Only pending requests can be cancelled
        if ($docRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }
        
        // Check if there's an active appointment
        $activeAppointment = \App\Models\Appointment::where('document_request_id', $docRequest->id)
            ->where('status', 'scheduled')
            ->first();
        
        if ($activeAppointment) {
            return back()->with('error', 'Please cancel your appointment first before cancelling the request.');
        }

        $oldStatus = $docRequest->status;
        $docRequest->update(['status' => 'cancelled']);

        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => Auth::id(),
            'old_status'          => $oldStatus,
            'new_status'          => 'cancelled',
            'notes'               => 'Request cancelled by student.',
        ]);

        // Send notification
        $message = 'Request ' . $docRequest->reference_number . ' has been cancelled.';
        $url = route('student.requests.history');
        $this->sendNotificationToCurrentUser($message, $url);

        return redirect()->route('student.requests.history');
    }

    // REQUEST HISTORY
    // GET /student/history
    // ─────────────────────────────────────────────────────────────────────────
    public function history()
    {
        $requests = DocumentRequest::with(['items.documentType', 'paymentProof', 'officialReceipt', 'appointment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        // Get all active time slots for the booking modal
        $timeSlots = \App\Models\TimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return view('student.history.index', compact('requests', 'timeSlots'));
    }
}