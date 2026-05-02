<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'fee',
        'processing_days',
        'has_school_year',
        'is_printable',
        'is_active',
        'template_path',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'has_school_year' => 'boolean',
        'is_printable' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function documentRequestItems()
    {
        return $this->hasMany(DocumentRequestItem::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrintable($query)
    {
        return $query->where('is_printable', true);
    }

    public function scopeNonPrintable($query)
    {
        return $query->where('is_printable', false);
    }

    // Helper methods
    public function isPrintable(): bool
    {
        return $this->is_printable;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasSchoolYear(): bool
    {
        return $this->has_school_year;
    }
}