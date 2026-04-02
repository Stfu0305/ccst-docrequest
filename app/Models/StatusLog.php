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

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * Which document request this log entry belongs to.
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Who triggered this status change (student, registrar, or cashier).
     * Usage: $log->changedBy->name
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
