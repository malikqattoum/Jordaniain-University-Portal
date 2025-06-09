<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquivalentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'equivalent_course_code',
        'equivalent_course_name',
        'credit_hours',
        'college',
        'is_active'
    ];

    protected $casts = [
        'credit_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}