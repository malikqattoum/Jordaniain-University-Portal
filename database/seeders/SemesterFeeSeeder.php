<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemesterFee;

class SemesterFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $semesterFeesData = [
            [
                'semester_name' => 'Fall 2024',
                'semester_fees' => 370.00,
                'academic_year' => '2024/2025',
                'is_active' => true,
                'description' => 'Fall semester fees for academic year 2024/2025'
            ],
            [
                'semester_name' => 'Spring 2025',
                'semester_fees' => 370.00,
                'academic_year' => '2024/2025',
                'is_active' => false,
                'description' => 'Spring semester fees for academic year 2024/2025'
            ],
            [
                'semester_name' => 'Summer 2025',
                'semester_fees' => 250.00,
                'academic_year' => '2024/2025',
                'is_active' => false,
                'description' => 'Summer semester fees for academic year 2024/2025'
            ],
            [
                'semester_name' => 'Fall 2025',
                'semester_fees' => 390.00,
                'academic_year' => '2025/2026',
                'is_active' => false,
                'description' => 'Fall semester fees for academic year 2025/2026 (projected)'
            ]
        ];

        foreach ($semesterFeesData as $data) {
            SemesterFee::updateOrCreate(
                ['semester_name' => $data['semester_name'], 'academic_year' => $data['academic_year']],
                $data
            );
        }
    }
}
