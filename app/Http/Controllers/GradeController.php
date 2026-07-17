<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'course_id' => ['nullable', 'integer', 'exists:courses,course_id'],
            'student_id' => ['nullable', 'integer', 'exists:students,student_id'],
        ]);

        $user = $request->user();
        $query = Grade::query()->with([
            'student.user:id,full_name,email',
            'course:course_id,instructor_id,course_name',
            'grader.user:id,full_name',
        ]);

        if ($user->isInstructor()) {
            $query->whereHas(
                'course',
                fn ($query) => $query->where('instructor_id', $this->instructorFor($user)->instructor_id),
            );
        } elseif ($user->isStudent()) {
            $query->where('student_id', $this->studentFor($user)->student_id);
        }

        $query->when(
            $validated['course_id'] ?? null,
            fn ($query, int $courseId) => $query->where('course_id', $courseId),
        );

        if (! $user->isStudent()) {
            $query
                ->when($validated['student_id'] ?? null, fn ($query, int $studentId) => $query->where('student_id', $studentId))
                ->when($validated['search'] ?? null, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->whereHas('student.user', fn ($query) => $query->where('full_name', 'like', "%{$search}%"))
                            ->orWhereHas('course', fn ($query) => $query->where('course_name', 'like', "%{$search}%"));
                    });
                });
        }

        return response()->json($query->latest('graded_at')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,student_id'],
            'course_id' => ['required', 'integer', 'exists:courses,course_id'],
            'score' => ['required', 'numeric', 'between:0,100'],
            'graded_by' => ['sometimes', 'integer', 'exists:instructors,instructor_id'],
        ]);

        $course = Course::query()->findOrFail($validated['course_id']);
        $graderId = $this->graderFor($request->user(), $course, $validated['graded_by'] ?? null);

        $this->ensureEnrolled($validated['student_id'], $course->course_id);
        $this->ensureGradeDoesNotExist($validated['student_id'], $course->course_id);

        $grade = Grade::query()->create([
            'student_id' => $validated['student_id'],
            'course_id' => $course->course_id,
            'score' => $validated['score'],
            'grade' => Grade::letterFor((float) $validated['score']),
            'graded_by' => $graderId,
        ]);

        return response()->json(
            $grade->load(['student.user', 'course', 'grader.user']),
            201,
        );
    }

    public function show(Request $request, Grade $grade): JsonResponse
    {
        $this->authorizeView($request->user(), $grade);

        return response()->json(
            $grade->load(['student.user', 'course', 'grader.user']),
        );
    }

    public function update(Request $request, Grade $grade): JsonResponse
    {
        $user = $request->user();
        $rules = [
            'score' => ['sometimes', 'required', 'numeric', 'between:0,100'],
        ];

        if ($user->isEmployee()) {
            $rules += [
                'student_id' => ['sometimes', 'required', 'integer', 'exists:students,student_id'],
                'course_id' => ['sometimes', 'required', 'integer', 'exists:courses,course_id'],
                'graded_by' => ['sometimes', 'integer', 'exists:instructors,instructor_id'],
            ];
        }

        $validated = $request->validate($rules);
        $studentId = $validated['student_id'] ?? $grade->student_id;
        $courseId = $validated['course_id'] ?? $grade->course_id;
        $course = Course::query()->findOrFail($courseId);
        $graderId = $this->graderFor($user, $course, $validated['graded_by'] ?? null);

        $this->ensureEnrolled($studentId, $courseId);
        $this->ensureGradeDoesNotExist($studentId, $courseId, $grade);

        $grade->fill([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'graded_by' => $graderId,
        ]);

        if (array_key_exists('score', $validated)) {
            $grade->score = $validated['score'];
            $grade->grade = Grade::letterFor((float) $validated['score']);
        }

        $grade->save();

        return response()->json(
            $grade->refresh()->load(['student.user', 'course', 'grader.user']),
        );
    }

    public function destroy(Grade $grade): JsonResponse
    {
        $grade->delete();

        return response()->json(null, 204);
    }

    private function authorizeView(User $user, Grade $grade): void
    {
        if ($user->isEmployee()) {
            return;
        }

        if ($user->isInstructor()) {
            abort_unless(
                $grade->course->instructor_id === $this->instructorFor($user)->instructor_id,
                403,
                'You are not authorized to view this grade.',
            );

            return;
        }

        abort_unless(
            $user->isStudent() && $grade->student_id === $this->studentFor($user)->student_id,
            403,
            'You are not authorized to view this grade.',
        );
    }

    private function graderFor(User $user, Course $course, ?int $requestedGraderId): int
    {
        if ($user->isInstructor()) {
            $instructorId = $this->instructorFor($user)->instructor_id;

            abort_unless(
                $course->instructor_id === $instructorId,
                403,
                'Instructors may only grade courses assigned to them.',
            );

            return $instructorId;
        }

        if ($requestedGraderId !== null && $requestedGraderId !== $course->instructor_id) {
            throw ValidationException::withMessages([
                'graded_by' => ['The grader must be the instructor assigned to this course.'],
            ]);
        }

        return $course->instructor_id;
    }

    private function instructorFor(User $user): Instructor
    {
        $instructor = $user->instructor;

        abort_unless($instructor, 422, 'The authenticated user does not have an instructor profile.');

        return $instructor;
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
    private function ensureEnrolled(int $studentId, int $courseId): void
    {
        $isEnrolled = Enrollment::query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', '!=', Enrollment::STATUS_DROPPED)
            ->exists();

        if (! $isEnrolled) {
            throw ValidationException::withMessages([
                'student_id' => ['The student must have an active or completed enrollment in this course.'],
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function ensureGradeDoesNotExist(
        int $studentId,
        int $courseId,
        ?Grade $except = null,
    ): void {
        $query = Grade::query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId);

        if ($except) {
            $query->whereKeyNot($except->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'student_id' => ['A grade already exists for this student and course.'],
            ]);
        }
    }
}
