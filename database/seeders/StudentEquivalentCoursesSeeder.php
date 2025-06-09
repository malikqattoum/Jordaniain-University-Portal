<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentEquivalentCourse;

class StudentEquivalentCoursesSeeder extends Seeder
{
    public function run()
    {
        // Get the three specific students
        $students = Student::whereIn('student_id', ['20248895', '0242363', '0249597'])->get();

        if ($students->count() < 3) {
            echo "Warning: Not all students found. Please run the student seeders first.\n";
            return;
        }

        // Equivalent courses data
        $equivalentCoursesData = [
            [
                'equivalent_course_code' => '0402264',
                'equivalent_course_name' => 'علم اصول الفقه',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1000111',
                'equivalent_course_name' => 'الصياغة والتعبير القانوني',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1032171',
                'equivalent_course_name' => 'المبادئ العامة لقانون العقوبات',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1032141',
                'equivalent_course_name' => 'القانون الدولي العام',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1002162',
                'equivalent_course_name' => 'القانون الاداري: الادارة العامة تنظيمها ونشاطها',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1032281',
                'equivalent_course_name' => 'المالية العامة والتشريعات الضريبية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001121',
                'equivalent_course_name' => 'مبادئ القانون التجاري',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1031222',
                'equivalent_course_name' => 'الشركات التجارية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1031323',
                'equivalent_course_name' => 'عقدي البيع والايجار',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1032254',
                'equivalent_course_name' => 'الأوراق التجارية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1032255',
                'equivalent_course_name' => 'الجرائم الواقعة على الاشخاص',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1031424',
                'equivalent_course_name' => 'الجرائم الواقعة على الأموال',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1031428',
                'equivalent_course_name' => 'قانون الاحصاء',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1002163',
                'equivalent_course_name' => 'قانون النقل',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001313',
                'equivalent_course_name' => 'الملكية الفكرية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1002345',
                'equivalent_course_name' => 'عقود التأمين',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '0402264',
                'equivalent_course_name' => 'القانون الاداري وسائل الادارة العامة والادارة الالكترونية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001113',
                'equivalent_course_name' => 'الذكاء الاصطناعي والقانون',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001114',
                'equivalent_course_name' => 'التأمينات العينية والشخصية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001115',
                'equivalent_course_name' => 'القانون الدولي للبحار',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001116',
                'equivalent_course_name' => 'احوال شخصية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001117',
                'equivalent_course_name' => 'مصادر الالتزام الارادية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001118',
                'equivalent_course_name' => 'مصادر الالتزام غير الارادية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ],
            [
                'equivalent_course_code' => '1001119',
                'equivalent_course_name' => 'القانون الدولي الخاص',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'status' => 'معادلة'
            ]
        ];

        // Create equivalent courses for each student
        foreach ($students as $student) {
            foreach ($equivalentCoursesData as $courseData) {
                StudentEquivalentCourse::create([
                    'student_id' => $student->id,
                    'course_code' => $courseData['equivalent_course_code'],
                    'course_name' => $courseData['equivalent_course_name'],
                    'credit_hours' => $courseData['credit_hours'],
                    'status' => $courseData['status'],
                    'notes' => 'ملاحظ'
                ]);
            }
        }

        echo "Equivalent courses created for all three students.\n";
    }
}
