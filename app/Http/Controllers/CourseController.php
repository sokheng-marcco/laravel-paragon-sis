<?php

namespace App\Http\Controllers;

use App\Models\Course;
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

        $courses = Course::query()
            ->with('instructor.user:id,full_name')
            ->when($search, fn ($query, string $search) =>
                $query->where('course_name', 'like', "%{$search}%"))
            ->latest('created_at')
            ->get();

        return response()->json($courses);
    }

    // Add Courses
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'instructor_id' => ['required', 'integer', 'exists:instructors,instructor_id'],
            'course_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer'],
        ]);

        $course = Course::query()->create($validated);

        return response()->json($course->load('instructor.user'), 201);
    }

    // Display Course by ID
    public function show(Course $course): JsonResponse
    {
        return response()->json($course->load('instructor.user'));
    }

    // Update Courses
    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'instructor_id' => ['sometimes', 'required', 'integer', 'exists:instructors,instructor_id'],
            'course_name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'duration' => ['sometimes', 'nullable', 'integer'],
        ]);

        $course->update($validated);

        return response()->json($course->refresh()->load('instructor.user'));
    }

    // Delete Courses
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json(null, 204);
    }
}