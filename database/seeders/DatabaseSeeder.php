<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            PeriodsTableSeeder::class,
            ClassRoomsTableSeeder::class,
            ClassStudentTableSeeder::class,
            // Call the school data seeder with attendance, timetables, exams, and results
            SchoolDataSeeder::class,
        ]);
    }
}
