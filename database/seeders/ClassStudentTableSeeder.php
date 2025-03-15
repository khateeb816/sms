<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClassStudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing class-student relationships
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('class_student')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get all classes
        $classes = ClassRoom::all();

        // Get all students
        $students = User::where('role', 4)->get();

        // Distribute students across classes
        foreach ($students as $student) {
            // Assign each student to 1-2 classes
            $numClasses = rand(1, 2);

            // Get random classes
            $randomClasses = $classes->random(min($numClasses, $classes->count()));

            foreach ($randomClasses as $class) {
                // Check if the class has reached capacity
                $currentStudentCount = DB::table('class_student')
                    ->where('class_id', $class->id)
                    ->count();

                if ($currentStudentCount < $class->capacity) {
                    // Add student to class if not already added
                    $exists = DB::table('class_student')
                        ->where('class_id', $class->id)
                        ->where('student_id', $student->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('class_student')->insert([
                            'class_id' => $class->id,
                            'student_id' => $student->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
