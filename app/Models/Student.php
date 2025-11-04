<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'student_id',
        'name',
        'name_en',
        'username',
        'email',
        'password',
        'passport_number',
        'date_of_birth',
        'national_id',
        'place_of_birth',
        'college',
        'major',
        'academic_year',
        'total_credit_hours',
        'cumulative_gpa',
        'successful_credit_hours',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'cumulative_gpa' => 'decimal:2',
        'total_credit_hours' => 'decimal:1',
        'successful_credit_hours' => 'decimal:1',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function academicRecords()
    {
        return $this->hasMany(AcademicRecord::class);
    }

    public function equivalentCourses()
    {
        return $this->hasMany(StudentEquivalentCourse::class);
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Generate username from Arabic name and student ID
     */
    public static function generateUsername($arabicName, $studentId)
    {
        // Extract first name from Arabic name
        $nameParts = explode(' ', trim($arabicName));
        $firstName = $nameParts[0];

        // Convert Arabic name to abbreviation
        $abbreviation = self::arabicNameToAbbreviation($firstName);

        return strtolower($abbreviation . $studentId);
    }

    /**
     * Convert Arabic first name to English abbreviation
     */
    private static function arabicNameToAbbreviation($arabicName)
    {
        $nameMap = [
            'جميلة' => 'jml',
            'أحمد' => 'ahd',
            'فاطمة' => 'ftm',
            'ابراهيم' => 'ibr',
            'يوسف' => 'ysf',
            'خالد' => 'khd',
            'محمد' => 'mhd',
            'علي' => 'ali',
            'عبدالله' => 'abd',
            'عبدالعزيز' => 'abz',
            'سعيدان' => 'sdn',
            'جاسم' => 'jsm',
            'حسن' => 'hsn',
            'ظافر' => 'dhf',
            'فهد' => 'fhd',
            'سمر' => 'smr'
        ];

        return $nameMap[$arabicName] ?? substr(str_replace(['ا', 'ة', 'ي', 'و'], '', $arabicName), 0, 3);
    }

    /**
     * Boot method to auto-generate username
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (!$student->username && $student->name && $student->student_id) {
                $student->username = self::generateUsername($student->name, $student->student_id);
            }
        });
    }
}
