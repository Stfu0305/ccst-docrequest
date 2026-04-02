<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * ORDER MATTERS:
     * - Users must come before anything that references user IDs (announcements, settings)
     * - DocumentTypes must come before DocumentRequestItems
     * - TimeSlots must come before Appointments
     * - Announcements and PaymentSettings reference users (published_by, updated_by)
     *   but those are nullable, so they can run anytime after users.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DocumentTypeSeeder::class,
            TimeSlotSeeder::class,
            PaymentSettingSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
