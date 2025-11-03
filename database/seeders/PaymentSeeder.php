<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SemesterFee;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some students
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->info('No students found. Please seed students first.');
            return;
        }

        $semesterFees = SemesterFee::all();

        foreach ($students->take(3) as $student) {
            // Create some historical payments for previous semesters
            $this->createSamplePayments($student, $semesterFees);
        }
    }

    private function createSamplePayments($student, $semesterFees)
    {
        $academicYears = ['2023/2024', '2024/2025'];

        foreach ($academicYears as $academicYear) {
            // Create 1-2 payments per academic year
            $paymentCount = rand(1, 2);

            for ($i = 0; $i < $paymentCount; $i++) {
                $semesterFee = $semesterFees->where('academic_year', $academicYear)->first();

                if (!$semesterFee) {
                    continue;
                }

                // Simulate different payment amounts (partial or full payments)
                $baseAmount = rand(300, 800); // Random amount between $300-$800

                Payment::create([
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'semester_name' => $semesterFee->semester_name,
                    'amount_paid' => $baseAmount,
                    'tuition_amount' => $baseAmount * 0.7, // 70% tuition
                    'semester_fees_amount' => $baseAmount * 0.3, // 30% semester fees
                    'payment_method' => 'credit_card',
                    'card_type' => rand(0, 1) ? 'local' : 'international',
                    'processing_fee' => ($baseAmount * 0.0055) + 0.09, // Local card fee
                    'receipt_number' => Payment::generateReceiptNumber(),
                    'status' => 'completed',
                    'notes' => 'Online payment via student portal',
                    'payment_details' => [
                        'ip_address' => '192.168.1.' . rand(100, 200),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'transaction_id' => 'TXN' . rand(1000000, 9999999)
                    ],
                    'created_at' => now()->subMonths(rand(1, 6))
                ]);
            }
        }

        // Add a recent payment for current semester (partial payment)
        $currentSemesterFee = $semesterFees->where('is_active', true)->first();

        if ($currentSemesterFee) {
            Payment::create([
                'student_id' => $student->id,
                'academic_year' => '2024/2025',
                'semester_name' => $currentSemesterFee->semester_name,
                'amount_paid' => 500.00, // Partial payment
                'tuition_amount' => 350.00,
                'semester_fees_amount' => 150.00,
                'payment_method' => 'credit_card',
                'card_type' => 'local',
                'processing_fee' => 2.84,
                'receipt_number' => Payment::generateReceiptNumber(),
                'status' => 'completed',
                'notes' => 'Partial payment for Fall 2024 semester',
                'payment_details' => [
                    'ip_address' => '192.168.1.150',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'transaction_id' => 'TXN' . rand(1000000, 9999999)
                ],
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }
}
