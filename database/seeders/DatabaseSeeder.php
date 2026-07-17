<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Instructor;
use App\Models\Student;
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
        $adminUser = User::query()->updateOrCreate(
            ['email' => 'admin@university.edu'],
            [
                'full_name' => 'System Administrator',
                'password' => 'admin',
                'role' => User::ROLE_EMPLOYEE,
            ],
        );

        $instructorUser = User::query()->updateOrCreate(
            ['email' => 'instructor1@university.edu'],
            [
                'full_name' => 'Instructor 1',
                'password' => 'instructor',
                'role' => User::ROLE_INSTRUCTOR,
            ],
        );

        $studentUser = User::query()->updateOrCreate(
            ['email' => 'student1@university.edu'],
            [
                'full_name' => 'Student 1',
                'password' => 'student',
                'role' => User::ROLE_STUDENT,
            ],
        );

        $employee = Employee::query()->updateOrCreate(
            ['user_id' => $adminUser->id],
            [
                'phone_number' => '555-0100',
                'position' => 'System Administrator',
            ],
        );

        $instructor = Instructor::query()->updateOrCreate(
            ['user_id' => $instructorUser->id],
            [
                'phone_number' => '555-0200',
                'department' => 'Computer Science',
            ],
        );

        $student = Student::query()->updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'phone_number' => '555-0300',
                'address' => 'Phnom Penh, Cambodia',
                'date_of_birth' => '2005-01-15',
            ],
        );

        $computerScience = Course::query()->updateOrCreate(
            ['course_name' => 'Introduction to Computer Science'],
            [
                'instructor_id' => $instructor->instructor_id,
                'description' => 'A foundational course in computer science.',
                'duration' => 12,
            ],
        );

        $advancedPhysics = Course::query()->updateOrCreate(
            ['course_name' => 'Advanced Physics'],
            [
                'instructor_id' => $instructor->instructor_id,
                'description' => 'Advanced concepts and applications in physics.',
                'duration' => 16,
            ],
        );

        $history = Course::query()->updateOrCreate(
            ['course_name' => 'Introduction to History'],
            [
                'instructor_id' => $instructor->instructor_id,
                'description' => 'An introduction to major historical periods.',
                'duration' => 10,
            ],
        );

        foreach ([
            [$computerScience, '2026-01-15', Enrollment::STATUS_COMPLETED],
            [$advancedPhysics, '2026-02-01', Enrollment::STATUS_ENROLLED],
            [$history, '2026-01-20', Enrollment::STATUS_COMPLETED],
        ] as [$course, $date, $status]) {
            Enrollment::query()->updateOrCreate(
                [
                    'student_id' => $student->student_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'employee_id' => $employee->employee_id,
                    'enrollment_date' => $date,
                    'status' => $status,
                ],
            );
        }

        foreach ([
            [$computerScience, 88],
            [$history, 61],
        ] as [$course, $score]) {
            Grade::query()->updateOrCreate(
                [
                    'student_id' => $student->student_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'score' => $score,
                    'grade' => Grade::letterFor($score),
                    'graded_by' => $instructor->instructor_id,
                ],
            );
        }
    }
}
