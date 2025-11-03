<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\MajorPricing;
use App\Models\SemesterFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        // Get payment history
        $payments = Payment::with('student')
            ->byStudent($student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate financial summary
        $financialSummary = $this->calculateFinancialSummary($student);

        return view('payment-history', compact('student', 'payments', 'financialSummary'));
    }

    public function show($id)
    {
        $student = Auth::guard('student')->user();

        $payment = Payment::byStudent($student->id)->findOrFail($id);

        return view('payment-receipt', compact('student', 'payment'));
    }

    private function calculateFinancialSummary($student)
    {
        $currentAcademicYear = $student->academic_year;

        // Get current semester enrollments
        $currentEnrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('academic_year', $currentAcademicYear)
            ->get();

        $currentCreditHours = $currentEnrollments->sum(function($enrollment) {
            return $enrollment->course->credit_hours ?? 0;
        });

        // Get current semester fees
        $semesterFee = SemesterFee::getActive();
        $currentSemesterFees = $semesterFee ? $semesterFee->semester_fees : 0;

        // Calculate current tuition
        $majorPricing = MajorPricing::findByMajorName($student->major);
        $hourlyRate = $majorPricing ? $majorPricing->hourly_rate : 30.00;
        $currentTuition = $currentCreditHours * $hourlyRate;

        // Calculate current total due
        $currentTotalDue = $currentTuition + $currentSemesterFees;

        // Get payments made for current semester
        $currentSemesterPayments = Payment::completed()
            ->byStudent($student->id)
            ->byAcademicYear($currentAcademicYear)
            ->where('semester_name', $semesterFee ? $semesterFee->semester_name : null)
            ->sum('amount_paid');

        $currentBalance = max(0, $currentTotalDue - $currentSemesterPayments);

        // Get all previous unpaid balances
        $previousUnpaidBalance = $this->calculatePreviousUnpaidBalance($student);

        // Total outstanding balance
        $totalOutstandingBalance = $currentBalance + $previousUnpaidBalance;

        // Total paid amount (all time)
        $totalPaidAmount = Payment::completed()->byStudent($student->id)->sum('amount_paid');

        // Payments by academic year
        $paymentsByYear = Payment::completed()
            ->byStudent($student->id)
            ->selectRaw('academic_year, SUM(amount_paid) as total_paid')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'desc')
            ->get();

        return [
            'current_semester' => [
                'credit_hours' => $currentCreditHours,
                'hourly_rate' => $hourlyRate,
                'tuition_amount' => $currentTuition,
                'semester_fees' => $currentSemesterFees,
                'total_due' => $currentTotalDue,
                'payments_made' => $currentSemesterPayments,
                'balance_due' => $currentBalance,
                'semester_name' => $semesterFee ? $semesterFee->semester_name : 'N/A'
            ],
            'previous_unpaid_balance' => $previousUnpaidBalance,
            'total_outstanding_balance' => $totalOutstandingBalance,
            'total_paid_amount' => $totalPaidAmount,
            'payments_by_year' => $paymentsByYear,
            'registration_hold' => $totalOutstandingBalance > 0
        ];
    }

    private function calculatePreviousUnpaidBalance($student)
    {
        $allAcademicYears = Enrollment::where('student_id', $student->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->reverse();

        $totalUnpaid = 0;

        foreach ($allAcademicYears as $academicYear) {
            if ($academicYear === $student->academic_year) {
                continue; // Skip current year as it's handled separately
            }

            // Get enrollments for this year
            $enrollments = Enrollment::with('course')
                ->where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->get();

            $creditHours = $enrollments->sum(function($enrollment) {
                return $enrollment->course->credit_hours ?? 0;
            });

            // Get semester fees for this year
            $semesterFees = SemesterFee::findByAcademicYear($academicYear);
            $totalSemesterFees = $semesterFees ? $semesterFees->sum('semester_fees') : 0;

            // Calculate total due for this year
            $majorPricing = MajorPricing::findByMajorName($student->major);
            $hourlyRate = $majorPricing ? $majorPricing->hourly_rate : 30.00;
            $tuitionAmount = $creditHours * $hourlyRate;
            $totalDue = $tuitionAmount + $totalSemesterFees;

            // Get payments made for this year
            $paymentsMade = Payment::completed()
                ->byStudent($student->id)
                ->byAcademicYear($academicYear)
                ->sum('amount_paid');

            // Add unpaid balance
            $unpaidBalance = max(0, $totalDue - $paymentsMade);
            $totalUnpaid += $unpaidBalance;
        }

        return $totalUnpaid;
    }
}
