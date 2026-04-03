<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SplitUserNamesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $nameParts = explode(' ', $user->name);
            
            // Assume first part is first name, last part is last name
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            
            // Middle name is everything in between (if exists)
            $middleName = null;
            if (count($nameParts) > 2) {
                $middleName = implode(' ', array_slice($nameParts, 1, -1));
            }
            
            $user->update([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
            ]);
            
            $this->command->info("Updated user: {$user->name} → {$firstName} {$middleName} {$lastName}");
        }
    }
}