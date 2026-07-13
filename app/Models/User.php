<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['full_name', 'email', 'password', 'role'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_EMPLOYEE = 'employee';

    public const ROLE_INSTRUCTOR = 'instructor';

    public const ROLE_STUDENT = 'student';

    /** @var list<string> */
    public const ROLES = [
        self::ROLE_EMPLOYEE,
        self::ROLE_INSTRUCTOR,
        self::ROLE_STUDENT,
    ];

    public const UPDATED_AT = null;

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    /**
     * Employees have administrator access in the UI.
     */
    public function isAdmin(): bool
    {
        return $this->isEmployee();
    }

    public function isInstructor(): bool
    {
        return $this->role === self::ROLE_INSTRUCTOR;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Get the student's profile associated with the user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the employee's profile associated with the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Get the instructor's profile associated with the user.
     */
    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }
}
