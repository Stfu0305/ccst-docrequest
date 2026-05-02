<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestItem;
use App\Models\DocumentType;
use App\Models\StatusLog;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    use SendsDatabaseNotifications;

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
            'full_name'        => $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name,
            'contact_number'   => $validated['contact_number'],
            'course_program'   => $validated['course_program'],
            'year_level'       => $validated['year_level'],
            'section'          => $validated['section'],
            'total_fee'        => 0,
            'status'           => 'pending',
            'is_printable'     => false, // Will be set after checking document types
        ]);

        // Step 2: Generate the reference number
        $docRequest->update([
            'reference_number' => 'DQST-' . date('Y') . '-' . str_pad($docRequest->id, 5, '0', STR_PAD_LEFT),
        ]);

        // Step 3: Create one DocumentRequestItem per selected document.
        $totalFee = 0;
        $allPrintable = true;

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

            // Check if any document is non-printable
            if (!$docType->is_printable) {
                $allPrintable = false;
            }
        }

        // Step 4: Save the calculated total and printable flag
        $docRequest->update([
            'total_fee'     => $totalFee,
            'is_printable'  => $allPrintable,
        ]);

        // Step 5: Write the initial status log entry.
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => $user->id,
            'old_status'          => null,
            'new_status'          => 'pending',
            'notes'               => 'Request submitted by student.',
        ]);

        // Send notification to student
        $message = 'Your request has been submitted! Reference: ' . $docRequest->reference_number;
        $url = route('student.requests.show', $docRequest->id);
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        // Redirect to summary with appointment modal, regardless of printability
        return redirect()->route('student.requests.show', $docRequest->id)
            ->with('show_appointment_modal', true)
            ->with('success', 'Your request has been submitted! Please book an appointment for pickup.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW REQUEST SUMMARY
    // GET /student/requests/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $docRequest = DocumentRequest::with(['items.documentType', 'paymentProof'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $paymentSettings = \App\Models\PaymentSetting::where('is_active', true)->get()->keyBy('method');

        // Get time slots if appointment modal should be shown
        $timeSlots = null;
        $showAppointmentModal = session('show_appointment_modal', false);
        if ($showAppointmentModal) {
            $timeSlots = \App\Models\TimeSlot::where('is_active', true)->orderBy('start_time')->get();
            // Clear the session flag so modal doesn't show again on refresh
            session()->forget('show_appointment_modal');
        }

        return view('student.requests.show', compact('docRequest', 'paymentSettings', 'timeSlots', 'showAppointmentModal'));
    }

    // ─────────────────────────────────────────────────────────────────────────
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
        session()->flash('check_notifications', true);

        return redirect()->route('student.requests.history');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REQUEST HISTORY
    // GET /student/history
    // ─────────────────────────────────────────────────────────────────────────
    public function history()
    {
        $requests = DocumentRequest::with(['items.documentType', 'appointment.timeSlot'])
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