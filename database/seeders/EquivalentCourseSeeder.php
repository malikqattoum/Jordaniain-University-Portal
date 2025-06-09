<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquivalentCourse;

class EquivalentCourseSeeder extends Seeder
{
    public function run()
    {
        $equivalentCourses = [
            [
                'course_id' => 2, // Introduction to Law
                'equivalent_course_code' => '0803100',
                'equivalent_course_name' => 'أساسيات القانون',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 3, // Constitutional Law
                'equivalent_course_code' => '0803103',
                'equivalent_course_name' => 'النظم الدستورية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 4, // Civil Law
                'equivalent_course_code' => '0803200',
                'equivalent_course_name' => 'الأحوال الشخصية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 5, // Criminal Law
                'equivalent_course_code' => '0803203',
                'equivalent_course_name' => 'قانون العقوبات',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 6, // Commercial Law
                'equivalent_course_code' => '0803300',
                'equivalent_course_name' => 'قانون الشركات',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 7, // Administrative Law
                'equivalent_course_code' => '0803303',
                'equivalent_course_name' => 'القانون الإداري المقارن',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 8, // International Law
                'equivalent_course_code' => '0803400',
                'equivalent_course_name' => 'القانون الدولي العام',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ],
            [
                'course_id' => 9, // Labor Law
                'equivalent_course_code' => '0803403',
                'equivalent_course_name' => 'قانون الضمان الاجتماعي',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق'
            ]
        ];

        foreach ($equivalentCourses as $equivalent) {
            EquivalentCourse::create($equivalent);
        }
    }
}