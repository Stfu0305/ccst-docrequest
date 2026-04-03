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
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'       => ['required', 'string', 'max:100'],
            'middle_name'      => ['nullable', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'student_number'   => ['required', 'string', 'max:50', 'unique:users,student_number'],
            'contact_number'   => ['required', 'string', 'max:20'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'strand'           => ['required', 'string', 'in:ABM,ICT,HUMSS,STEM,GAS,HE'],
            'grade_level'      => ['required', 'string', 'in:Grade 11,Grade 12'],
            'section'          => ['required', 'string', 'max:50'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate name field from first and last name
        $middleName = $request->middle_name ? ' ' . $request->middle_name . ' ' : ' ';
        $fullName = $request->first_name . $middleName . $request->last_name;

        $user = User::create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'name'           => $fullName,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => 'student',
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