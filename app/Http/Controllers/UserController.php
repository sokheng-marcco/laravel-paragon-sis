<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['student', 'instructor', 'employee'])
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, string $role) => $query->where('role', $role))
            ->latest('created_at')
            ->paginate(10);

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

        $validated['password'] = $validated['password'] ?? '11112222';

        $user = DB::transaction(function () use ($validated): User {
            $user = User::query()->create($validated);
            $this->ensureRoleProfile($user);

            return $user;
        });

        return response()->json(
            $user->load(['student', 'instructor', 'employee']),
            201,
        );
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user->load(['student', 'instructor', 'employee']));
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

        DB::transaction(function () use ($user, $validated): void {
            $user->update($validated);
            $this->ensureRoleProfile($user);
        });

        return response()->json(
            $user->refresh()->load(['student', 'instructor', 'employee']),
        );
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }

    private function ensureRoleProfile(User $user): void
    {
        match ($user->role) {
            User::ROLE_STUDENT => $user->student()->firstOrCreate(),
            User::ROLE_INSTRUCTOR => $user->instructor()->firstOrCreate(),
            User::ROLE_EMPLOYEE => $user->employee()->firstOrCreate(),
        };
    }
}
