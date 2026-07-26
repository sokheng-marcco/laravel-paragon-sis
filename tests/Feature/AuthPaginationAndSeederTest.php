<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthPaginationAndSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_the_default_password(): void
    {
        $user = User::factory()->create([
            'email' => 'password@example.com',
            'password' => '11112222',
            'role' => User::ROLE_STUDENT,
        ]);

        $this->postJson('/api/auth/change-password', [
            'email' => $user->email,
            'current_password' => '11112222',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertOk();

        $this->assertTrue(
            Hash::check('new-password-123', $user->refresh()->password),
        );
    }

    public function test_user_list_is_paginated_at_ten_records(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        Employee::query()->create(['user_id' => $admin->id]);
        User::factory()->count(14)->create();
        Sanctum::actingAs($admin);

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('per_page', 10)
            ->assertJsonPath('total', 15);
    }

    public function test_database_seeder_creates_ten_records_for_each_sis_type(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('employees', 10);
        $this->assertDatabaseCount('instructors', 10);
        $this->assertDatabaseCount('students', 10);
        $this->assertDatabaseCount('courses', 10);
        $this->assertDatabaseCount('enrollments', 10);
        $this->assertDatabaseCount('grades', 10);
        $this->assertSame(30, User::query()->count());
        $this->assertDatabaseHas('users', [
            'email' => 'student1@university.edu',
            'full_name' => 'Dara Sok',
            'role' => User::ROLE_STUDENT,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'instructor1@university.edu',
            'full_name' => 'Dr. Sok Dara',
            'role' => User::ROLE_INSTRUCTOR,
        ]);

        $this->assertTrue(
            Hash::check(
                '11112222',
                User::query()->where('email', 'student1@university.edu')->firstOrFail()->password,
            ),
        );
    }

    public function test_academic_list_endpoints_return_at_most_ten_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()
            ->where('email', 'admin@university.edu')
            ->firstOrFail();
        Sanctum::actingAs($admin);

        foreach (['students', 'courses', 'enrollments', 'grades'] as $endpoint) {
            $this->getJson("/api/{$endpoint}")
                ->assertOk()
                ->assertJsonCount(10, 'data')
                ->assertJsonPath('per_page', 10);
        }
    }
}
