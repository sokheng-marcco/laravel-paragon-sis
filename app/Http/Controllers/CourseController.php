<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Display Courses
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $validated['search'] ?? null;

        $user = $request->user();
        $courses = Course::query()
            ->with('instructor.user:id,full_name')
            ->withCount('enrollments')
            ->when($user->isInstructor(), fn ($query) => $query->where(
                'instructor_id',
                $this->instructorFor($user)->instructor_id,
            ))
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('course_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas(
                            'instructor.user',
                            fn ($query) => $query->where('full_name', 'like', "%{$search}%"),
                        );
                });
            })
            ->latest('created_at')
            ->paginate(10);

        return response()->json($courses);
    }

    // Add Courses
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'instructor_id' => ['required', 'integer', 'exists:instructors,instructor_id'],
            'course_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:1', 'max:520'],
        ]);

        $course = Course::query()->create($validated);

        return response()->json($course->load('instructor.user'), 201);
    }

    // Display Course by ID
    public function show(Request $request, Course $course): JsonResponse
    {
        $this->authorizeView($request->user(), $course);

        return response()->json(
            $course->load('instructor.user')->loadCount('enrollments'),
        );
    }

    // Update Courses
    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'instructor_id' => ['sometimes', 'required', 'integer', 'exists:instructors,instructor_id'],
            'course_name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'duration' => ['sometimes', 'required', 'integer', 'min:1', 'max:520'],
        ]);

        $course->update($validated);

        return response()->json($course->refresh()->load('instructor.user'));
    }

    // Delete Courses
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.',
        ]);
    }

    private function authorizeView(User $user, Course $course): void
    {
        if (! $user->isInstructor()) {
            return;
        }

        abort_unless(
            $course->instructor_id === $this->instructorFor($user)->instructor_id,
            403,
            'Instructors may only view courses assigned to them.',
        );
    }

    private function instructorFor(User $user): Instructor
    {
        $instructor = $user->instructor;

        abort_unless($instructor, 422, 'The authenticated user does not have an instructor profile.');

        return $instructor;
    }
}
