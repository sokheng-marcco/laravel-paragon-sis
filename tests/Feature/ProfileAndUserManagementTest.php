<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileAndUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_users_receive_the_default_password(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        Employee::query()->create(['user_id' => $admin->id]);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/users', [
            'full_name' => 'New Student',
            'email' => 'new.student@example.com',
            'role' => User::ROLE_STUDENT,
        ]);

        $response->assertCreated();

        $createdUser = User::query()
            ->where('email', 'new.student@example.com')
            ->firstOrFail();

        $this->assertTrue(Hash::check('11112222', $createdUser->password));
        $this->assertDatabaseHas('students', ['user_id' => $createdUser->id]);
    }

    public function test_each_role_can_update_its_own_profile(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        Student::query()->create(['user_id' => $student->id]);
        Sanctum::actingAs($student);
        $this->patchJson('/api/profile', [
            'full_name' => 'Updated Student',
            'phone_number' => '100-200',
            'address' => 'Phnom Penh',
            'date_of_birth' => '2004-04-12',
        ])->assertOk();

        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        Instructor::query()->create(['user_id' => $instructor->id]);
        Sanctum::actingAs($instructor);
        $this->patchJson('/api/profile', [
            'full_name' => 'Updated Instructor',
            'phone_number' => '300-400',
            'department' => 'Information Systems',
        ])->assertOk();

        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        Employee::query()->create(['user_id' => $employee->id]);
        Sanctum::actingAs($employee);
        $this->patchJson('/api/profile', [
            'full_name' => 'Updated Administrator',
            'phone_number' => '500-600',
            'position' => 'Registrar',
        ])->assertOk();

        $this->assertDatabaseHas('students', [
            'user_id' => $student->id,
            'phone_number' => '100-200',
            'address' => 'Phnom Penh',
        ]);
        $this->assertDatabaseHas('instructors', [
            'user_id' => $instructor->id,
            'phone_number' => '300-400',
            'department' => 'Information Systems',
        ]);
        $this->assertDatabaseHas('employees', [
            'user_id' => $employee->id,
            'phone_number' => '500-600',
            'position' => 'Registrar',
        ]);
    }

    public function test_modal_web_routes_render_the_parent_page(): void
    {
        $this->get('/admin/students/create')->assertOk();
        $this->get('/admin/students/10/edit')->assertOk();
        $this->get('/admin/students/10/delete')->assertOk();
        $this->get('/instructor/grades/10/20/edit')->assertOk();
        $this->get('/student/profile/edit')->assertOk();
    }
}
