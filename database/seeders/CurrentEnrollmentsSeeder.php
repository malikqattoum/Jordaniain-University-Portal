<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;

class CurrentEnrollmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First, let's create the courses from the sample data
        $courses = [
            [
                'course_code' => '01502770',
                'course_name' => 'English Preparatory Program',
                'course_name_ar' => 'البرنامج التأهيلي باللغة الانجليزية',
                'credit_hours' => 3,
                'college' => 'كلية العلوم',
                'department' => 'اللغات'
            ],
            [
                'course_code' => '0341737',
                'course_name' => 'Biostatistics',
                'course_name_ar' => 'الاحصاء الحيوي',
                'credit_hours' => 3,
                'college' => 'كلية الطب',
                'department' => 'الاحصاء الحيوي'
            ],
            [
                'course_code' => '0333715',
                'course_name' => 'Advanced Environmental Chemistry',
                'course_name_ar' => 'كيمياء البيئة المتقدمة',
                'credit_hours' => 3,
                'college' => 'كلية العلوم',
                'department' => 'الكيمياء'
            ]
        ];

        foreach ($courses as $course) {
            \App\Models\Course::create($course);
        }

        // Sample current semester enrollments for Jamila Abdullah (student_id: 1)
        $currentEnrollments = [
            [
                'student_id' => 1,
                'course_id' => 10, // English Preparatory Program
                'academic_year' => '2024',
                'semester' => 1,
                'status' => 'enrolled',
                'prerequisite' => 'محاسب',
                'nature' => 'نظري',
                'teaching_method' => 'تعلم عن بعد',
                'course_index' => 'ENG001',
                'accounting_code' => 'ACC001',
                'section' => '2',
                'section_number' => 2,
                'schedule_days' => 'السبت',
                'schedule_time' => '11:00-14:00',
                'schedule_day' => 'السبت',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'د. سارة أحمد',
                'created_at' => '2025-10-09 00:00:00'
            ],
            [
                'student_id' => 1,
                'course_id' => 11, // Biostatistics
                'academic_year' => '2024',
                'semester' => 1,
                'status' => 'enrolled',
                'prerequisite' => 'محاسب',
                'nature' => 'نظري',
                'teaching_method' => 'تعلم عن بعد',
                'course_index' => 'BIO001',
                'accounting_code' => 'ACC002',
                'section' => '1',
                'section_number' => 1,
                'schedule_days' => 'الخميس',
                'schedule_time' => '12:15-15:15',
                'schedule_day' => 'الخميس',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'د. محمد علي',
                'created_at' => '2025-10-15 00:00:00'
            ],
            [
                'student_id' => 1,
                'course_id' => 12, // Advanced Environmental Chemistry
                'academic_year' => '2024',
                'semester' => 1,
                'status' => 'enrolled',
                'prerequisite' => 'محاسب',
                'nature' => 'نظري',
                'teaching_method' => 'تعلم عن بعد',
                'course_index' => 'CHEM001',
                'accounting_code' => 'ACC003',
                'section' => '2',
                'section_number' => 2,
                'schedule_days' => 'السبت',
                'schedule_time' => '15:00-18:00',
                'schedule_day' => 'السبت',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'د. لينا حسن',
                'created_at' => '2025-10-10 00:00:00'
            ]
        ];

        foreach ($currentEnrollments as $enrollment) {
            Enrollment::create($enrollment);
        }
    }
}
