<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'semester',
        'semester_credit_hours',
        'semester_gpa',
        'cumulative_credit_hours',
        'cumulative_gpa',
        'successful_credit_hours',
        'semester_status'
    ];

    protected $casts = [
        'semester_credit_hours' => 'decimal:1',
        'semester_gpa' => 'decimal:2',
        'cumulative_credit_hours' => 'decimal:1',
        'cumulative_gpa' => 'decimal:2',
        'successful_credit_hours' => 'decimal:1',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getSemesterStatusInArabic()
    {
        $statusMap = [
            'regular' => 'منتظم',
            'probation' => 'إنذار أكاديمي',
            'warning' => 'تحذير',
            'excellent' => 'ممتاز',
            'honor' => 'شرف'
        ];

        return $statusMap[$this->semester_status] ?? $this->semester_status;
    }
}