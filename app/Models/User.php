<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * $fillable lists every column we allow to be mass-assigned.
     * Mass assignment is when you do User::create($request->all()) — Laravel blocks
     * any column NOT listed here, protecting against users injecting extra fields.
     */
    protected $fillable = [
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

    /**
     * $hidden hides these columns when you convert a User to JSON or an array.
     * This prevents passwords and tokens from leaking into API responses or logs.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * $casts tells Laravel how to automatically convert column values.
     * 'email_verified_at' becomes a Carbon date object (so you can do ->format('Y-m-d')).
     * 'password' => 'hashed' means Laravel auto-hashes when you set the password.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------------------------

    /**
     * A student user can have many document requests.
     * Usage: $user->documentRequests — returns a collection of DocumentRequest models.
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * A student user can have many appointments.
     * Usage: $user->appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // -------------------------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------------------------

    /**
     * Quick role-check helpers — use these in Blade and controllers.
     * Example in Blade: @if(auth()->user()->isStudent())
     */
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
