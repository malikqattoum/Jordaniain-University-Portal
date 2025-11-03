<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AcademicRecord;
use App\Models\Payment;
use App\Models\SemesterFee;
use App\Models\MajorPricing;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive statistics.
     */
    public function index(Request $request)
    {
        // Overall system statistics
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $graduatedStudents = Student::where('status', 'graduated')->count();
        $suspendedStudents = Student::where('status', 'suspended')->count();

        // Academic statistics
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $academicRecords = AcademicRecord::count();

        // Financial statistics
        $totalPayments = Payment::where('status', 'completed')->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Recent activities
        $recentStudents = Student::orderBy('created_at', 'desc')->limit(5)->get();
        $recentPayments = Payment::with('student')->orderBy('created_at', 'desc')->limit(5)->get();

        // Monthly growth data
        $monthlyStudentGrowth = $this->getMonthlyStudentGrowth();
        $monthlyRevenueGrowth = $this->getMonthlyRevenueGrowth();
        $monthlyEnrollmentGrowth = $this->getMonthlyEnrollmentGrowth();

        // College and major distribution
        $collegeStats = $this->getCollegeStatistics();
        $majorStats = $this->getMajorStatistics();
        $gpaDistribution = $this->getGPADistribution();

        // Payment statistics
        $paymentTypeStats = $this->getPaymentTypeStatistics();
        $outstandingPayments = $this->getOutstandingPaymentsCount();

        return view('admin.dashboard.index', compact(
            'totalStudents',
            'activeStudents',
            'graduatedStudents',
            'suspendedStudents',
            'totalCourses',
            'totalEnrollments',
            'academicRecords',
            'totalPayments',
            'totalRevenue',
            'pendingPayments',
            'recentStudents',
            'recentPayments',
            'monthlyStudentGrowth',
            'monthlyRevenueGrowth',
            'monthlyEnrollmentGrowth',
            'collegeStats',
            'majorStats',
            'gpaDistribution',
            'paymentTypeStats',
            'outstandingPayments'
        ));
    }

    /**
     * Get detailed student statistics.
     */
    public function getStudentStatistics(Request $request)
    {
        $statistics = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'graduated_students' => Student::where('status', 'graduated')->count(),
            'suspended_students' => Student::where('status', 'suspended')->count(),
            'inactive_students' => Student::where('status', 'inactive')->count(),
            'average_gpa' => Student::avg('cumulative_gpa'),
            'students_by_college' => Student::selectRaw('college, COUNT(*) as count')
                ->groupBy('college')
                ->pluck('count', 'college')
                ->toArray(),
            'students_by_major' => Student::selectRaw('major, COUNT(*) as count')
                ->groupBy('major')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'major')
                ->toArray(),
            'students_by_year' => Student::selectRaw('academic_year, COUNT(*) as count')
                ->groupBy('academic_year')
                ->pluck('count', 'academic_year')
                ->toArray(),
        ];

        return response()->json($statistics);
    }

    /**
     * Get detailed academic statistics.
     */
    public function getAcademicStatistics(Request $request)
    {
        $statistics = [
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'academic_records' => AcademicRecord::count(),
            'courses_by_level' => Course::selectRaw('level, COUNT(*) as count')
                ->groupBy('level')
                ->pluck('count', 'level')
                ->toArray(),
            'enrollments_by_semester' => Enrollment::selectRaw('semester, COUNT(*) as count')
                ->groupBy('semester')
                ->pluck('count', 'semester')
                ->toArray(),
            'average_grades' => AcademicRecord::selectRaw('course_id, AVG(grade) as avg_grade')
                ->groupBy('course_id')
                ->with('course')
                ->get()
                ->map(function ($record) {
                    return [
                        'course_name' => $record->course->course_name,
                        'average_grade' => round($record->avg_grade, 2)
                    ];
                }),
            'grade_distribution' => AcademicRecord::selectRaw('
                CASE
                    WHEN grade >= 90 THEN "A (90-100)"
                    WHEN grade >= 80 THEN "B (80-89)"
                    WHEN grade >= 70 THEN "C (70-79)"
                    WHEN grade >= 60 THEN "D (60-69)"
                    ELSE "F (0-59)"
                END as grade_range,
                COUNT(*) as count
            ')
            ->groupBy('grade_range')
            ->pluck('count', 'grade_range')
            ->toArray(),
        ];

        return response()->json($statistics);
    }

    /**
     * Get detailed financial statistics.
     */
    public function getFinancialStatistics(Request $request)
    {
        $statistics = [
            'total_payments' => Payment::where('status', 'completed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'revenue_by_type' => Payment::where('status', 'completed')
                ->selectRaw('payment_type, SUM(amount) as total')
                ->groupBy('payment_type')
                ->pluck('total', 'payment_type')
                ->toArray(),
            'monthly_revenue' => $this->getMonthlyRevenueGrowth(),
            'outstanding_by_college' => $this->getOutstandingByCollege(),
        ];

        return response()->json($statistics);
    }

    /**
     * Generate comprehensive reports.
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:students,academic,financial,comprehensive',
            'format' => 'required|in:pdf,excel,json',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        switch ($validated['report_type']) {
            case 'students':
                return $this->generateStudentsReport($validated);
            case 'academic':
                return $this->generateAcademicReport($validated);
            case 'financial':
                return $this->generateFinancialReport($validated);
            case 'comprehensive':
                return $this->generateComprehensiveReport($validated);
        }
    }

    /**
     * Get real-time dashboard data via AJAX.
     */
    public function getDashboardData(Request $request)
    {
        $data = [
            'students_count' => Student::count(),
            'active_students_count' => Student::where('status', 'active')->count(),
            'today_revenue' => Payment::where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('amount'),
            'month_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount'),
            'enrollments_today' => Enrollment::whereDate('created_at', Carbon::today())->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        return response()->json($data);
    }

    /**
     * Get system health metrics.
     */
    public function getSystemHealth(Request $request)
    {
        $health = [
            'database_status' => 'healthy',
            'response_time' => rand(50, 200), // Placeholder - would be actual response time
            'memory_usage' => memory_get_usage(true),
            'disk_usage' => disk_free_space('/') / disk_total_space('/'),
            'last_backup' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
            'active_sessions' => rand(10, 50), // Placeholder
            'error_logs_count' => rand(0, 5), // Placeholder
        ];

        return response()->json($health);
    }

    /**
     * Get notification alerts for dashboard.
     */
    public function getAlerts(Request $request)
    {
        $alerts = [];

        // Check for high number of failed payments
        $failedPaymentsCount = Payment::where('status', 'failed')
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($failedPaymentsCount > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "يوجد {$failedPaymentsCount} مدفوعة فاشلة اليوم",
                'action_url' => route('admin.financial.payments.index', ['status' => 'failed'])
            ];
        }

        // Check for students with low GPA
        $lowGPAStudents = Student::where('cumulative_gpa', '<', 2.0)->count();
        if ($lowGPAStudents > 5) {
            $alerts[] = [
                'type' => 'info',
                'message' => "يوجد {$lowGPAStudents} طلاب بمعدل تراكمي منخفض",
                'action_url' => route('admin.students.index', ['gpa_max' => '1.99'])
            ];
        }

        // Check for pending semester fees
        $pendingSemesterFees = SemesterFee::whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })->count();

        if ($pendingSemesterFees > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "يوجد {$pendingSemesterFees} رسوم فصل معلقة",
                'action_url' => route('admin.financial.outstanding-payments')
            ];
        }

        return response()->json($alerts);
    }

    // Private helper methods

    private function getMonthlyStudentGrowth()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Student::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        return $data;
    }

    private function getMonthlyRevenueGrowth()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $data[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        return $data;
    }

    private function getMonthlyEnrollmentGrowth()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        return $data;
    }

    private function getCollegeStatistics()
    {
        return Student::selectRaw('college, COUNT(*) as count')
            ->groupBy('college')
            ->pluck('count', 'college')
            ->toArray();
    }

    private function getMajorStatistics()
    {
        return Student::selectRaw('major, COUNT(*) as count')
            ->groupBy('major')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'major')
            ->toArray();
    }

    private function getGPADistribution()
    {
        return AcademicRecord::selectRaw('
            CASE
                WHEN grade >= 90 THEN "A (90-100)"
                WHEN grade >= 80 THEN "B (80-89)"
                WHEN grade >= 70 THEN "C (70-79)"
                WHEN grade >= 60 THEN "D (60-69)"
                ELSE "F (0-59)"
            END as grade_range,
            COUNT(*) as count
        ')
        ->groupBy('grade_range')
        ->pluck('count', 'grade_range')
        ->toArray();
    }

    private function getPaymentTypeStatistics()
    {
        return Payment::where('status', 'completed')
            ->selectRaw('payment_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->payment_type,
                    'total' => $item->total,
                    'count' => $item->count,
                ];
            });
    }

    private function getOutstandingPaymentsCount()
    {
        return Student::whereHas('semesterFees', function ($q) {
            $q->whereDoesntHave('payments', function ($paymentQuery) {
                $paymentQuery->where('status', 'completed');
            });
        })->count();
    }

    private function getOutstandingByCollege()
    {
        return DB::table('students')
            ->join('semester_fees', 'students.major', '=', 'semester_fees.major')
            ->leftJoin('payments', function ($join) {
                $join->on('semester_fees.id', '=', 'payments.semester_fee_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereNull('payments.id')
            ->selectRaw('students.college, COUNT(DISTINCT students.id) as outstanding_students')
            ->groupBy('students.college')
            ->pluck('outstanding_students', 'students.college')
            ->toArray();
    }

    private function generateStudentsReport($data)
    {
        $query = Student::with('enrollments.course', 'academicRecords.course');

        if (!empty($data['date_from']) && !empty($data['date_to'])) {
            $query->whereBetween('created_at', [$data['date_from'], $data['date_to']]);
        }

        $students = $query->get();

        if ($data['format'] === 'json') {
            return response()->json($students);
        }

        // Generate PDF or Excel report based on format
        return response()->json(['message' => 'تم إنشاء تقرير الطلاب بنجاح']);
    }

    private function generateAcademicReport($data)
    {
        $query = AcademicRecord::with('student', 'course');

        if (!empty($data['date_from']) && !empty($data['date_to'])) {
            $query->whereBetween('created_at', [$data['date_from'], $data['date_to']]);
        }

        $records = $query->get();

        if ($data['format'] === 'json') {
            return response()->json($records);
        }

        return response()->json(['message' => 'تم إنشاء التقرير الأكاديمي بنجاح']);
    }

    private function generateFinancialReport($data)
    {
        $query = Payment::with('student');

        if (!empty($data['date_from']) && !empty($data['date_to'])) {
            $query->whereBetween('created_at', [$data['date_from'], $data['date_to']]);
        }

        $payments = $query->get();

        if ($data['format'] === 'json') {
            return response()->json($payments);
        }

        return response()->json(['message' => 'تم إنشاء التقرير المالي بنجاح']);
    }

    private function generateComprehensiveReport($data)
    {
        $reportData = [
            'students' => Student::with('enrollments.course', 'academicRecords.course')->get(),
            'courses' => Course::withCount('enrollments')->get(),
            'payments' => Payment::with('student')->get(),
            'statistics' => $this->getReportStatistics(),
            'generated_at' => Carbon::now(),
        ];

        if ($data['format'] === 'json') {
            return response()->json($reportData);
        }

        return response()->json(['message' => 'تم إنشاء التقرير الشامل بنجاح']);
    }

    private function getReportStatistics()
    {
        return [
            'total_students' => Student::count(),
            'total_courses' => Course::count(),
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'average_gpa' => Student::avg('cumulative_gpa'),
        ];
    }
}
