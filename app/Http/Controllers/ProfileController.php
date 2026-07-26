<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update the authenticated user's account and role-specific profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $rules = [
            'full_name' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];

        if ($user->isStudent()) {
            $rules += [
                'address' => ['sometimes', 'nullable', 'string'],
                'date_of_birth' => ['sometimes', 'nullable', 'date'],
            ];
        } elseif ($user->isInstructor()) {
            $rules += [
                'department' => ['sometimes', 'nullable', 'string', 'max:30'],
            ];
        } else {
            $rules += [
                'position' => ['sometimes', 'nullable', 'string', 'max:50'],
            ];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($user, $validated): void {
            $user->update(array_intersect_key(
                $validated,
                array_flip(['full_name', 'email']),
            ));

            $profile = match ($user->role) {
                User::ROLE_STUDENT => $user->student()->firstOrCreate(),
                User::ROLE_INSTRUCTOR => $user->instructor()->firstOrCreate(),
                User::ROLE_EMPLOYEE => $user->employee()->firstOrCreate(),
            };

            $profile->update(array_diff_key(
                $validated,
                array_flip(['full_name', 'email']),
            ));
        });

        return response()->json(
            $user->refresh()->load(['student', 'instructor', 'employee']),
        );
    }
}
