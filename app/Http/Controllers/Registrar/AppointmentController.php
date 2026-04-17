<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        $upcomingAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('time_slot_id', 'asc')
            ->get();

        $todayAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->whereDate('appointment_date', today())
            ->orderBy('time_slot_id', 'asc')
            ->get();

        $allAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time', 'asc')->get();

        $upcomingCount = $upcomingAppointments->count();
        $todayCount = $todayAppointments->count();

        return view('registrar.appointments.index', compact(
            'upcomingAppointments',
            'todayAppointments',
            'allAppointments',
            'timeSlots',
            'upcomingCount',
            'todayCount'
        ));
    }

    public function complete($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'completed']);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Appointment marked as completed.');
    }

    public function missed($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'missed']);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Appointment marked as missed.');
    }

    public function storeSlot(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        TimeSlot::create([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot added successfully.');
    }

    public function updateSlot(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $slot->update([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot updated successfully.');
    }

    public function toggleSlot($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->update(['is_active' => !$slot->is_active]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot status toggled.');
    }

    public function getSlotData($id)
    {
        $slot = TimeSlot::findOrFail($id);
        return response()->json([
            'label' => $slot->label,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'max_capacity' => $slot->max_capacity,
            'is_active' => $slot->is_active,
        ]);
    }
}