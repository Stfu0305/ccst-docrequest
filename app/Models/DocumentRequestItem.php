<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequestItem extends Model
{
    protected $fillable = [
        'document_request_id',
        'document_type_id',
        'copies',
        'assessment_year',
        'semester',
        'fee',
    ];

    protected $casts = [
        'fee'    => 'decimal:2',
        'copies' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * Which document request this item belongs to.
     * Usage: $item->documentRequest
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Which document type this item is (e.g. COE, TOR).
     * Usage: $item->documentType->name  →  "Certificate of Enrollment"
     * Usage: $item->documentType->code  →  "COE"
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    // -------------------------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------------------------

    /**
     * Subtotal for this line item: fee × copies.
     * Usage: $item->subtotal()  →  e.g. 150.00 for 2 copies at ₱75 each
     */
    public function subtotal(): float
    {
        return $this->fee * $this->copies;
    }
}
