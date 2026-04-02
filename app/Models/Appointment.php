<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'document_request_id',
        'user_id',
        'time_slot_id',
        'appointment_date',
        'status',
        'claimed_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',      // Returns a Carbon date object
        'claimed_at'       => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * The document request this appointment is for.
     * Usage: $appointment->documentRequest
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * The student who booked this appointment.
     * Usage: $appointment->student->name
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The time slot chosen for this appointment.
     * Usage: $appointment->timeSlot->label  →  "8:00 AM – 9:00 AM"
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
