<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DocumentTypeSeeder::class,
            TimeSlotSeeder::class,
            // PaymentSettingSeeder::class, // REMOVED - table no longer exists
            AnnouncementSeeder::class,
        ]);
    }
}