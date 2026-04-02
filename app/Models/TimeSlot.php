<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'label',
        'start_time',
        'end_time',
        'max_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'max_capacity' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * All appointments ever booked for this time slot (across all dates).
     * Usage: $slot->appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // -------------------------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------------------------

    /**
     * How many appointments are already booked for this slot on a specific date.
     * Used to check if a slot is full before allowing a new booking.
     *
     * Usage: $slot->bookedCount('2026-04-15')
     */
    public function bookedCount(string $date): int
    {
        return $this->appointments()
            ->where('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'completed']) // Don't count cancelled/missed
            ->count();
    }

    /**
     * Whether this slot still has room on a given date.
     * Usage: if ($slot->hasCapacity('2026-04-15')) { ... }
     */
    public function hasCapacity(string $date): bool
    {
        return $this->bookedCount($date) < $this->max_capacity;
    }
}
