<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'course_code' => '0803799',
                'course_name' => 'Thesis',
                'course_name_ar' => 'الرسالة',
                'credit_hours' => 9,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803101',
                'course_name' => 'Introduction to Law',
                'course_name_ar' => 'مدخل إلى القانون',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803102',
                'course_name' => 'Constitutional Law',
                'course_name_ar' => 'القانون الدستوري',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803201',
                'course_name' => 'Civil Law',
                'course_name_ar' => 'القانون المدني',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803202',
                'course_name' => 'Criminal Law',
                'course_name_ar' => 'القانون الجنائي',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803301',
                'course_name' => 'Commercial Law',
                'course_name_ar' => 'القانون التجاري',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803302',
                'course_name' => 'Administrative Law',
                'course_name_ar' => 'القانون الإداري',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803401',
                'course_name' => 'International Law',
                'course_name_ar' => 'القانون الدولي',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '0803402',
                'course_name' => 'Labor Law',
                'course_name_ar' => 'قانون العمل',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ]
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
