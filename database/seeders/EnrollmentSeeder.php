<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();
        $courses = Course::all();

        // Sample enrollments for Jamila Abdullah (student_id: 20210001)
        $jamilaEnrollments = [
            // First semester 2021/2022
            [
                'student_id' => 1,
                'course_id' => 2, // Introduction to Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 3, // Constitutional Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B+',
                'grade_points' => 3.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 4, // Civil Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A-',
                'grade_points' => 3.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 5, // Criminal Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B',
                'grade_points' => 3.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 6, // Commercial Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            // Second semester 2021/2022
            [
                'student_id' => 1,
                'course_id' => 1, // Thesis
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'P',
                'grade_points' => 0.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 7, // Administrative Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'A-',
                'grade_points' => 3.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 8, // International Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'B+',
                'grade_points' => 3.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 1,
                'course_id' => 9, // Labor Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ]
        ];

        // Sample enrollments for Ahmed Ahmad (student_id: 20210002)
        $ahmedEnrollments = [
            // First semester 2021/2022
            [
                'student_id' => 2,
                'course_id' => 2, // Introduction to Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B',
                'grade_points' => 3.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 3, // Constitutional Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B-',
                'grade_points' => 2.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 4, // Civil Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'C+',
                'grade_points' => 2.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 5, // Criminal Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B+',
                'grade_points' => 3.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 6, // Commercial Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A-',
                'grade_points' => 3.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            // Second semester 2021/2022
            [
                'student_id' => 2,
                'course_id' => 7, // Administrative Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'B',
                'grade_points' => 3.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 8, // International Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'C+',
                'grade_points' => 2.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 2,
                'course_id' => 9, // Labor Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'B+',
                'grade_points' => 3.5,
                'status' => 'completed',
                'is_passed' => true
            ]
        ];

        // Sample enrollments for Fatima Salem (student_id: 20210003)
        $fatimaEnrollments = [
            // First semester 2021/2022
            [
                'student_id' => 3,
                'course_id' => 2, // Introduction to Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A+',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 3, // Constitutional Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 4, // Civil Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A-',
                'grade_points' => 3.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 5, // Criminal Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 6, // Commercial Law
                'academic_year' => '2021',
                'semester' => 1,
                'grade' => 'B+',
                'grade_points' => 3.5,
                'status' => 'completed',
                'is_passed' => true
            ],
            // Second semester 2021/2022
            [
                'student_id' => 3,
                'course_id' => 7, // Administrative Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 8, // International Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'A-',
                'grade_points' => 3.7,
                'status' => 'completed',
                'is_passed' => true
            ],
            [
                'student_id' => 3,
                'course_id' => 9, // Labor Law
                'academic_year' => '2021',
                'semester' => 2,
                'grade' => 'A',
                'grade_points' => 4.0,
                'status' => 'completed',
                'is_passed' => true
            ]
        ];

        $allEnrollments = array_merge($jamilaEnrollments, $ahmedEnrollments, $fatimaEnrollments);

        foreach ($allEnrollments as $enrollment) {
            Enrollment::create($enrollment);
        }
    }
}