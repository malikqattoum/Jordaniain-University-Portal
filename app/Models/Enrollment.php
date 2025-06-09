<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'semester',
        'grade',
        'grade_points',
        'status',
        'is_passed'
    ];

    protected $casts = [
        'grade_points' => 'decimal:2',
        'is_passed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getGradeInArabic()
    {
        $gradeMap = [
            'A+' => 'ممتاز مرتفع',
            'A' => 'ممتاز',
            'A-' => 'ممتاز منخفض',
            'B+' => 'جيد جداً مرتفع',
            'B' => 'جيد جداً',
            'B-' => 'جيد جداً منخفض',
            'C+' => 'جيد مرتفع',
            'C' => 'جيد',
            'C-' => 'جيد منخفض',
            'D+' => 'مقبول مرتفع',
            'D' => 'مقبول',
            'F' => 'راسب',
            'P' => 'ناجح',
            'NP' => 'راسب',
            'W' => 'منسحب',
            'I' => 'غير مكتمل'
        ];

        return $gradeMap[$this->grade] ?? $this->grade;
    }
}
