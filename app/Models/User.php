<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'password',
        'role',
        'student_number',
        'contact_number',
        'address',
        'strand',
        'grade_level',
        'section',
        'profile_photo',
        'student_id_photo',
        'is_verified',
        'verified_at',
        'verified_by',
        'is_walk_in',
        'walk_in_registered_by',
        'walk_in_registered_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_walk_in' => 'boolean',
            'verified_at' => 'datetime',
            'walk_in_registered_at' => 'datetime',
        ];
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name . ' ' : ' ';
        return $this->first_name . $middle . $this->last_name;
    }

    /**
     * Get the user's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Auto-generate name field from first and last name
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            $middle = $user->middle_name ? ' ' . $user->middle_name . ' ' : ' ';
            $user->name = $user->first_name . $middle . $user->last_name;
        });
        
        static::updating(function ($user) {
            $middle = $user->middle_name ? ' ' . $user->middle_name . ' ' : ' ';
            $user->name = $user->first_name . $middle . $user->last_name;
        });
    }

    // Relationships
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function walkInRegisteredBy()
    {
        return $this->belongsTo(User::class, 'walk_in_registered_by');
    }

    // Role checks
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isRegistrar(): bool
    {
        return $this->role === 'registrar';
    }

    // Verification helpers
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function markAsVerified($registrarId = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $registrarId ?? auth()->id(),
        ]);
    }
}