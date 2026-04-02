<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'type',
        'content',
        'is_published',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * The registrar who last published this announcement.
     * Usage: $announcement->publishedBy->name
     */
    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    // -------------------------------------------------------------------------
    // STATIC HELPERS
    // -------------------------------------------------------------------------

    /**
     * Get the general announcement row.
     * There is exactly ONE row of each type — created by the seeder.
     * Usage: Announcement::general()
     */
    public static function general(): ?self
    {
        return static::where('type', 'announcement')->first();
    }

    /**
     * Get the transaction days announcement row.
     * Usage: Announcement::transactionDays()
     */
    public static function transactionDays(): ?self
    {
        return static::where('type', 'transaction_days')->first();
    }
}
