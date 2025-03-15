<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClassRoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing classes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ClassRoom::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get teacher IDs
        $teachers = User::where('role', 2)->pluck('id')->toArray();

        $classes = [
            [
                'name' => 'Class 1A',
                'grade_year' => 'Grade 1',
                'capacity' => 25,
                'room_number' => '101',
                'description' => 'Primary first grade class',
                'is_active' => true,
            ],
            [
                'name' => 'Class 2B',
                'grade_year' => 'Grade 2',
                'capacity' => 30,
                'room_number' => '102',
                'description' => 'Primary second grade class',
                'is_active' => true,
            ],
            [
                'name' => 'Class 3C',
                'grade_year' => 'Grade 3',
                'capacity' => 28,
                'room_number' => '103',
                'description' => 'Primary third grade class',
                'is_active' => true,
            ],
            [
                'name' => 'Class 4D',
                'grade_year' => 'Grade 4',
                'capacity' => 30,
                'room_number' => '201',
                'description' => 'Primary fourth grade class',
                'is_active' => true,
            ],
            [
                'name' => 'Class 5E',
                'grade_year' => 'Grade 5',
                'capacity' => 25,
                'room_number' => '202',
                'description' => 'Primary fifth grade class',
                'is_active' => true,
            ],
            [
                'name' => 'Class 6F',
                'grade_year' => 'Grade 6',
                'capacity' => 30,
                'room_number' => '203',
                'description' => 'Middle school sixth grade class',
                'is_active' => true,
            ],
        ];

        foreach ($classes as $index => $class) {
            // Assign a teacher if available
            if (isset($teachers[$index % count($teachers)])) {
                $class['teacher_id'] = $teachers[$index % count($teachers)];
            }

            ClassRoom::create($class);
        }
    }
}
