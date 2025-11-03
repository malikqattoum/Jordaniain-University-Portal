<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_name',
        'semester_fees',
        'academic_year',
        'is_active',
        'description'
    ];

    protected $casts = [
        'semester_fees' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active semester fees
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the current active semester fee
     */
    public static function getActive()
    {
        return static::active()->first();
    }

    /**
     * Find semester fee by academic year
     */
    public static function findByAcademicYear($academicYear)
    {
        return static::where('academic_year', $academicYear)->get();
    }

    /**
     * Get all active semester fees
     */
    public static function getActiveFees()
    {
        return static::active()->orderBy('academic_year', 'desc')->get();
    }
}
