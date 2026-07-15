<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_sign_in_and_receive_a_token(): void
    {
        $user = User::factory()->create([
            'email' => 'student1@university.edu',
            'password' => 'student',
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'student1@university.edu',
            'password' => 'student',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role', User::ROLE_STUDENT)
            ->assertJsonStructure(['token', 'expires_at']);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'student1@university.edu',
            'password' => 'student',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'student1@university.edu',
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_only_employees_can_manage_user_accounts(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();

        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        Sanctum::actingAs($employee);

        $this->getJson('/api/users')->assertOk();

        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        Sanctum::actingAs($instructor);

        $this->getJson('/api/users')->assertForbidden();

        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        Sanctum::actingAs($student);

        $this->getJson('/api/users')->assertForbidden();
    }

    public function test_unauthenticated_api_requests_never_redirect_to_a_login_page(): void
    {
        $this->get('/api/users')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_an_authenticated_user_can_view_their_account_and_sign_out(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('role', User::ROLE_INSTRUCTOR);

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Signed out successfully.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_mock_users_are_seeded_with_their_expected_roles(): void
    {
        $this->seed();

        $employee = User::query()->where('email', 'admin@university.edu')->firstOrFail();
        $instructor = User::query()->where('email', 'instructor1@university.edu')->firstOrFail();
        $student = User::query()->where('email', 'student1@university.edu')->firstOrFail();

        $this->assertTrue($employee->isAdmin());
        $this->assertTrue($instructor->isInstructor());
        $this->assertTrue($student->isStudent());
        $this->assertTrue(Hash::check('admin', $employee->password));
        $this->assertTrue(Hash::check('instructor', $instructor->password));
        $this->assertTrue(Hash::check('student', $student->password));
    }
}
