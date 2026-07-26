<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome', ['page' => 'landing'])
    ->name('home');
Route::view('/signin', 'welcome', ['page' => 'signin'])
    ->name('signin');
Route::view('/change-password', 'welcome', ['page' => 'change-password'])
    ->name('password.change');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::view('/', 'welcome', [
        'page' => 'admin-dashboard',
        'portalRole' => 'admin',
    ])->name('dashboard');

    $entities = [
        'students' => ['page' => 'admin-students', 'type' => 'student', 'parameter' => 'student'],
        'courses' => ['page' => 'admin-courses', 'type' => 'course', 'parameter' => 'course'],
        'enrollments' => ['page' => 'admin-enrollments', 'type' => 'enrollment', 'parameter' => 'enrollment'],
        'grades' => ['page' => 'admin-grades', 'type' => 'grade', 'parameter' => 'grade'],
        'users' => ['page' => 'admin-users', 'type' => 'user', 'parameter' => 'user'],
    ];

    foreach ($entities as $path => $entity) {
        $baseView = [
            'page' => $entity['page'],
            'portalRole' => 'admin',
        ];

        Route::view("/{$path}", 'welcome', $baseView)
            ->name($path);
        Route::view("/{$path}/create", 'welcome', $baseView + [
            'modalType' => $entity['type'],
            'modalMode' => 'create',
            'modalReturnRoute' => "admin.{$path}",
        ])->name("{$path}.create");
        Route::view("/{$path}/{{$entity['parameter']}}/edit", 'welcome', $baseView + [
            'modalType' => $entity['type'],
            'modalMode' => 'edit',
            'modalIdParameter' => $entity['parameter'],
            'modalReturnRoute' => "admin.{$path}",
        ])->name("{$path}.edit");
        Route::view("/{$path}/{{$entity['parameter']}}/delete", 'welcome', $baseView + [
            'modalType' => $entity['type'],
            'modalMode' => 'delete',
            'modalIdParameter' => $entity['parameter'],
            'modalReturnRoute' => "admin.{$path}",
        ])->name("{$path}.delete");
    }

    Route::view('/profile', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'admin',
    ])->name('profile');
    Route::view('/profile/edit', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'admin',
        'modalType' => 'profile',
        'modalMode' => 'edit',
        'modalReturnRoute' => 'admin.profile',
    ])->name('profile.edit');
});

Route::prefix('instructor')->name('instructor.')->group(function (): void {
    Route::view('/', 'welcome', [
        'page' => 'instructor-dashboard',
        'portalRole' => 'instructor',
    ])->name('dashboard');
    Route::view('/courses', 'welcome', [
        'page' => 'instructor-courses',
        'portalRole' => 'instructor',
    ])->name('courses');
    Route::view('/grades', 'welcome', [
        'page' => 'instructor-grades',
        'portalRole' => 'instructor',
    ])->name('grades');
    Route::view('/grades/{student}/{course}/edit', 'welcome', [
        'page' => 'instructor-grades',
        'portalRole' => 'instructor',
        'modalType' => 'grade',
        'modalMode' => 'edit',
        'modalIdParameter' => 'student',
        'modalCourseParameter' => 'course',
        'modalReturnRoute' => 'instructor.grades',
    ])->name('grades.edit');
    Route::view('/profile', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'instructor',
    ])->name('profile');
    Route::view('/profile/edit', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'instructor',
        'modalType' => 'profile',
        'modalMode' => 'edit',
        'modalReturnRoute' => 'instructor.profile',
    ])->name('profile.edit');
});

Route::prefix('student')->name('student.')->group(function (): void {
    Route::view('/', 'welcome', [
        'page' => 'student-dashboard',
        'portalRole' => 'student',
    ])->name('dashboard');
    Route::view('/courses', 'welcome', [
        'page' => 'student-browse',
        'portalRole' => 'student',
    ])->name('courses');
    Route::view('/enrollments', 'welcome', [
        'page' => 'student-enrollments',
        'portalRole' => 'student',
    ])->name('enrollments');
    Route::view('/grades', 'welcome', [
        'page' => 'student-grades',
        'portalRole' => 'student',
    ])->name('grades');
    Route::view('/profile', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'student',
    ])->name('profile');
    Route::view('/profile/edit', 'welcome', [
        'page' => 'profile',
        'portalRole' => 'student',
        'modalType' => 'profile',
        'modalMode' => 'edit',
        'modalReturnRoute' => 'student.profile',
    ])->name('profile.edit');
});
