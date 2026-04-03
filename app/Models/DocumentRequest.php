<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'reference_number',
        'user_id',
        'student_number',
        'full_name',
        'contact_number',
        'course_program',
        'year_level',
        'section',
        'total_fee',
        'status',
        'payment_method',
        'claiming_number',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * Get the user (student) who owns this document request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the items for this document request.
     */
    public function items()
    {
        return $this->hasMany(DocumentRequestItem::class);
    }

    /**
     * Get the payment proof for this document request.
     */
    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class);
    }

    /**
     * Get the official receipt for this document request.
     */
    public function officialReceipt()
    {
        return $this->hasOne(OfficialReceipt::class);
    }

    /**
     * Get the appointment for this document request.
     */
    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    /**
     * Get the status logs for this document request.
     */
    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }
}