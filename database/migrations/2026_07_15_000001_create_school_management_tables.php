<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table): void {
            $table->id('student_id');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id('employee_id');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone_number', 20)->nullable();
            $table->string('position', 50)->nullable();
        });

        Schema::create('instructors', function (Blueprint $table): void {
            $table->id('instructor_id');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone_number', 20)->nullable();
            $table->string('department', 30)->nullable();
        });

        Schema::create('courses', function (Blueprint $table): void {
            $table->id('course_id');
            $table->foreignId('instructor_id')
                ->constrained('instructors', 'instructor_id')
                ->restrictOnDelete();
            $table->string('course_name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('duration');
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id('enrollment_id');
            $table->foreignId('student_id')
                ->constrained('students', 'student_id')
                ->cascadeOnDelete();
            $table->foreignId('course_id')
                ->constrained('courses', 'course_id')
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees', 'employee_id')
                ->nullOnDelete();
            $table->date('enrollment_date');
            $table->string('status', 50)->default('enrolled');
            $table->unique(['student_id', 'course_id']);
        });

        Schema::create('grades', function (Blueprint $table): void {
            $table->id('grade_id');
            $table->foreignId('student_id')
                ->constrained('students', 'student_id')
                ->cascadeOnDelete();
            $table->foreignId('course_id')
                ->constrained('courses', 'course_id')
                ->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('grade', 5);
            $table->foreignId('graded_by')
                ->constrained('instructors', 'instructor_id')
                ->restrictOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('students');
    }
};
