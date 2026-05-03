<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrarManagementController extends Controller
{
    public function __construct()
    {
        // Only allow admin users to access this controller
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Unauthorized access. Only administrators can manage registrar accounts.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $registrars = User::where('role', 'registrar')->get();
        return view('registrar.manage.index', compact('registrars'));
    }

    public function create()
    {
        return view('registrar.manage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Split name into parts to handle first_name and last_name requirements
        $nameParts = explode(' ', trim($validated['name']));
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? end($nameParts) : '';

        User::create([
            'name' => $validated['name'],
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'registrar',
            'is_verified' => true,
            'is_active' => true,
            'is_admin' => false,
        ]);

        return redirect()->route('registrar.manage.index')->with('success', 'Registrar account created successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        if ($user->role !== 'registrar') {
            return back()->with('error', 'You can only delete registrar accounts.');
        }

        $user->delete();
        return back()->with('success', 'Registrar account removed successfully.');
    }
}
