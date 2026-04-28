<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Student
        User::updateOrCreate(
            ['email' => 'student@ccst.edu.ph'],
            [
                'first_name' => 'Juan',
                'middle_name' => null,
                'last_name' => 'Dela Cruz',
                'name' => 'Juan Dela Cruz',
                'email' => 'student@ccst.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'student',
                'student_number' => '2024-0001',
                'contact_number' => '09171234567',
                'address' => 'Dau, Mabalacat City, Pampanga',
                'strand' => 'ICT',
                'grade_level' => 'Grade 12',
                'section' => 'Diligence',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // Registrar
        User::updateOrCreate(
            ['email' => 'registrar@ccst.edu.ph'],
            [
                'first_name' => 'Maria',
                'middle_name' => null,
                'last_name' => 'Santos',
                'name' => 'Maria Santos',
                'email' => 'registrar@ccst.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'registrar',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // Note: Cashier account is REMOVED
    }
}