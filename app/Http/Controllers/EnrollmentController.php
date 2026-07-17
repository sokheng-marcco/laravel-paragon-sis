<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(Enrollment::STATUSES)],
            'course_id' => ['nullable', 'integer', 'exists:courses,course_id'],
            'student_id' => ['nullable', 'integer', 'exists:students,student_id'],
        ]);

        $user = $request->user();
        $query = Enrollment::query()->with([
            'student.user:id,full_name,email',
            'course.instructor.user:id,full_name',
            'employee.user:id,full_name',
        ]);

        if ($user->isStudent()) {
            $query->where('student_id', $this->studentFor($user)->student_id);
        }

        $query
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($validated['course_id'] ?? null, fn ($query, int $courseId) => $query->where('course_id', $courseId));

        if ($user->isEmployee()) {
            $query
                ->when($validated['student_id'] ?? null, fn ($query, int $studentId) => $query->where('student_id', $studentId))
                ->when($validated['search'] ?? null, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->whereHas('student.user', fn ($query) => $query->where('full_name', 'like', "%{$search}%"))
                            ->orWhereHas('course', fn ($query) => $query->where('course_name', 'like', "%{$search}%"));
                    });
                });
        }

        return response()->json(
            $query->latest('enrollment_date')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $rules = [
            'course_id' => ['required', 'integer', 'exists:courses,course_id'],
        ];

        if ($user->isEmployee()) {
            $rules += [
                'student_id' => ['required', 'integer', 'exists:students,student_id'],
                'enrollment_date' => ['nullable', 'date'],
                'status' => ['nullable', Rule::in(Enrollment::STATUSES)],
            ];
        }

        $validated = $request->validate($rules);
        $studentId = $user->isStudent()
            ? $this->studentFor($user)->student_id
            : $validated['student_id'];

        $this->ensureNotEnrolled($studentId, $validated['course_id']);

        $enrollment = Enrollment::query()->create([
            'student_id' => $studentId,
            'course_id' => $validated['course_id'],
            'employee_id' => $user->isEmployee() ? $user->employee?->employee_id : null,
            'enrollment_date' => $validated['enrollment_date'] ?? now()->toDateString(),
            'status' => $validated['status'] ?? Enrollment::STATUS_ENROLLED,
        ]);

        return response()->json(
            $enrollment->load(['student.user', 'course.instructor.user', 'employee.user']),
            201,
        );
    }

    public function show(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->authorizeView($request->user(), $enrollment);

        return response()->json(
            $enrollment->load(['student.user', 'course.instructor.user', 'employee.user']),
        );
    }

    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['sometimes', 'required', 'integer', 'exists:students,student_id'],
            'course_id' => ['sometimes', 'required', 'integer', 'exists:courses,course_id'],
            'enrollment_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', Rule::in(Enrollment::STATUSES)],
        ]);

        $studentId = $validated['student_id'] ?? $enrollment->student_id;
        $courseId = $validated['course_id'] ?? $enrollment->course_id;

        $this->ensureNotEnrolled($studentId, $courseId, $enrollment);

        $enrollment->update($validated);

        return response()->json(
            $enrollment->refresh()->load(['student.user', 'course.instructor.user', 'employee.user']),
        );
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        DB::transaction(function () use ($enrollment): void {
            Grade::query()
                ->where('student_id', $enrollment->student_id)
                ->where('course_id', $enrollment->course_id)
                ->delete();

            $enrollment->delete();
        });

        return response()->json([
            'message' => 'Enrollment deleted successfully.',
        ], 200);
    }

    private function authorizeView(User $user, Enrollment $enrollment): void
    {
        if ($user->isEmployee()) {
            return;
        }

        abort_unless(
            $user->isStudent() && $enrollment->student_id === $this->studentFor($user)->student_id,
            403,
            'You are not authorized to view this enrollment.',
        );
    }

    private function studentFor(User $user): Student
    {
        $student = $user->student;

        abort_unless($student, 422, 'The authenticated user does not have a student profile.');

        return $student;
    }

    /**
     * @throws ValidationException
     */
    private function ensureNotEnrolled(
        int $studentId,
        int $courseId,
        ?Enrollment $except = null,
    ): void {
        $query = Enrollment::query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId);

        if ($except) {
            $query->whereKeyNot($except->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'course_id' => ['The student is already enrolled in this course.'],
            ]);
        }
    }
}
