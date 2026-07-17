<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->latest('created_at')
            ->get();

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
    public function show(Student $student): JsonResponse
    {
        return response()->json($student->load('user'));
    }

    /**
     * Update the specified student. (Update Students)
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'required', 'string', 'max:50'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
        ]);

        if (array_key_exists('full_name', $validated)) {
            $student->user->update(['full_name' => $validated['full_name']]);
            unset($validated['full_name']);
        }

        $student->update($validated);

        return response()->json($student->refresh()->load('user'));
    }

    /**
     * Remove the specified student. (Delete Students)
     */
    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(null, 204);
    }
}