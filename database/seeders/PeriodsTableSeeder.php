<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing periods
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Period::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $periods = [
            [
                'name' => 'Period 1',
                'start_time' => '08:00:00',
                'end_time' => '08:45:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 2',
                'start_time' => '08:50:00',
                'end_time' => '09:35:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 3',
                'start_time' => '09:40:00',
                'end_time' => '10:25:00',
                'is_active' => true,
            ],
            [
                'name' => 'Break',
                'start_time' => '10:25:00',
                'end_time' => '10:45:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 4',
                'start_time' => '10:45:00',
                'end_time' => '11:30:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 5',
                'start_time' => '11:35:00',
                'end_time' => '12:20:00',
                'is_active' => true,
            ],
            [
                'name' => 'Lunch',
                'start_time' => '12:20:00',
                'end_time' => '13:05:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 6',
                'start_time' => '13:05:00',
                'end_time' => '13:50:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 7',
                'start_time' => '13:55:00',
                'end_time' => '14:40:00',
                'is_active' => true,
            ],
            [
                'name' => 'Period 8',
                'start_time' => '14:45:00',
                'end_time' => '15:30:00',
                'is_active' => true,
            ],
        ];

        foreach ($periods as $period) {
            // Calculate duration in minutes
            $startTime = Carbon::parse($period['start_time']);
            $endTime = Carbon::parse($period['end_time']);
            $period['duration'] = $endTime->diffInMinutes($startTime);

            Period::create($period);
        }
    }
}
