<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'fee',
        'has_school_year',
        'processing_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'fee'             => 'decimal:2', // Always 2 decimal places (e.g. 80.00)
        'has_school_year' => 'boolean',   // Returns true/false, not 1/0
        'is_active'       => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * A document type can appear in many request item rows.
     * Usage: $documentType->requestItems
     */
    public function requestItems()
    {
        return $this->hasMany(DocumentRequestItem::class);
    }
}
