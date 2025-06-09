<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AcademicRecord;

class NewStudentsSeeder extends Seeder
{
    public function run()
    {
        // Create the two new students
        $ibrahim = Student::create([
            'student_id' => '0242363',
            'name' => 'ابراهيم جاسم ابراهيم حسين القطان',
            'name_en' => 'IBRAHIM JASSIM I H AL-QATTAN',
            'username' => 'ibr0242363',
            'email' => 'ibrahim.qattan@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => '01790892',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2024/2025',
            'total_credit_hours' => 21.0,
            'cumulative_gpa' => 0.0, // Will be calculated
            'successful_credit_hours' => 21.0,
            'status' => 'active'
        ]);

        $youssef = Student::create([
            'student_id' => '0249597',
            'name' => 'يوسف عبدالعزيز احمد عبدالعزيز المهيزع',
            'name_en' => 'YOUSEF ABDULAZIZ A A AL-MUHAIZA',
            'username' => 'ysf0249597',
            'email' => 'youssef.mahizaa@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => '01794101',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2024/2025',
            'total_credit_hours' => 21.0,
            'cumulative_gpa' => 0.0, // Will be calculated
            'successful_credit_hours' => 21.0,
            'status' => 'active'
        ]);

        // Create the new courses if they don't exist
        $courses = [
            [
                'course_code' => '1001327',
                'course_name' => 'Competition Laws',
                'course_name_ar' => 'قوانين المنافسة',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1002491',
                'course_name' => 'Pre-Trial Investigation Procedures',
                'course_name_ar' => 'اجراءات التحقيق ما قبل المحاكمة',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1001393',
                'course_name' => 'Civil Judgments and Appeal Methods',
                'course_name_ar' => 'الاحكام وطرق الطعن المدنية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1031231',
                'course_name' => 'Legal Terms and Texts in English',
                'course_name_ar' => 'مصطلحات ونصوص قانونية باللغة الانجليزية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1002494',
                'course_name' => 'Judicial Applications',
                'course_name_ar' => 'تطبيقات قضائية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1031415',
                'course_name' => 'Real Rights',
                'course_name_ar' => 'الحقوق العينية',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ],
            [
                'course_code' => '1001316',
                'course_name' => 'Labor Law and Social Security',
                'course_name_ar' => 'قانون العمل والضمان الاجتماعي',
                'credit_hours' => 3,
                'college' => 'كلية الحقوق',
                'department' => 'القانون'
            ]
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['course_code' => $courseData['course_code']],
                $courseData
            );
        }

        // Get course IDs for enrollments
        $courseIds = Course::whereIn('course_code', [
            '1001327', '1002491', '1001393', '1031231',
            '1002494', '1031415', '1001316'
        ])->pluck('id', 'course_code');

        // Ibrahim's enrollments (Second semester 2024/2025)
        $ibrahimGrades = [
            '1001327' => ['grade' => 'C-', 'points' => 1.7, 'mark' => 22], // قوانين المنافسة
            '1002491' => ['grade' => 'D', 'points' => 1.0, 'mark' => 20],  // اجراءات التحقيق
            '1001393' => ['grade' => 'D+', 'points' => 1.5, 'mark' => 21], // الاحكام وطرق الطعن
            '1031231' => ['grade' => 'C', 'points' => 2.0, 'mark' => 23],  // مصطلحات انجليزية
            '1002494' => ['grade' => 'C+', 'points' => 2.5, 'mark' => 24], // تطبيقات قضائية
            '1031415' => ['grade' => 'B-', 'points' => 2.7, 'mark' => 27], // الحقوق العينية
            '1001316' => ['grade' => 'B', 'points' => 3.0, 'mark' => 26]   // قانون العمل
        ];

        foreach ($ibrahimGrades as $courseCode => $gradeData) {
            Enrollment::create([
                'student_id' => $ibrahim->id,
                'course_id' => $courseIds[$courseCode],
                'academic_year' => '2024',
                'semester' => 2,
                'grade' => $gradeData['grade'],
                'grade_points' => $gradeData['points'],
                'status' => 'completed',
                'is_passed' => true
            ]);
        }

        // Youssef's enrollments (Second semester 2024/2025)
        $youssefGrades = [
            '1001327' => ['grade' => 'D', 'points' => 1.0, 'mark' => 20],  // قوانين المنافسة
            '1002491' => ['grade' => 'C', 'points' => 2.0, 'mark' => 23],  // اجراءات التحقيق
            '1001393' => ['grade' => 'C-', 'points' => 1.7, 'mark' => 22], // الاحكام وطرق الطعن
            '1031231' => ['grade' => 'D+', 'points' => 1.5, 'mark' => 21], // مصطلحات انجليزية
            '1002494' => ['grade' => 'C+', 'points' => 2.5, 'mark' => 25], // تطبيقات قضائية
            '1031415' => ['grade' => 'D+', 'points' => 1.5, 'mark' => 21], // الحقوق العينية
            '1001316' => ['grade' => 'B', 'points' => 3.0, 'mark' => 28]   // قانون العمل
        ];

        foreach ($youssefGrades as $courseCode => $gradeData) {
            Enrollment::create([
                'student_id' => $youssef->id,
                'course_id' => $courseIds[$courseCode],
                'academic_year' => '2024',
                'semester' => 2,
                'grade' => $gradeData['grade'],
                'grade_points' => $gradeData['points'],
                'status' => 'completed',
                'is_passed' => true
            ]);
        }

        // Calculate and create academic records
        $this->createAcademicRecord($ibrahim, '2024', 2, $ibrahimGrades);
        $this->createAcademicRecord($youssef, '2024', 2, $youssefGrades);
    }

    private function createAcademicRecord($student, $year, $semester, $grades)
    {
        $totalCreditHours = count($grades) * 3; // Each course is 3 credit hours
        $totalGradePoints = 0;

        foreach ($grades as $gradeData) {
            $totalGradePoints += ($gradeData['points'] * 3);
        }

        $semesterGpa = $totalGradePoints / $totalCreditHours;

        // Determine semester status
        $semesterStatus = 'regular';
        if ($semesterGpa >= 3.75) {
            $semesterStatus = 'excellent';
        } elseif ($semesterGpa >= 3.5) {
            $semesterStatus = 'honor';
        } elseif ($semesterGpa < 2.0) {
            $semesterStatus = 'probation';
        } elseif ($semesterGpa < 2.5) {
            $semesterStatus = 'warning';
        }

        AcademicRecord::create([
            'student_id' => $student->id,
            'academic_year' => $year,
            'semester' => $semester,
            'semester_credit_hours' => $totalCreditHours,
            'semester_gpa' => round($semesterGpa, 2),
            'cumulative_credit_hours' => $totalCreditHours,
            'cumulative_gpa' => round($semesterGpa, 2),
            'successful_credit_hours' => $totalCreditHours,
            'semester_status' => $semesterStatus
        ]);

        // Update student's cumulative data
        $student->update([
            'total_credit_hours' => $totalCreditHours,
            'cumulative_gpa' => round($semesterGpa, 2),
            'successful_credit_hours' => $totalCreditHours
        ]);
    }
}
