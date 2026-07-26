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

    private const DEFAULT_PASSWORD = '11112222';

    /**
     * Seed ten records for every SIS role/profile and academic model.
     */
    public function run(): void
    {
        $employees = [];
        $instructors = [];
        $students = [];

        for ($number = 1; $number <= 10; $number++) {
            $adminEmail = $number === 1
                ? 'admin@university.edu'
                : "admin{$number}@university.edu";
            $adminUser = User::query()->updateOrCreate(
                ['email' => $adminEmail],
                [
                    'full_name' => $number === 1 ? 'System Administrator' : "Administrator {$number}",
                    'password' => self::DEFAULT_PASSWORD,
                    'role' => User::ROLE_EMPLOYEE,
                ],
            );
            $employees[] = Employee::query()->updateOrCreate(
                ['user_id' => $adminUser->id],
                [
                    'phone_number' => sprintf('555-01%02d', $number),
                    'position' => $number === 1 ? 'System Administrator' : "Administrative Officer {$number}",
                ],
            );

            $instructorUser = User::query()->updateOrCreate(
                ['email' => "instructor{$number}@university.edu"],
                [
                    'full_name' => $this->instructorNames()[$number - 1],
                    'password' => self::DEFAULT_PASSWORD,
                    'role' => User::ROLE_INSTRUCTOR,
                ],
            );
            $instructors[] = Instructor::query()->updateOrCreate(
                ['user_id' => $instructorUser->id],
                [
                    'phone_number' => sprintf('555-02%02d', $number),
                    'department' => $this->departments()[$number - 1],
                ],
            );

            $studentUser = User::query()->updateOrCreate(
                ['email' => "student{$number}@university.edu"],
                [
                    'full_name' => $this->studentNames()[$number - 1],
                    'password' => self::DEFAULT_PASSWORD,
                    'role' => User::ROLE_STUDENT,
                ],
            );
            $students[] = Student::query()->updateOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'phone_number' => sprintf('555-03%02d', $number),
                    'address' => "District {$number}, Phnom Penh, Cambodia",
                    'date_of_birth' => sprintf('2005-%02d-15', $number),
                ],
            );
        }

        $courses = [];
        foreach ($this->courseDefinitions() as $index => [$name, $description, $duration]) {
            $courses[] = Course::query()->updateOrCreate(
                ['course_name' => $name],
                [
                    'instructor_id' => $instructors[$index]->instructor_id,
                    'description' => $description,
                    'duration' => $duration,
                ],
            );
        }

        $scores = [88, 76, 93, 81, 69, 95, 73, 84, 79, 91];
        foreach ($students as $index => $student) {
            $course = $courses[$index];
            Enrollment::query()->updateOrCreate(
                [
                    'student_id' => $student->student_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'employee_id' => $employees[$index]->employee_id,
                    'enrollment_date' => sprintf('2026-%02d-01', ($index % 10) + 1),
                    'status' => Enrollment::STATUS_COMPLETED,
                ],
            );

            Grade::query()->updateOrCreate(
                [
                    'student_id' => $student->student_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'score' => $scores[$index],
                    'grade' => Grade::letterFor($scores[$index]),
                    'graded_by' => $instructors[$index]->instructor_id,
                ],
            );
        }
    }

    /**
     * @return list<string>
     */
    private function departments(): array
    {
        return [
            'Computer Science',
            'Information Systems',
            'Physics',
            'Mathematics',
            'History',
            'Business',
            'English',
            'Design',
            'Engineering',
            'Economics',
        ];
    }

    /**
     * @return list<string>
     */
    private function instructorNames(): array
    {
        return [
            'Dr. Sok Dara',
            'Prof. Chan Sopheak',
            'Dr. Lim Sreypov',
            'Prof. Chea Vannak',
            'Dr. Heng Piseth',
            'Prof. Kim Sothea',
            'Dr. Touch Sreymom',
            'Prof. Mao Rithy',
            'Dr. Keo Samnang',
            'Prof. Yim Bopha',
        ];
    }

    /**
     * @return list<string>
     */
    private function studentNames(): array
    {
        return [
            'Dara Sok',
            'Sreypich Chan',
            'Vannak Lim',
            'Sophea Chea',
            'Piseth Heng',
            'Sothea Kim',
            'Sreymom Touch',
            'Rithy Mao',
            'Samnang Keo',
            'Bopha Yim',
        ];
    }

    /**
     * @return list<array{string, string, int}>
     */
    private function courseDefinitions(): array
    {
        return [
            ['Introduction to Computer Science', 'A foundational course in computer science.', 12],
            ['Student Information Systems', 'Academic information systems and workflows.', 14],
            ['Advanced Physics', 'Advanced concepts and applications in physics.', 16],
            ['Applied Mathematics', 'Mathematical techniques for practical problems.', 12],
            ['Introduction to History', 'An introduction to major historical periods.', 10],
            ['Business Analytics', 'Data-driven analysis for business decisions.', 12],
            ['Academic Writing', 'Research, citation, and academic communication.', 10],
            ['UI Design Fundamentals', 'Foundations of accessible interface design.', 11],
            ['Software Engineering', 'Software design, testing, and project practices.', 15],
            ['Principles of Economics', 'Core concepts in micro and macroeconomics.', 13],
        ];
    }
}
