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
        'first_name',      // Add this
        'middle_name',     // Add this
        'last_name',       // Add this
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

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isRegistrar(): bool
    {
        return $this->role === 'registrar';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }
}