<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicRecord;

class AcademicRecordSeeder extends Seeder
{
    public function run()
    {
        $academicRecords = [
            // Jamila Abdullah records
            [
                'student_id' => 1,
                'academic_year' => '2021',
                'semester' => 1,
                'semester_credit_hours' => 15.0,
                'semester_gpa' => 3.64,
                'cumulative_credit_hours' => 15.0,
                'cumulative_gpa' => 3.64,
                'successful_credit_hours' => 15.0,
                'semester_status' => 'regular'
            ],
            [
                'student_id' => 1,
                'academic_year' => '2021',
                'semester' => 2,
                'semester_credit_hours' => 18.0,
                'semester_gpa' => 3.73,
                'cumulative_credit_hours' => 33.0,
                'cumulative_gpa' => 3.69,
                'successful_credit_hours' => 33.0,
                'semester_status' => 'regular'
            ],
            // Ahmed Ahmad records
            [
                'student_id' => 2,
                'academic_year' => '2021',
                'semester' => 1,
                'semester_credit_hours' => 15.0,
                'semester_gpa' => 3.08,
                'cumulative_credit_hours' => 15.0,
                'cumulative_gpa' => 3.08,
                'successful_credit_hours' => 15.0,
                'semester_status' => 'regular'
            ],
            [
                'student_id' => 2,
                'academic_year' => '2021',
                'semester' => 2,
                'semester_credit_hours' => 9.0,
                'semester_gpa' => 3.0,
                'cumulative_credit_hours' => 24.0,
                'cumulative_gpa' => 3.05,
                'successful_credit_hours' => 24.0,
                'semester_status' => 'regular'
            ],
            // Fatima Salem records
            [
                'student_id' => 3,
                'academic_year' => '2021',
                'semester' => 1,
                'semester_credit_hours' => 15.0,
                'semester_gpa' => 3.84,
                'cumulative_credit_hours' => 15.0,
                'cumulative_gpa' => 3.84,
                'successful_credit_hours' => 15.0,
                'semester_status' => 'excellent'
            ],
            [
                'student_id' => 3,
                'academic_year' => '2021',
                'semester' => 2,
                'semester_credit_hours' => 9.0,
                'semester_gpa' => 3.9,
                'cumulative_credit_hours' => 24.0,
                'cumulative_gpa' => 3.86,
                'successful_credit_hours' => 24.0,
                'semester_status' => 'excellent'
            ]
        ];

        foreach ($academicRecords as $record) {
            AcademicRecord::create($record);
        }
    }
}