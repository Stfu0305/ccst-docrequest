<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Traits\SendsDatabaseNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Display the calendar page
     */
    public function index()
    {
        $timeSlots = TimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return view('registrar.calendar.index', compact('timeSlots'));
    }

    /**
     * Get appointments as JSON for FullCalendar
     */
    public function getAppointments(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $appointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->whereBetween('appointment_date', [$start, $end])
            ->get()
            ->map(function ($appointment) {
                // Determine color based on status
                $color = match($appointment->status) {
                    'scheduled' => '#F5C518',  // Yellow
                    'completed' => '#1B6B3A',  // Green
                    'missed' => '#DC3545',      // Red
                    'cancelled' => '#AAAAAA',   // Gray
                    default => '#1A9FE0',       // Blue
                };

                $textColor = in_array($appointment->status, ['scheduled', 'completed']) ? '#FFFFFF' : '#1A1A1A';

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->student->full_name ?? 'Unknown Student',
                    'start' => $appointment->appointment_date . ' ' . $appointment->timeSlot->start_time,
                    'end' => $appointment->appointment_date . ' ' . $appointment->timeSlot->end_time,
                    'color' => $color,
                    'textColor' => $textColor,
                    'extendedProps' => [
                        'status' => $appointment->status,
                        'reference_number' => $appointment->documentRequest->reference_number ?? 'N/A',
                        'student_number' => $appointment->student->student_number ?? 'N/A',
                        'amount' => $appointment->documentRequest->total_fee ?? 0,
                        'time_slot_label' => $appointment->timeSlot->label ?? 'N/A',
                    ],
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Get time slots for the sidebar
     */
    public function getTimeSlots()
    {
        $timeSlots = TimeSlot::orderBy('start_time')->get();
        
        return response()->json($timeSlots);
    }

    /**
     * Reschedule an appointment via drag & drop
     */
    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        $newDate = Carbon::parse($request->new_date);
        $newTimeSlotId = $request->time_slot_id;
        
        // Check if the new time slot has capacity
        $timeSlot = TimeSlot::findOrFail($newTimeSlotId);
        $bookedCount = Appointment::where('time_slot_id', $newTimeSlotId)
            ->where('appointment_date', $newDate->format('Y-m-d'))
            ->where('id', '!=', $id)
            ->count();
        
        if ($bookedCount >= $timeSlot->max_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is fully booked. Please choose another slot.'
            ], 400);
        }
        
        $oldDate = $appointment->appointment_date;
        $oldTimeSlot = $appointment->timeSlot;
        
        $appointment->update([
            'appointment_date' => $newDate->format('Y-m-d'),
            'time_slot_id' => $newTimeSlotId,
        ]);
        
        // Send notification to student
        $student = $appointment->student;
        $message = "📅 Your appointment has been rescheduled from " . 
                   Carbon::parse($oldDate)->format('F d, Y') . " at " . $oldTimeSlot->label .
                   " to " . $newDate->format('F d, Y') . " at " . $timeSlot->label;
        $url = route('student.requests.history');
        
        $this->sendNotification($student, $message, $url);
        
        // Send notification to registrar
        $this->sendNotificationToCurrentUser(
            "✅ Appointment #{$appointment->id} has been rescheduled.",
            route('registrar.calendar')
        );
        
        session()->flash('check_notifications', true);
        
        return response()->json(['success' => true]);
    }
    /**
     * Store a new time slot
     */
    public function storeTimeSlot(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
        ]);

        $timeSlot = TimeSlot::create([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
            'is_active' => true,
        ]);

        // Send notification to registrar
        $this->sendNotificationToCurrentUser(
            "✅ New time slot '{$timeSlot->label}' has been added.",
            route('registrar.calendar')
        );
        session()->flash('check_notifications', true);

        return response()->json([
            'success' => true,
            'message' => 'Time slot added successfully.',
            'time_slot' => $timeSlot
        ]);
    }

    /**
     * Update an existing time slot
     */
    public function updateTimeSlot(Request $request, $id)
    {
        $timeSlot = TimeSlot::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
        ]);

        $timeSlot->update([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
        ]);

        $this->sendNotificationToCurrentUser(
            "✅ Time slot '{$timeSlot->label}' has been updated.",
            route('registrar.calendar')
        );
        session()->flash('check_notifications', true);

        return response()->json([
            'success' => true,
            'message' => 'Time slot updated successfully.',
            'time_slot' => $timeSlot
        ]);
    }

    /**
     * Delete a time slot
     */
    public function deleteTimeSlot($id)
    {
        $timeSlot = TimeSlot::findOrFail($id);
        
        // Check if there are appointments using this time slot
        $appointmentsCount = Appointment::where('time_slot_id', $id)->count();
        
        if ($appointmentsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete this time slot. It has {$appointmentsCount} existing appointment(s). Deactivate it instead."
            ], 400);
        }
        
        $label = $timeSlot->label;
        $timeSlot->delete();

        $this->sendNotificationToCurrentUser(
            "❌ Time slot '{$label}' has been deleted.",
            route('registrar.calendar')
        );
        session()->flash('check_notifications', true);

        return response()->json([
            'success' => true,
            'message' => 'Time slot deleted successfully.'
        ]);
    }

    /**
     * Toggle time slot active status
     */
    public function toggleTimeSlot($id)
    {
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->update(['is_active' => !$timeSlot->is_active]);
        
        $status = $timeSlot->is_active ? 'activated' : 'deactivated';
        
        $this->sendNotificationToCurrentUser(
            "✅ Time slot '{$timeSlot->label}' has been {$status}.",
            route('registrar.calendar')
        );
        session()->flash('check_notifications', true);

        return response()->json([
            'success' => true,
            'message' => "Time slot {$status} successfully.",
            'is_active' => $timeSlot->is_active
        ]);
    }

    public function getSlotData($id)
    {
        $slot = TimeSlot::findOrFail($id);
        return response()->json($slot);
    }

}