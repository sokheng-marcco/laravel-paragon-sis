<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'course_id', 'employee_id', 'enrollment_date', 'status'])]
class Enrollment extends Model
{
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DROPPED = 'dropped';

    public const STATUS_ENROLLED = 'enrolled';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_ENROLLED,
        self::STATUS_COMPLETED,
        self::STATUS_DROPPED,
    ];

    protected $primaryKey = 'enrollment_id';

    public $timestamps = false;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
        ];
    }
}
