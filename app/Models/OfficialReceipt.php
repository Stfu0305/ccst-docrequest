<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialReceipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'document_request_id',
        'payment_method',
        'amount_paid',
        'reference_number',
        'issued_by',
        'issued_at',
        'file_path',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'issued_at'   => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * The document request this receipt is for.
     * Usage: $receipt->documentRequest
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * The cashier who issued this receipt.
     * Usage: $receipt->issuedBy->name
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
