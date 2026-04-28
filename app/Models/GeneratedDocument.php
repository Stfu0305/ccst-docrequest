<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    protected $fillable = [
        'document_request_id',
        'document_type_id',
        'file_path',
        'printed_at',
        'printed_by',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    // Relationships
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    // Helper methods
    public function wasPrinted(): bool
    {
        return !is_null($this->printed_at);
    }
}