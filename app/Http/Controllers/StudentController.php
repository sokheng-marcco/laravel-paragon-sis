<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display all students. (Display Students)
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $validated['search'] ?? null;

        $students = Student::query()
            ->with('user:id,full_name,email')
            ->when($search, function ($query, string $search): void {
                $query->whereHas('user', function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('student_id')
            ->paginate(10);

        return response()->json($students);
    }

    /**
     * Store a newly created student. (Add Students)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'student'),
                'unique:students,user_id',
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $student = Student::query()->create($validated);

        return response()->json($student->load('user'), 201);
    }

    /**
     * Display the specified student. (Display Students by ID)
     */
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorizeView($request->user(), $student);

        return response()->json($student->load('user'));
    }

    /**
     * Display the authenticated student's profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($this->studentFor($request->user())->load('user'));
    }

    /**
     * Update the specified student. (Update Students)
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
        ]);

        DB::transaction(function () use ($student, $validated): void {
            $userAttributes = array_intersect_key(
                $validated,
                array_flip(['full_name', 'email']),
            );

            if ($userAttributes !== []) {
                $student->user->update($userAttributes);
            }

            $student->update(array_diff_key(
                $validated,
                array_flip(['full_name', 'email']),
            ));
        });

        return response()->json($student->refresh()->load('user'));
    }

    /**
     * Update the authenticated student's profile.
     */
    public function updateMe(Request $request): JsonResponse
    {
        return $this->update($request, $this->studentFor($request->user()));
    }

    /**
     * Remove the specified student. (Delete Students)
     */
    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }

    private function authorizeView(User $user, Student $student): void
    {
        if ($user->isEmployee()) {
            return;
        }

        abort_unless(
            $user->isStudent() && $student->user_id === $user->id,
            403,
            'You are not authorized to view this student.',
        );
    }

    private function studentFor(User $user): Student
    {
        $student = $user->student;

        abort_unless($student, 422, 'The authenticated user does not have a student profile.');

        return $student;
    }
}
