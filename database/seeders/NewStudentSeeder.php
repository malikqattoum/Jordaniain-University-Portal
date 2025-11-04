<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Payment;

class NewStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if student already exists
        $student = Student::where('student_id', '0259244')->first();

        if ($student) {
            $this->command->info('Student already exists: ' . $student->name . ' (ID: ' . $student->student_id . ')');
        } else {
            // Create the new student: سمر بنت صالح بن محمد الربيش
            $student = Student::create([
                'student_id' => '0259244',
                'name' => 'سمر بنت صالح بن محمد الربيش',
                'name_en' => 'SUMAR BINT SALIH BIN MOHAMMED AL-RUBEISH',
                'email' => 'sumar.alrubeish@ju.edu.jo',
                'password' => Hash::make('password123'),
                'passport_number' => null,
                'date_of_birth' => '1989-06-07',
                'national_id' => '1066249846',
                'place_of_birth' => 'سكاكا الجوف',
                'college' => 'العلوم\الجيولوجيا',
                'major' => 'علوم البيئة وادارتها',
                'academic_year' => '2025/2026',
                'total_credit_hours' => 0.0,
                'cumulative_gpa' => 0.00,
                'successful_credit_hours' => 0.0,
                'status' => 'active'
            ]);

            $this->command->info('New student created: ' . $student->name . ' (ID: ' . $student->student_id . ')');
        }

        // Create enrollments for the student
        $this->createStudentEnrollments($student);

        // Create payment record for the student
        $this->createStudentPayment($student);
    }

    private function createStudentEnrollments($student)
    {
        // Get course IDs
        $course1 = Course::where('course_code', '01502770')->first(); // البرنامج التأهيلي باللغة الانجليزية
        $course2 = Course::where('course_code', '0341737')->first();  // الاحصاء الحيوي
        $course3 = Course::where('course_code', '0333715')->first();  // كيمياء البيئة المتقدمة

        if (!$course1 || !$course2 || !$course3) {
            $this->command->error('Required courses not found in database');
            return;
        }

        // Check existing enrollments
        $existingEnrollments = Enrollment::where('student_id', $student->id)
            ->where('academic_year', '2025/2026')
            ->where('semester', 1)
            ->count();

        if ($existingEnrollments >= 3) {
            $this->command->info('Enrollments already exist for student ' . $student->student_id);
            return;
        }

        // Create enrollments based on the provided data
        $enrollments = [
            [
                'course_id' => $course1->id,
                'academic_year' => '2025/2026',
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
                'schedule_time' => '1100-1400',
                'schedule_day' => 'السبت',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'البرنامج التأهيلي باللغة الانجليزية',
                'created_at' => '2025-10-09 00:00:00'
            ],
            [
                'course_id' => $course2->id,
                'academic_year' => '2025/2026',
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
                'schedule_time' => '1215-1515',
                'schedule_day' => 'الخميس',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'الاحصاء الحيوي',
                'created_at' => '2025-10-15 00:00:00'
            ],
            [
                'course_id' => $course3->id,
                'academic_year' => '2025/2026',
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
                'schedule_time' => '1500-1700',
                'schedule_day' => 'السبت',
                'is_in_person' => false,
                'room' => 'غير محدد',
                'instructor_name' => 'كيمياء البيئة المتقدمة',
                'created_at' => '2025-10-15 00:00:00'
            ]
        ];

        $createdCount = 0;
        foreach ($enrollments as $enrollmentData) {
            // Check if this specific enrollment already exists
            $existing = Enrollment::where('student_id', $student->id)
                ->where('course_id', $enrollmentData['course_id'])
                ->where('academic_year', $enrollmentData['academic_year'])
                ->where('semester', $enrollmentData['semester'])
                ->first();

            if (!$existing) {
                $enrollmentData['student_id'] = $student->id;
                Enrollment::create($enrollmentData);
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->command->info('Created ' . $createdCount . ' enrollments for student ' . $student->student_id);
        } else {
            $this->command->info('All enrollments already exist for student ' . $student->student_id);
        }
    }

    private function createStudentPayment($student)
    {
        // Use the original receipt amount of $3,750.00
        // The calculation logic will handle showing 0 balance for large payments
        $originalAmount = 3750.00;

        // Check if payment already exists
        $existingPayment = Payment::where('student_id', $student->id)
            ->where('receipt_number', '01869993')
            ->first();

        if ($existingPayment) {
            // Update existing payment back to original amount
            $existingPayment->update([
                'amount_paid' => $originalAmount,
                'tuition_amount' => 2625.00, // 70% of 3750
                'semester_fees_amount' => 1125.00, // 30% of 3750
                'processing_fee' => 20.63,
                'payment_details' => [
                    'account_number' => '0203004',
                    'payment_description' => 'ثالثة آالف وسبعمائة وخمسون دوالر أمريكي فقط ال غير',
                    'date' => '2025-10-09'
                ]
            ]);
            $this->command->info('Updated payment record for student ' . $student->student_id . ' back to original $3,750.00 amount');
            return;
        }

        // Create payment record with the original receipt amount
        $payment = Payment::create([
            'student_id' => $student->id,
            'academic_year' => '2025/2026',
            'semester_name' => 'First Semester 2025/2026',
            'amount_paid' => $originalAmount, // Original receipt amount
            'tuition_amount' => 2625.00, // 70% of 3750
            'semester_fees_amount' => 1125.00, // 30% of 3750
            'payment_method' => 'credit_card',
            'card_type' => 'local',
            'processing_fee' => 20.63, // Sample processing fee
            'receipt_number' => '01869993',
            'status' => 'completed',
            'notes' => 'رسوم تسجيل مواد فصل اول دولي 2026/2025',
            'payment_details' => [
                'account_number' => '0203004',
                'payment_description' => 'ثالثة آالف وسبعمائة وخمسون دوالر أمريكي فقط ال غير',
                'date' => '2025-10-09'
            ],
            'created_at' => '2025-10-09 00:00:00'
        ]);

        $this->command->info('Created payment record for student ' . $student->student_id . ' (Receipt: ' . $payment->receipt_number . ')');
    }
}
