<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEquivalentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_code',
        'course_name',
        'credit_hours',
        'status',
        'notes'
    ];

    protected $casts = [
        'credit_hours' => 'decimal:1',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
