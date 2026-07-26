<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Attributes\Fillable;
=======
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> feature-1-2
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['instructor_id', 'course_name', 'description', 'duration'])]
class Course extends Model
{
<<<<<<< HEAD
=======
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

>>>>>>> feature-1-2
    protected $primaryKey = 'course_id';

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'course_id');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> feature-1-2
