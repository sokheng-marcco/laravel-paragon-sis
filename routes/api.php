<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class)
        ->middleware('role:employee');

    Route::get('enrollments', [EnrollmentController::class, 'index'])
        ->middleware('role:employee,student');
    Route::post('enrollments', [EnrollmentController::class, 'store'])
        ->middleware('role:employee,student');
    Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])
        ->middleware('role:employee,student');
    Route::match(['put', 'patch'], 'enrollments/{enrollment}', [EnrollmentController::class, 'update'])
        ->middleware('role:employee');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])
        ->middleware('role:employee');

    Route::get('grades', [GradeController::class, 'index'])
        ->middleware('role:employee,instructor,student');
    Route::post('grades', [GradeController::class, 'store'])
        ->middleware('role:employee,instructor');
    Route::get('grades/{grade}', [GradeController::class, 'show'])
        ->middleware('role:employee,instructor,student');
    Route::match(['put', 'patch'], 'grades/{grade}', [GradeController::class, 'update'])
        ->middleware('role:employee,instructor');
    Route::delete('grades/{grade}', [GradeController::class, 'destroy'])
        ->middleware('role:employee');
});
