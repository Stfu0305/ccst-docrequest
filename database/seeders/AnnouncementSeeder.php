<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Creates exactly 2 rows — one of each type.
        // The registrar edits these from the dashboard. They are never created again.
        // updateOrCreate prevents duplicate rows if seeder is re-run.

        Announcement::updateOrCreate(
            ['type' => 'announcement'],
            [
                'content'      => 'No announcement currently published.',
                'is_published' => false,
            ]
        );

        Announcement::updateOrCreate(
            ['type' => 'transaction_days'],
            [
                'content'      => 'No transaction day changes at this time.',
                'is_published' => false,
            ]
        );
    }
}
