<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Authenticate a user and issue an API token.
     *
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $expiresAt = ($validated['remember'] ?? false)
            ? now()->addDays(30)
            : now()->addHours(8);

        $token = $user->createToken(
            $validated['device_name'] ?? 'web',
            $this->abilitiesFor($user),
            $expiresAt,
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toISOString(),
            'user' => $user,
        ]);
    }

    /**
     * Return the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        $accessToken = $request->user()?->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Signed out successfully.',
        ]);
    }

    /**
     * @return list<string>
     */
    private function abilitiesFor(User $user): array
    {
        return match ($user->role) {
            User::ROLE_EMPLOYEE => ['users:manage', 'students:manage', 'courses:manage', 'enrollments:manage', 'grades:manage'],
            User::ROLE_INSTRUCTOR => ['courses:view', 'grades:manage'],
            User::ROLE_STUDENT => ['courses:view', 'enrollments:view', 'grades:view'],
        };
    }
}
