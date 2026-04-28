<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'method',
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'extra_info',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * The cashier who last updated this setting.
     * Usage: $setting->updatedBy->name
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -------------------------------------------------------------------------
    // STATIC HELPERS
    // -------------------------------------------------------------------------

    /**
     * Get all active payment settings, keyed by method name.
     * Used on the student's Request Summary page to show payment details.
     *
     * Usage: $settings = PaymentSetting::activeSettings();
     *        $settings['gcash']->account_number
     */
    public static function activeSettings(): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)->get()->keyBy('method');
    }

    /**
     * Get ALL settings keyed by method — used on the cashier Settings page.
     * Includes inactive methods so the cashier can re-activate them.
     */
    public static function allSettings(): \Illuminate\Support\Collection
    {
        return static::all()->keyBy('method');
    }
}
