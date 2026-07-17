<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'course_id', 'score', 'grade', 'graded_by'])]
class Grade extends Model
{
    public const CREATED_AT = 'graded_at';

    public const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'grade_id';

    public static function letterFor(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F',
        };
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'graded_by');
    }

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
