<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(User::ROLES)],
        ]);

        $search = $validated['search'] ?? null;
        $role = $validated['role'] ?? null;

        $users = User::query()
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, string $role) => $query->where('role', $role))
            ->latest('created_at')
            ->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        // The Add User dialog does not request a password. A random password
        // keeps the account secure until a password setup/reset flow is used.
        $validated['password'] = $validated['password'] ?? Str::password(32);

        $user = User::query()->create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'max:255'],
            'role' => [
                'sometimes',
                'required',
                Rule::in(User::ROLES),
            ],
        ]);

        if (array_key_exists('password', $validated) && $validated['password'] === null) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->refresh());
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
