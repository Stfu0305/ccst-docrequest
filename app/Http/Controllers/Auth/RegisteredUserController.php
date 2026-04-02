<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * All new self-registrations are students — role is forced to 'student'.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'student_number'   => ['required', 'string', 'max:50', 'unique:users,student_number'],
            'contact_number'   => ['required', 'string', 'max:20'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'strand'           => ['required', 'string', 'in:ABM,ICT,HUMSS,STEM,GAS,HE'],
            'grade_level'      => ['required', 'string', 'in:Grade 11,Grade 12'],
            'section'          => ['required', 'string', 'max:50'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => 'student',   // all self-registrations are students
            'student_number' => $request->student_number,
            'contact_number' => $request->contact_number,
            'strand'         => $request->strand,
            'grade_level'    => $request->grade_level,
            'section'        => $request->section,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }
}