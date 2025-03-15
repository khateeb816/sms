<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 1, // Admin
            'remember_token' => Str::random(10),
            'phone' => '1234567890',
            'address' => '123 Admin Street',
        ]);

        // Create Teachers
        $teachers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
            ],
            [
                'name' => 'Emily Johnson',
                'email' => 'emily.johnson@example.com',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
            ],
            [
                'name' => 'Sarah Davis',
                'email' => 'sarah.davis@example.com',
            ],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 2, // Teacher
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Teacher Avenue',
            ]);
        }

        // Create Parents
        $parents = [
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@example.com',
            ],
            [
                'name' => 'Jennifer Taylor',
                'email' => 'jennifer.taylor@example.com',
            ],
            [
                'name' => 'David Martinez',
                'email' => 'david.martinez@example.com',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@example.com',
            ],
        ];

        $parentIds = [];
        foreach ($parents as $parent) {
            $user = User::create([
                'name' => $parent['name'],
                'email' => $parent['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 3, // Parent
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Parent Street',
            ]);
            $parentIds[] = $user->id;
        }

        // Create Students
        $students = [
            [
                'name' => 'Alex Wilson',
                'email' => 'alex.wilson@example.com',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@example.com',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Ryan Taylor',
                'email' => 'ryan.taylor@example.com',
                'parent_id' => $parentIds[1],
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia.martinez@example.com',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Daniel Martinez',
                'email' => 'daniel.martinez@example.com',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Olivia Anderson',
                'email' => 'olivia.anderson@example.com',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Ethan Anderson',
                'email' => 'ethan.anderson@example.com',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Isabella Johnson',
                'email' => 'isabella.johnson@example.com',
                'parent_id' => null,
            ],
            [
                'name' => 'James Brown',
                'email' => 'james.brown@example.com',
                'parent_id' => null,
            ],
            [
                'name' => 'Mia Davis',
                'email' => 'mia.davis@example.com',
                'parent_id' => null,
            ],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 4, // Student
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Student Road',
                'parent_id' => $student['parent_id'],
            ]);
        }
    }
}
