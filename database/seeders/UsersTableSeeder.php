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
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'role' => 1, // Admin
            'remember_token' => Str::random(10),
            'phone' => '1234567890',
            'address' => '123 Admin Street',
            'status' => 'active',
        ]);

        // Create Teachers
        $teachers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@gmail.com',
            ],
            [
                'name' => 'Emily Johnson',
                'email' => 'emily.johnson@gmail.com',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@gmail.com',
            ],
            [
                'name' => 'Sarah Davis',
                'email' => 'sarah.davis@gmail.com',
            ],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => 2, // Teacher
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Teacher Avenue',
                'status' => 'active',

            ]);
        }

        // Create Parents
        $parents = [
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@gmail.com',
            ],
            [
                'name' => 'Jennifer Taylor',
                'email' => 'jennifer.taylor@gmail.com',
            ],
            [
                'name' => 'David Martinez',
                'email' => 'david.martinez@gmail.com',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@gmail.com',
            ],
        ];

        $parentIds = [];
        foreach ($parents as $parent) {
            $user = User::create([
                'name' => $parent['name'],
                'email' => $parent['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => 3, // Parent
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Parent Street',
                'status' => 'active',
            ]);
            $parentIds[] = $user->id;
        }

        // Create Students
        $students = [
            [
                'name' => 'Alex Wilson',
                'email' => 'alex.wilson@gmail.com',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@gmail.com',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Ryan Taylor',
                'email' => 'ryan.taylor@gmail.com',
                'parent_id' => $parentIds[1],
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia.martinez@gmail.com',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Daniel Martinez',
                'email' => 'daniel.martinez@gmail.com',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Olivia Anderson',
                'email' => 'olivia.anderson@gmail.com',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Ethan Anderson',
                'email' => 'ethan.anderson@gmail.com',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Isabella Johnson',
                'email' => 'isabella.johnson@gmail.com',
                'parent_id' => null,
            ],
            [
                'name' => 'James Brown',
                'email' => 'james.brown@gmail.com',
                'parent_id' => null,
            ],
            [
                'name' => 'Mia Davis',
                'email' => 'mia.davis@gmail.com',
                'parent_id' => null,
            ],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => 4, // Student
                'remember_token' => Str::random(10),
                'phone' => '1234' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Student Road',
                'parent_id' => $student['parent_id'],
                'status' => 'active',
            ]);
        }
    }
}
