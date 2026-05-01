<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\StatusLog;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestManagementController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        $requests = DocumentRequest::with(['items.documentType', 'paymentProof', 'appointment.timeSlot', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalRequests = DocumentRequest::count();
        $pendingCount = DocumentRequest::whereIn('status', ['pending', 'payment_method_set', 'payment_uploaded', 'payment_rejected'])->count();
        $readyCount = DocumentRequest::where('status', 'ready_for_pickup')->count();
        $receivedCount = DocumentRequest::where('status', 'received')->count();
        $cancelledCount = DocumentRequest::where('status', 'cancelled')->count();

        return view('registrar.requests.index', compact(
            'requests', 'totalRequests', 'pendingCount', 'readyCount', 'receivedCount', 'cancelledCount'
        ));
    }

    public function show($id)
    {
        $request = DocumentRequest::with(['items.documentType', 'paymentProof', 'appointment.timeSlot', 'user'])
            ->findOrFail($id);

        return view('registrar.requests.show', compact('request'));
    }

    public function updateStatus(Request $request, $id)
    {
        $docRequest = DocumentRequest::findOrFail($id);
        $oldStatus = $docRequest->status;
        $newStatus = $request->input('status');

        // Define allowed status transitions
        $allowedTransitions = [
            'pending' => ['ready_for_pickup', 'completed', 'cancelled'],
            'payment_method_set' => ['ready_for_pickup', 'completed', 'cancelled'],
            'payment_uploaded' => ['ready_for_pickup', 'completed', 'cancelled'],
            'payment_rejected' => ['ready_for_pickup', 'completed', 'cancelled'],
            'payment_verified' => ['ready_for_pickup', 'completed', 'cancelled'],
            'ready_for_pickup' => ['received', 'completed', 'cancelled'],
            'received' => [],
            'completed' => [],
            'cancelled' => [],
        ];

        // Check if transition is allowed
        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            // Send notification to registrar instead of error banner
            $message = "❌ Cannot change status from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'. Invalid status transition.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.index'));
            session()->flash('check_notifications', true);
            
            return redirect()->route('registrar.requests.index');
        }

        // Generate claiming number when status becomes ready_for_pickup
        $claimingNumber = null;
        if ($newStatus === 'ready_for_pickup') {
            if (empty($docRequest->claiming_number)) {
                $claimingNumber = 'CLM-' . strtoupper(substr(uniqid(), 0, 6));
                $docRequest->claiming_number = $claimingNumber;
            } else {
                $claimingNumber = $docRequest->claiming_number;
            }
        }

        // Auto-mark payment if completed
        if ($newStatus === 'completed') {
            $docRequest->payment_status = 'paid';
            $docRequest->paid_at = $docRequest->paid_at ?? now();
        }

        $docRequest->status = $newStatus;
        $docRequest->save();

        // Log status change
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => "Status updated by registrar.",
        ]);

        // Send notification to STUDENT
        $student = $docRequest->user;
        if ($student) {
            $message = $this->getStudentStatusMessage($newStatus, $docRequest->reference_number, $claimingNumber);
            $url = route('student.requests.history');
            $this->sendNotification($student, $message, $url);
        }

        // Send notification to REGISTRAR (self)
        $registrarMessage = "✅ Request {$docRequest->reference_number} status updated from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'.";
        $this->sendNotificationToCurrentUser($registrarMessage, route('registrar.requests.show', $docRequest->id));
        
        // Set session flag for auto-open notification
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.requests.index');
    }

    public function markReceived($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->status !== 'ready_for_pickup') {
            // Send notification instead of error banner
            $message = "❌ Cannot mark as received. Request must be 'Ready for Pickup' first.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.index'));
            session()->flash('check_notifications', true);
            
            return redirect()->route('registrar.requests.index');
        }

        $docRequest->status = 'received';
        $docRequest->payment_status = 'paid';
        $docRequest->paid_at = $docRequest->paid_at ?? now();
        $docRequest->save();

        // Update appointment if exists
        if ($docRequest->appointment) {
            $docRequest->appointment->update([
                'status' => 'completed',
                'claimed_at' => now(),
            ]);
        }

        // Log status change
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => 'ready_for_pickup',
            'new_status' => 'received',
            'notes' => "Documents received by student. Claiming number verified.",
        ]);

        // Send notification to STUDENT
        $student = $docRequest->user;
        if ($student) {
            $message = "✅ Your documents for request {$docRequest->reference_number} have been received. Thank you for using CCST DocRequest!";
            $url = route('student.requests.history');
            $this->sendNotification($student, $message, $url);
        }

        // Send notification to REGISTRAR
        $registrarMessage = "✅ Request {$docRequest->reference_number} has been marked as RECEIVED. Student picked up their documents.";
        $this->sendNotificationToCurrentUser($registrarMessage, route('registrar.requests.show', $docRequest->id));
        
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.requests.index');
    }

    public function markAsPaid(Request $request, $id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->payment_status === 'paid') {
            $message = "ℹ️ Request {$docRequest->reference_number} is already marked as paid.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $docRequest->id));
            session()->flash('check_notifications', true);
            return redirect()->back();
        }

        $receiptNumber = $request->input('receipt_number');
        $cashierName = $request->input('cashier_name');

        $docRequest->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'receipt_number' => $receiptNumber,
            'cashier_name'   => $cashierName,
        ]);

        // Log it
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => Auth::id(),
            'old_status'          => $docRequest->status,
            'new_status'          => $docRequest->status,
            'notes'               => "Payment marked as PAID by registrar. Receipt: {$receiptNumber}",
        ]);

        // Notify student
        $student = $docRequest->user;
        if ($student) {
            $message = "💰 Payment for your request {$docRequest->reference_number} has been confirmed. Receipt No: {$receiptNumber}.";
            $url = route('student.requests.history');
            $this->sendNotification($student, $message, $url);
        }

        // Notify registrar
        $this->sendNotificationToCurrentUser(
            "✅ Request {$docRequest->reference_number} marked as PAID. Receipt: {$receiptNumber}",
            route('registrar.requests.show', $docRequest->id)
        );
        session()->flash('check_notifications', true);

        return redirect()->back()->with('success', 'Payment marked as paid successfully.');
    }

    public function serveProof($id)
    {
        $request = DocumentRequest::findOrFail($id);
        
        if (!$request->paymentProof || !$request->paymentProof->file_path) {
            abort(404);
        }

        $path = storage_path('app/private/' . $request->paymentProof->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);
        
        return response()->file($path, ['Content-Type' => $mime]);
    }

    private function getStudentStatusMessage($status, $referenceNumber, $claimingNumber = null)
    {
        return match($status) {
            'processing' => "📋 Your request {$referenceNumber} is now being processed. We'll notify you when it's ready for pickup.",
            'ready_for_pickup' => "📦 Your request {$referenceNumber} is ready for pickup! Your claiming number is: {$claimingNumber}. Please bring this when claiming your documents.",
            'received' => "✅ Thank you for picking up your documents for request {$referenceNumber}. Have a great day!",
            'cancelled' => "❌ Your request {$referenceNumber} has been cancelled. Please contact the registrar for more information.",
            default => "Your request {$referenceNumber} status has been updated to: " . ucfirst(str_replace('_', ' ', $status)),
        };
    }
}