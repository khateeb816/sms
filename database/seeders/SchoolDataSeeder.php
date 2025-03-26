<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get existing records
        $students = User::where('role', 4)->get(); // Student role
        $teachers = User::where('role', 2)->get(); // Teacher role
        $classes = ClassRoom::all();
        $periods = Period::all();

        if($students->isEmpty() || $teachers->isEmpty() || $classes->isEmpty() || $periods->isEmpty()) {
            $this->command->error('Please run the basic seeders first to create users, classes, and periods.');
            return;
        }

        $this->seedAttendance($faker, $students, $teachers);
        $this->seedTimetables($faker, $classes, $teachers, $periods);
        $this->seedExamsAndResults($faker, $classes, $teachers, $students);
    }

    /**
     * Seed attendance records for students and teachers
     */
    private function seedAttendance($faker, $students, $teachers)
    {
        $this->command->info('Seeding attendances...');

        // Get last 30 days
        $days = [];
        for ($i = 0; $i < 30; $i++) {
            $days[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }

        // Student attendances
        $studentAttendances = [];
        foreach ($students as $student) {
            foreach ($days as $day) {
                // Skip weekends (Saturday and Sunday)
                $dayOfWeek = Carbon::parse($day)->dayOfWeek;
                if ($dayOfWeek == 6 || $dayOfWeek == 0) {
                    continue;
                }

                $studentAttendances[] = [
                    'user_id' => $student->id,
                    'date' => $day,
                    'status' => $faker->randomElement(['present', 'present', 'present', 'present', 'absent', 'late', 'leave']), // More weight to present
                    'attendee_type' => 'student',
                    'remarks' => $faker->optional(0.2)->sentence(), // 20% chance of having remarks
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Teacher attendances
        $teacherAttendances = [];
        foreach ($teachers as $teacher) {
            foreach ($days as $day) {
                // Skip weekends (Saturday and Sunday)
                $dayOfWeek = Carbon::parse($day)->dayOfWeek;
                if ($dayOfWeek == 6 || $dayOfWeek == 0) {
                    continue;
                }

                $teacherAttendances[] = [
                    'user_id' => $teacher->id,
                    'date' => $day,
                    'status' => $faker->randomElement(['present', 'present', 'present', 'present', 'present', 'absent', 'leave']), // More weight to present for teachers
                    'attendee_type' => 'teacher',
                    'remarks' => $faker->optional(0.1)->sentence(), // 10% chance of having remarks
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        collect($studentAttendances)->chunk(500)->each(function ($chunk) {
            DB::table('attendances')->insert($chunk->toArray());
        });

        collect($teacherAttendances)->chunk(500)->each(function ($chunk) {
            DB::table('attendances')->insert($chunk->toArray());
        });

        $this->command->info('Attendance records seeded successfully!');
    }

    /**
     * Seed timetable records for classes
     */
    private function seedTimetables($faker, $classes, $teachers, $periods)
    {
        $this->command->info('Seeding timetables...');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $subjects = [
            'Mathematics', 'Science', 'English', 'History', 'Geography', 'Physics',
            'Chemistry', 'Biology', 'Computer Science', 'Art', 'Music', 'Physical Education',
            'Social Studies', 'Economics', 'Business Studies', 'Accounting', 'Statistics'
        ];

        $timetables = [];

        foreach ($classes as $class) {
            foreach ($days as $day) {
                // Assign a break period (usually mid-day)
                $breakPeriod = $faker->numberBetween(3, 5); // Break during 3rd to 5th period

                foreach ($periods as $index => $period) {
                    $isBreak = ($index + 1) == $breakPeriod;

                    if ($isBreak) {
                        $timetables[] = [
                            'class_id' => $class->id,
                            'day_of_week' => $day,
                            'period_id' => $period->id,
                            'teacher_id' => null,
                            'subject' => 'Break',
                            'start_time' => $period->start_time,
                            'end_time' => $period->end_time,
                            'notes' => 'Lunch/Recess Break',
                            'is_break' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        $timetables[] = [
                            'class_id' => $class->id,
                            'day_of_week' => $day,
                            'period_id' => $period->id,
                            'teacher_id' => $teachers->random()->id,
                            'subject' => $faker->randomElement($subjects),
                            'start_time' => $period->start_time,
                            'end_time' => $period->end_time,
                            'notes' => $faker->optional(0.3)->sentence(),
                            'is_break' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Insert in chunks to avoid memory issues
        collect($timetables)->chunk(500)->each(function ($chunk) {
            DB::table('timetables')->insert($chunk->toArray());
        });

        $this->command->info('Timetables seeded successfully!');
    }

    /**
     * Seed exams, datesheets, and exam results
     */
    private function seedExamsAndResults($faker, $classes, $teachers, $students)
    {
        $this->command->info('Seeding exams, datesheets, and results...');

        $terms = ['first', 'second', 'third', 'final'];
        $examTypes = ['first_term', 'second_term', 'third_term', 'final_term'];

        // Seed for each class
        foreach ($classes as $class) {
            $classStudents = $class->students; // Get students in the class

            if ($classStudents->isEmpty()) {
                $this->command->warn("No students found for class {$class->name}. Skipping...");
                continue;
            }

            // Create 4 datesheets (one for each term)
            for ($term = 0; $term < 4; $term++) {
                $currentTerm = $terms[$term];
                $currentExamType = $examTypes[$term];

                // Calculate term start and end dates
                $startMonth = 1 + ($term * 3); // Start of term: Jan, Apr, Jul, Oct
                $startDate = Carbon::create(null, $startMonth, 1);
                $endDate = (clone $startDate)->addMonths(3)->subDay(); // End of term

                // Create the datesheet
                $datesheet = [
                    'title' => ucfirst($currentTerm) . ' Term Examinations - ' . $class->name,
                    'class_id' => $class->id,
                    'term' => $currentTerm,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'status' => 'published',
                    'is_result_published' => true,
                    'description' => $faker->paragraph,
                    'instructions' => $faker->paragraph,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $datesheetId = DB::table('datesheets')->insertGetId($datesheet);

                // Create exams for the datesheet
                $subjects = [
                    'Mathematics', 'Science', 'English', 'History', 'Geography', 'Physics',
                    'Chemistry', 'Biology', 'Computer Science'
                ];

                $examIds = [];
                $dayNumber = 1;

                foreach ($subjects as $subject) {
                    // Create an exam
                    $examDate = (clone $startDate)->addDays($dayNumber);

                    // Skip weekends
                    while ($examDate->isWeekend()) {
                        $examDate->addDay();
                    }

                    $exam = [
                        'title' => ucfirst($currentTerm) . ' Term ' . $subject . ' Exam',
                        'teacher_id' => $teachers->random()->id,
                        'class_id' => $class->id,
                        'subject' => $subject,
                        'exam_date' => $examDate->format('Y-m-d'),
                        'start_time' => '09:00:00',
                        'end_time' => '11:00:00',
                        'total_marks' => 100,
                        'passing_marks' => 33,
                        'type' => $currentExamType,
                        'description' => $faker->paragraph,
                        'instructions' => $faker->paragraph,
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $examId = DB::table('exams')->insertGetId($exam);
                    $examIds[] = $examId;

                    // Create exam results for each student
                    foreach ($classStudents as $student) {
                        $marksObtained = $faker->numberBetween(25, 100);
                        $percentage = ($marksObtained / 100) * 100;
                        $isPassed = $marksObtained >= 33;

                        // Calculate grade based on percentage
                        $grade = 'F';
                        if ($percentage >= 90) {
                            $grade = 'A+';
                        } elseif ($percentage >= 80) {
                            $grade = 'A';
                        } elseif ($percentage >= 70) {
                            $grade = 'B+';
                        } elseif ($percentage >= 60) {
                            $grade = 'B';
                        } elseif ($percentage >= 50) {
                            $grade = 'C+';
                        } elseif ($percentage >= 40) {
                            $grade = 'C';
                        } elseif ($percentage >= 33) {
                            $grade = 'D';
                        }

                        $result = [
                            'exam_id' => $examId,
                            'student_id' => $student->id,
                            'marks_obtained' => $marksObtained,
                            'percentage' => $percentage,
                            'grade' => $grade,
                            'is_passed' => $isPassed,
                            'remarks' => $faker->optional(0.3)->sentence(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        DB::table('exam_results')->insert($result);
                    }

                    // Connect exam to datesheet
                    DB::table('datesheet_exam')->insert([
                        'datesheet_id' => $datesheetId,
                        'exam_id' => $examId,
                        'day_number' => $dayNumber,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $dayNumber++;
                }
            }
        }

        // Create some additional regular tests/exams
        $this->createAdditionalTests($faker, $classes, $teachers, $students);

        $this->command->info('Exams, datesheets, and results seeded successfully!');
    }

    /**
     * Create additional tests/exams (weekly, monthly, etc.)
     */
    private function createAdditionalTests($faker, $classes, $teachers, $students)
    {
        $this->command->info('Creating additional tests...');

        $testTypes = ['weekly', 'monthly'];

        foreach ($classes as $class) {
            $classStudents = $class->students;

            if ($classStudents->isEmpty()) {
                continue;
            }

            // Create 5 additional tests for each class
            for ($i = 0; $i < 5; $i++) {
                $testType = $faker->randomElement($testTypes);
                $subject = $faker->randomElement([
                    'Mathematics', 'Science', 'English', 'History', 'Geography', 'Physics',
                    'Chemistry', 'Biology', 'Computer Science'
                ]);

                $testDate = Carbon::now()->subDays($faker->numberBetween(7, 60));

                // Skip weekends
                while ($testDate->isWeekend()) {
                    $testDate->subDay();
                }

                $test = [
                    'title' => ucfirst($testType) . ' ' . $subject . ' Test',
                    'teacher_id' => $teachers->random()->id,
                    'class_id' => $class->id,
                    'subject' => $subject,
                    'exam_date' => $testDate->format('Y-m-d'),
                    'start_time' => '09:00:00',
                    'end_time' => '10:00:00',
                    'total_marks' => 50,
                    'passing_marks' => 17,
                    'type' => $testType,
                    'description' => $faker->paragraph,
                    'instructions' => $faker->paragraph,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $testId = DB::table('exams')->insertGetId($test);

                // Create test results for each student
                foreach ($classStudents as $student) {
                    $marksObtained = $faker->numberBetween(12, 50);
                    $percentage = ($marksObtained / 50) * 100;
                    $isPassed = $marksObtained >= 17;

                    // Calculate grade based on percentage
                    $grade = 'F';
                    if ($percentage >= 90) {
                        $grade = 'A+';
                    } elseif ($percentage >= 80) {
                        $grade = 'A';
                    } elseif ($percentage >= 70) {
                        $grade = 'B+';
                    } elseif ($percentage >= 60) {
                        $grade = 'B';
                    } elseif ($percentage >= 50) {
                        $grade = 'C+';
                    } elseif ($percentage >= 40) {
                        $grade = 'C';
                    } elseif ($percentage >= 33) {
                        $grade = 'D';
                    }

                    $result = [
                        'exam_id' => $testId,
                        'student_id' => $student->id,
                        'marks_obtained' => $marksObtained,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'is_passed' => $isPassed,
                        'remarks' => $faker->optional(0.3)->sentence(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    DB::table('exam_results')->insert($result);
                }
            }
        }

        $this->command->info('Additional tests created successfully!');
    }
}
