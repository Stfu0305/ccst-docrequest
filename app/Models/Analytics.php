<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = [
        'date',
        'total_requests',
        'total_appointments',
        'total_completed',
        'total_cancelled',
        'peak_hour',
        'peak_day',
        'document_type_counts',
        'average_processing_time',
    ];

    protected $casts = [
        'date' => 'date',
        'document_type_counts' => 'array',
        'average_processing_time' => 'decimal:2',
    ];

    // Static helper methods
    public static function getToday()
    {
        return static::where('date', today())->first();
    }

    public static function getStatsForDate($date)
    {
        return static::where('date', $date)->first();
    }
}