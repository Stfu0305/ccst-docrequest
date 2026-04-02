<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Delete only the 3 test accounts (safe to re-run without wiping real users)
        User::whereIn('email', [
            'student@ccst.edu.ph',
            'registrar@ccst.edu.ph',
            'cashier@ccst.edu.ph',
        ])->delete();

        // Test student account — has all student-specific fields filled in
        User::create([
            'name'           => 'Juan dela Cruz',
            'email'          => 'student@ccst.edu.ph',
            'password'       => Hash::make('password'),
            'role'           => 'student',
            'student_number' => '2024-00001',
            'contact_number' => '09171234567',
            'address'        => 'Dau, Mabalacat City, Pampanga',
            'strand'         => 'ICT',
            'grade_level'    => 'Grade 12',
            'section'        => 'Diligence',
        ]);

        // Test registrar account
        User::create([
            'name'           => 'Maria Santos',
            'email'          => 'registrar@ccst.edu.ph',
            'password'       => Hash::make('password'),
            'role'           => 'registrar',
            'contact_number' => '09181234567',
        ]);

        // Test cashier account
        User::create([
            'name'           => 'Pedro Reyes',
            'email'          => 'cashier@ccst.edu.ph',
            'password'       => Hash::make('password'),
            'role'           => 'cashier',
            'contact_number' => '09191234567',
        ]);
    }
}
