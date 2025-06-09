<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'course_name_ar',
        'credit_hours',
        'college',
        'department',
        'is_active'
    ];

    protected $casts = [
        'credit_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function equivalentCourses()
    {
        return $this->hasMany(EquivalentCourse::class);
    }
}