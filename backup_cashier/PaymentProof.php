<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'document_request_id',
        'file_path',
        'original_filename',
        'file_size_kb',
        'amount_declared',
        'reference_number',
        'is_resubmission',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount_declared'  => 'decimal:2',
        'is_resubmission'  => 'boolean',
        'verified_at'      => 'datetime',  // Returns a Carbon object for date formatting
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * The document request this proof belongs to.
     * Usage: $proof->documentRequest
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * The cashier who verified or rejected this proof.
     * Usage: $proof->verifiedBy->name
     * Returns null if cashier has not acted yet.
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // -------------------------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------------------------

    /**
     * Whether the cashier has already acted on this proof (verified or rejected).
     * If true, the student can no longer re-upload.
     * Usage: if ($proof->isFinal()) { ... }
     */
    public function isFinal(): bool
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Check if the declared amount differs from the total_fee by more than ₱1.
     * The cashier sees a yellow warning box when this returns true.
     * Usage: if ($proof->hasAmountMismatch()) { ... }
     */
    public function hasAmountMismatch(): bool
    {
        if (is_null($this->amount_declared)) {
            return false;
        }

        return abs($this->amount_declared - $this->documentRequest->total_fee) > 1;
    }
}
