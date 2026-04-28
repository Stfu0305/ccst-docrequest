<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'type',
        'subject',
        'body',
        'updated_by',
    ];

    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Static helper methods
    public static function getTemplate($type)
    {
        return static::where('type', $type)->first();
    }

    public static function getSubject($type, $default = null)
    {
        $template = static::getTemplate($type);
        return $template ? $template->subject : $default;
    }

    public static function getBody($type, $default = null)
    {
        $template = static::getTemplate($type);
        return $template ? $template->body : $default;
    }
}