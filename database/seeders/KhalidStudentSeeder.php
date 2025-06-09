<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AcademicRecord;

class KhalidStudentSeeder extends Seeder
{
    public function run()
    {
        // Create Khalid student
        $khalid = Student::create([
            'student_id' => '20248895',
            'name' => 'خالد سعيدان حسن ظافر المعجبه',
            'name_en' => 'KHALID SAEDAN HD AL-MEJABA',
            'email' => 'khalid.ajaba@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => '01764315',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2024/2025',
            'total_credit_hours' => 21.0,
            'cumulative_gpa' => 0.0, // Will be calculated
            'successful_credit_hours' => 21.0,
            'status' => 'active'
        ]);

        // Get course IDs for enrollments (courses should already exist from previous seeder)
        $courseIds = Course::whereIn('course_code', [
            '1001327', '1002491', '1001393', '1031231',
            '1002494', '1031415', '1001316'
        ])->pluck('id', 'course_code');

        // Khalid's enrollments (Second semester 2024/2025)
        $khalidGrades = [
            '1001327' => ['grade' => 'C+', 'points' => 2.5, 'mark' => 25], // قوانين المنافسة
            '1002491' => ['grade' => 'B-', 'points' => 2.7, 'mark' => 27], // اجراءات التحقيق
            '1001393' => ['grade' => 'C+', 'points' => 2.5, 'mark' => 24], // الاحكام وطرق الطعن
            '1031231' => ['grade' => 'B-', 'points' => 2.7, 'mark' => 27], // مصطلحات انجليزية
            '1002494' => ['grade' => 'B', 'points' => 3.0, 'mark' => 26],  // تطبيقات قضائية
            '1031415' => ['grade' => 'C-', 'points' => 1.7, 'mark' => 22], // الحقوق العينية
            '1001316' => ['grade' => 'D', 'points' => 1.0, 'mark' => 20]   // قانون العمل
        ];

        foreach ($khalidGrades as $courseCode => $gradeData) {
            Enrollment::create([
                'student_id' => $khalid->id,
                'course_id' => $courseIds[$courseCode],
                'academic_year' => '2024',
                'semester' => 2,
                'grade' => $gradeData['grade'],
                'grade_points' => $gradeData['points'],
                'status' => 'completed',
                'is_passed' => true
            ]);
        }

        // Calculate and create academic record
        $this->createAcademicRecord($khalid, '2024', 2, $khalidGrades);
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

        echo "Created academic record for {$student->name}:\n";
        echo "- Semester GPA: " . round($semesterGpa, 2) . "\n";
        echo "- Semester Status: {$semesterStatus}\n";
        echo "- Credit Hours: {$totalCreditHours}\n\n";
    }
}
