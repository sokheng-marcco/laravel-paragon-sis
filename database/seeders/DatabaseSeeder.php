<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@university.edu'],
            [
                'full_name' => 'System Administrator',
                'password' => 'admin',
                'role' => User::ROLE_EMPLOYEE,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'instructor1@university.edu'],
            [
                'full_name' => 'Instructor 1',
                'password' => 'instructor',
                'role' => User::ROLE_INSTRUCTOR,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'student1@university.edu'],
            [
                'full_name' => 'Student 1',
                'password' => 'student',
                'role' => User::ROLE_STUDENT,
            ],
        );
    }
}
