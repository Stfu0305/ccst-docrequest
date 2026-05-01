<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentProof;

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
        'payment_status',
        'payment_method',
        'claiming_number',
        'appointment_id',
        'processed_by',
        'remarks',
        'paid_at',
        'receipt_number',
        'cashier_name',
        'is_walk_in',
        'walk_in_handled_by',
        'is_printable',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'is_walk_in' => 'boolean',
        'is_printable' => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(DocumentRequestItem::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany();
    }

    public function paymentProofs()
    {
        return $this->hasMany(PaymentProof::class);
    }


    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function walkInHandledBy()
    {
        return $this->belongsTo(User::class, 'walk_in_handled_by');
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReadyForPickup($query)
    {
        return $query->where('status', self::STATUS_READY_FOR_PICKUP);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_UNPAID);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReadyForPickup(): bool
    {
        return $this->status === self::STATUS_READY_FOR_PICKUP;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === self::PAYMENT_UNPAID;
    }

    public function isPrintable(): bool
    {
        return $this->is_printable;
    }

    public function isWalkIn(): bool
    {
        return $this->is_walk_in;
    }

    public function markAsPaid($receiptNumber = null, $cashierName = null)
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'paid_at' => now(),
            'receipt_number' => $receiptNumber,
            'cashier_name' => $cashierName,
        ]);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function generateClaimingNumber()
    {
        $claimingNumber = 'CLM-' . strtoupper(substr(uniqid(), 0, 6));
        $this->update(['claiming_number' => $claimingNumber]);
        return $claimingNumber;
    }

    /**
     * Log status change
     */
    public function logStatusChange($oldStatus, $newStatus, $notes = null)
    {
        return StatusLog::create([
            'document_request_id' => $this->id,
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ]);
    }
}