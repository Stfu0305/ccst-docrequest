<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TimeSlot::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slots = [
            ['label' => '8:00 AM – 9:00 AM',   'start_time' => '08:00:00', 'end_time' => '09:00:00'],
            ['label' => '9:00 AM – 10:00 AM',  'start_time' => '09:00:00', 'end_time' => '10:00:00'],
            ['label' => '10:00 AM – 11:00 AM', 'start_time' => '10:00:00', 'end_time' => '11:00:00'],
            ['label' => '1:00 PM – 2:00 PM',   'start_time' => '13:00:00', 'end_time' => '14:00:00'],
            ['label' => '2:00 PM – 3:00 PM',   'start_time' => '14:00:00', 'end_time' => '15:00:00'],
            ['label' => '3:00 PM – 4:00 PM',   'start_time' => '15:00:00', 'end_time' => '16:00:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::create([
                'label'        => $slot['label'],
                'start_time'   => $slot['start_time'],
                'end_time'     => $slot['end_time'],
                'max_capacity' => 5,    // max 5 students per slot per day
                'is_active'    => true,
            ]);
        }
    }
}