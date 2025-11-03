<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\MajorPricing;
use App\Models\SemesterFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $student = Auth::guard('student')->user();

        // Get current semester enrollments to calculate credit hours
        $currentEnrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('academic_year', $student->academic_year)
            ->get();

        // Calculate current semester credit hours
        $currentCreditHours = $currentEnrollments->sum(function($enrollment) {
            return $enrollment->course->credit_hours ?? 0;
        });

        // Get major pricing configuration
        $majorPricing = $this->getMajorPricing($student->major);

        // Calculate tuition amount (credit hours × hourly rate)
        $tuitionAmount = $currentCreditHours * $majorPricing['hourly_rate'];

        // Get current semester fees
        $semesterFee = SemesterFee::getActive();
        $semesterFeesAmount = $semesterFee ? $semesterFee->semester_fees : 0;

        // Calculate total base amount (tuition + semester fees)
        $baseAmount = $tuitionAmount + $semesterFeesAmount;

        // Calculate payment processing fees
        $paymentFees = $this->calculatePaymentFees($baseAmount);

        // Calculate total amount due (using average of local and international fees for display)
        $averageFees = ($paymentFees['local']['amount'] + $paymentFees['international']['amount']) / 2;
        $totalAmountDue = $baseAmount + $averageFees;

        // Prepare fee breakdown
        $feeBreakdown = [
            'credit_hours' => $currentCreditHours,
            'hourly_rate' => $majorPricing['hourly_rate'],
            'tuition_amount' => $tuitionAmount,
            'semester_fees_amount' => $semesterFeesAmount,
            'semester_fee_info' => $semesterFee,
            'base_amount' => $baseAmount,
            'payment_fees' => $paymentFees,
            'total_amount_due' => $totalAmountDue,
            'major_info' => $majorPricing
        ];

        return view('fee-payment', compact('student', 'feeBreakdown'));
    }

    /**
     * Get pricing information for student's major
     */
    private function getMajorPricing($major)
    {
        // Try to find pricing by major name from database first
        $majorPricing = MajorPricing::findByMajorName($major);

        if ($majorPricing) {
            // Return database pricing as array format
            return [
                'name' => $majorPricing->major_name,
                'hourly_rate' => $majorPricing->hourly_rate,
                'currency' => 'JOD'
            ];
        }

        // If not found in database, fall back to config file
        $configPricing = config('major_pricing');
        $majorKey = strtolower(trim($major));
        $defaultKey = 'law';

        // Try to find exact match first
        if (isset($configPricing[$majorKey])) {
            $configPricing[$majorKey]['currency'] = 'JOD';
            return $configPricing[$majorKey];
        }

        // Try partial matching for common variations
        foreach ($configPricing as $key => $data) {
            if (strpos($majorKey, $key) !== false || strpos($key, $majorKey) !== false) {
                $data['currency'] = 'JOD';
                return $data;
            }
        }

        // Return default (Law)
        $configPricing[$defaultKey]['currency'] = 'JOD';
        return $configPricing[$defaultKey];
    }

    /**
     * Calculate payment processing fees (in Jordanian Dinars)
     */
    private function calculatePaymentFees($baseAmount)
    {
        // Local cards: 0.55% + 1.30 JOD fixed fee
        $localPercentage = 0.0055;
        $localFixedFee = 1.30; // 1.3 JOD fixed fee
        $localTotalFees = ($baseAmount * $localPercentage) + $localFixedFee;

        // International cards: 1.95% + 1.30 JOD fixed fee
        $internationalPercentage = 0.0195;
        $internationalFixedFee = 1.30; // 1.3 JOD fixed fee
        $internationalTotalFees = ($baseAmount * $internationalPercentage) + $internationalFixedFee;

        return [
            'local' => [
                'percentage' => $localPercentage,
                'fixed_fee' => $localFixedFee,
                'amount' => $localTotalFees,
                'description' => 'البطاقات المحلية'
            ],
            'international' => [
                'percentage' => $internationalPercentage,
                'fixed_fee' => $internationalFixedFee,
                'amount' => $internationalTotalFees,
                'description' => 'البطاقات الدولية'
            ]
        ];
    }
}
