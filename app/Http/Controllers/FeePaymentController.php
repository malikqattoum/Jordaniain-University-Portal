<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\MajorPricing;
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

        // Calculate base amount
        $baseAmount = $currentCreditHours * $majorPricing['hourly_rate'];

        // Calculate payment processing fees
        $paymentFees = $this->calculatePaymentFees($baseAmount);

        // Calculate total amount due (using average of local and international fees for display)
        $averageFees = ($paymentFees['local']['amount'] + $paymentFees['international']['amount']) / 2;
        $totalAmountDue = $baseAmount + $averageFees;

        // Prepare fee breakdown
        $feeBreakdown = [
            'credit_hours' => $currentCreditHours,
            'hourly_rate' => $majorPricing['hourly_rate'],
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
                'currency' => $majorPricing->currency
            ];
        }

        // If not found in database, fall back to config file
        $configPricing = config('major_pricing');
        $majorKey = strtolower(trim($major));
        $defaultKey = 'law';

        // Try to find exact match first
        if (isset($configPricing[$majorKey])) {
            return $configPricing[$majorKey];
        }

        // Try partial matching for common variations
        foreach ($configPricing as $key => $data) {
            if (strpos($majorKey, $key) !== false || strpos($key, $majorKey) !== false) {
                return $data;
            }
        }

        // Return default (Law)
        return $configPricing[$defaultKey];
    }

    /**
     * Calculate payment processing fees
     */
    private function calculatePaymentFees($baseAmount)
    {
        // Local cards: 0.55% + 0.09 fixed fee
        $localPercentage = 0.0055;
        $localFixedFee = 0.09;
        $localTotalFees = ($baseAmount * $localPercentage) + $localFixedFee;

        // International cards: 1.95% + 0.09 fixed fee
        $internationalPercentage = 0.0195;
        $internationalFixedFee = 0.09;
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
