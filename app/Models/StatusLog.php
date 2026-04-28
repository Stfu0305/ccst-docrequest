<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    protected $fillable = [
        'document_request_id',
        'changed_by',
        'old_status',
        'new_status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}