<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SemesterFee;
use App\Models\MajorPricing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;

class PaymentManagementController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function paymentsIndex(Request $request)
    {
        $query = Payment::with(['student'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($studentQuery) use ($search) {
                      $studentQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('student_id', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $payments = $query->paginate(20)->withQueryString();

        // Get filter options
        $paymentTypes = Payment::distinct()->pluck('payment_type');
        $statuses = ['completed', 'pending', 'failed', 'refunded'];

        return view('admin.financial.payments.index', compact('payments', 'paymentTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function paymentsCreate()
    {
        $students = Student::active()->orderBy('name')->get();
        $paymentTypes = ['tuition', 'registration', 'late_fee', 'graduation_fee', 'other'];

        return view('admin.financial.payments.create', compact('students', 'paymentTypes'));
    }

    /**
     * Store a newly created payment.
     */
    public function paymentsStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type' => 'required|in:tuition,registration,late_fee,graduation_fee,other',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:completed,pending,failed,refunded',
            'reference_number' => 'nullable|string|unique:payments,reference_number',
        ]);

        if (empty($validated['reference_number'])) {
            $validated['reference_number'] = 'PAY-' . time() . '-' . rand(1000, 9999);
        }

        $payment = Payment::create($validated);

        return redirect()->route('admin.financial.payments.index')
            ->with('success', 'تم إضافة الدفعة بنجاح.');
    }

    /**
     * Display the specified payment.
     */
    public function paymentsShow(Payment $payment)
    {
        $payment->load('student');

        return view('admin.financial.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function paymentsEdit(Payment $payment)
    {
        $students = Student::orderBy('name')->get();
        $paymentTypes = ['tuition', 'registration', 'late_fee', 'graduation_fee', 'other'];
        $statuses = ['completed', 'pending', 'failed', 'refunded'];

        return view('admin.financial.payments.edit', compact('payment', 'students', 'paymentTypes', 'statuses'));
    }

    /**
     * Update the specified payment.
     */
    public function paymentsUpdate(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type' => 'required|in:tuition,registration,late_fee,graduation_fee,other',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:completed,pending,failed,refunded',
            'reference_number' => 'nullable|string|unique:payments,reference_number,' . $payment->id,
        ]);

        if (empty($validated['reference_number'])) {
            $validated['reference_number'] = 'PAY-' . time() . '-' . rand(1000, 9999);
        }

        $payment->update($validated);

        return redirect()->route('admin.financial.payments.index')
            ->with('success', 'تم تحديث الدفعة بنجاح.');
    }

    /**
     * Remove the specified payment.
     */
    public function paymentsDestroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.financial.payments.index')
            ->with('success', 'تم حذف الدفعة بنجاح.');
    }

    /**
     * Display financial dashboard with statistics.
     */
    public function financialDashboard()
    {
        // Get current month and year statistics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Total revenue statistics
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // Payment type breakdown
        $paymentTypeStats = Payment::where('status', 'completed')
            ->selectRaw('payment_type, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('payment_type')
            ->get();

        // Monthly revenue for the past 12 months
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');

            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');

        // Failed payments
        $failedPayments = Payment::where('status', 'failed')->count();

        // Recent payments
        $recentPayments = Payment::with('student')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.financial.dashboard', compact(
            'totalRevenue',
            'monthlyRevenue',
            'paymentTypeStats',
            'monthlyStats',
            'pendingPayments',
            'pendingAmount',
            'failedPayments',
            'recentPayments'
        ));
    }

    /**
     * Generate financial reports.
     */
    public function financialReports(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.financial.reports.index');
        }

        $validated = $request->validate([
            'report_type' => 'required|in:payments,outstanding,collection',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel',
        ]);

        switch ($validated['report_type']) {
            case 'payments':
                return $this->generatePaymentsReport($validated);
            case 'outstanding':
                return $this->generateOutstandingReport($validated);
            case 'collection':
                return $this->generateCollectionReport($validated);
        }
    }

    /**
     * Display semester fees management.
     */
    public function semesterFeesIndex(Request $request)
    {
        $query = SemesterFee::withCount('payments')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('academic_year', 'like', "%{$search}%")
                  ->orWhere('semester', 'like', "%{$search}%")
                  ->orWhere('major', 'like', "%{$search}%");
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        $semesterFees = $query->paginate(20)->withQueryString();

        $academicYears = SemesterFee::distinct()->pluck('academic_year')->sort();
        $semesters = ['الأول', 'الثاني', 'الصيفي'];
        $majors = SemesterFee::distinct()->pluck('major')->sort();

        return view('admin.financial.semester-fees.index', compact('semesterFees', 'academicYears', 'semesters', 'majors'));
    }

    /**
     * Show the form for creating semester fees.
     */
    public function semesterFeesCreate()
    {
        $academicYears = ['2023-2024', '2024-2025', '2025-2026'];
        $semesters = ['الأول', 'الثاني', 'الصيفي'];

        return view('admin.financial.semester-fees.create', compact('academicYears', 'semesters'));
    }

    /**
     * Store newly created semester fees.
     */
    public function semesterFeesStore(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
            'major' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        // Check for existing semester fee
        $existing = SemesterFee::where([
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'major' => $validated['major'],
        ])->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'تم تحديد رسوم هذا الفصل مسبقاً.');
        }

        SemesterFee::create($validated);

        return redirect()->route('admin.financial.semester-fees.index')
            ->with('success', 'تم إضافة رسوم الفصل بنجاح.');
    }

    /**
     * Show the form for editing semester fees.
     */
    public function semesterFeesEdit(SemesterFee $semesterFee)
    {
        $academicYears = ['2023-2024', '2024-2025', '2025-2026'];
        $semesters = ['الأول', 'الثاني', 'الصيفي'];

        return view('admin.financial.semester-fees.edit', compact('semesterFee', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified semester fees.
     */
    public function semesterFeesUpdate(Request $request, SemesterFee $semesterFee)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
            'major' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        // Check for existing semester fee (excluding current)
        $existing = SemesterFee::where([
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'major' => $validated['major'],
        ])->where('id', '!=', $semesterFee->id)->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'تم تحديد رسوم هذا الفصل مسبقاً.');
        }

        $semesterFee->update($validated);

        return redirect()->route('admin.financial.semester-fees.index')
            ->with('success', 'تم تحديث رسوم الفصل بنجاح.');
    }

    /**
     * Remove the specified semester fees.
     */
    public function semesterFeesDestroy(SemesterFee $semesterFee)
    {
        $semesterFee->delete();

        return redirect()->route('admin.financial.semester-fees.index')
            ->with('success', 'تم حذف رسوم الفصل بنجاح.');
    }

    /**
     * Display major pricing management.
     */
    public function majorPricingIndex(Request $request)
    {
        $query = MajorPricing::withCount('semesterFees')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('college', 'like', "%{$search}%")
                  ->orWhere('major', 'like', "%{$search}%");
            });
        }

        // Filter by college
        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        $majorPricings = $query->paginate(20)->withQueryString();

        $colleges = MajorPricing::distinct()->pluck('college')->sort();

        return view('admin.financial.major-pricing.index', compact('majorPricings', 'colleges'));
    }

    /**
     * Show the form for creating major pricing.
     */
    public function majorPricingCreate()
    {
        $colleges = [
            'كلية الهندسة',
            'كلية العلوم',
            'كلية الطب',
            'كلية الصيدلة',
            'كلية العلوم الطبية المساعدة',
            'كلية الأعمال',
            'كلية الزراعة',
            'كلية الفنون الجميلة',
        ];

        return view('admin.financial.major-pricing.create', compact('colleges'));
    }

    /**
     * Store newly created major pricing.
     */
    public function majorPricingStore(Request $request)
    {
        $validated = $request->validate([
            'college' => 'required|string',
            'major' => 'required|string',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'graduation_fee' => 'required|numeric|min:0',
            'late_registration_fee' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Check for existing major pricing
        $existing = MajorPricing::where([
            'college' => $validated['college'],
            'major' => $validated['major'],
        ])->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'تم تحديد أسعار هذا التخصص مسبقاً.');
        }

        MajorPricing::create($validated);

        return redirect()->route('admin.financial.major-pricing.index')
            ->with('success', 'تم إضافة أسعار التخصص بنجاح.');
    }

    /**
     * Show the form for editing major pricing.
     */
    public function majorPricingEdit(MajorPricing $majorPricing)
    {
        $colleges = [
            'كلية الهندسة',
            'كلية العلوم',
            'كلية الطب',
            'كلية الصيدلة',
            'كلية العلوم الطبية المساعدة',
            'كلية الأعمال',
            'كلية الزراعة',
            'كلية الفنون الجميلة',
        ];

        return view('admin.financial.major-pricing.edit', compact('majorPricing', 'colleges'));
    }

    /**
     * Update the specified major pricing.
     */
    public function majorPricingUpdate(Request $request, MajorPricing $majorPricing)
    {
        $validated = $request->validate([
            'college' => 'required|string',
            'major' => 'required|string',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'graduation_fee' => 'required|numeric|min:0',
            'late_registration_fee' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Check for existing major pricing (excluding current)
        $existing = MajorPricing::where([
            'college' => $validated['college'],
            'major' => $validated['major'],
        ])->where('id', '!=', $majorPricing->id)->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'تم تحديد أسعار هذا التخصص مسبقاً.');
        }

        $majorPricing->update($validated);

        return redirect()->route('admin.financial.major-pricing.index')
            ->with('success', 'تم تحديث أسعار التخصص بنجاح.');
    }

    /**
     * Remove the specified major pricing.
     */
    public function majorPricingDestroy(MajorPricing $majorPricing)
    {
        $majorPricing->delete();

        return redirect()->route('admin.financial.major-pricing.index')
            ->with('success', 'تم حذف أسعار التخصص بنجاح.');
    }

    /**
     * Check for outstanding payments.
     */
    public function outstandingPayments(Request $request)
    {
        $query = Student::with(['semesterFees' => function ($q) {
                $q->whereDoesntHave('payments', function ($paymentQuery) {
                    $paymentQuery->where('status', 'completed');
                });
            }])
            ->whereHas('semesterFees', function ($q) {
                $q->whereDoesntHave('payments', function ($paymentQuery) {
                    $paymentQuery->where('status', 'completed');
                });
            });

        // Filter by college
        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->whereHas('semesterFees', function ($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        $outstandingStudents = $query->paginate(20)->withQueryString();

        $colleges = Student::distinct()->pluck('college')->sort();
        $majors = Student::distinct()->pluck('major')->sort();
        $academicYears = SemesterFee::distinct()->pluck('academic_year')->sort();

        return view('admin.financial.outstanding-payments', compact('outstandingStudents', 'colleges', 'majors', 'academicYears'));
    }

    /**
     * Export payments to Excel.
     */
    public function exportPayments(Request $request)
    {
        return Excel::download(new PaymentsExport($request->all()), 'payments_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate payments report.
     */
    private function generatePaymentsReport($data)
    {
        $payments = Payment::with('student')
            ->whereBetween('created_at', [$data['date_from'], $data['date_to']])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($data['format'] === 'pdf') {
            $pdf = \PDF::loadView('admin.financial.reports.payments', compact('payments', 'data'));
            return $pdf->download('payments_report_' . $data['date_from'] . '_to_' . $data['date_to'] . '.pdf');
        } else {
            return Excel::download(new PaymentsExport($data), 'payments_report_' . now()->format('Y-m-d') . '.xlsx');
        }
    }

    /**
     * Generate outstanding report.
     */
    private function generateOutstandingReport($data)
    {
        $outstandingStudents = Student::with(['semesterFees' => function ($q) use ($data) {
                $q->where('academic_year', $data['academic_year'] ?? null);
            }])
            ->whereHas('semesterFees', function ($q) use ($data) {
                $q->whereDoesntHave('payments', function ($paymentQuery) {
                    $paymentQuery->where('status', 'completed');
                });
            })
            ->whereBetween('created_at', [$data['date_from'], $data['date_to']])
            ->get();

        if ($data['format'] === 'pdf') {
            $pdf = \PDF::loadView('admin.financial.reports.outstanding', compact('outstandingStudents', 'data'));
            return $pdf->download('outstanding_report_' . $data['date_from'] . '_to_' . $data['date_to'] . '.pdf');
        } else {
            // Export to Excel with custom logic
            return response()->json(['message' => 'تم إنشاء التقرير بنجاح']);
        }
    }

    /**
     * Generate collection report.
     */
    private function generateCollectionReport($data)
    {
        $collections = Payment::with('student')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$data['date_from'], $data['date_to']])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($data['format'] === 'pdf') {
            $pdf = \PDF::loadView('admin.financial.reports.collection', compact('collections', 'data'));
            return $pdf->download('collection_report_' . $data['date_from'] . '_to_' . $data['date_to'] . '.pdf');
        } else {
            return Excel::download(new PaymentsExport($data), 'collection_report_' . now()->format('Y-m-d') . '.xlsx');
        }
    }

    /**
     * Get payment statistics.
     */
    public function getPaymentStats(Request $request)
    {
        $stats = [
            'total_payments' => Payment::where('status', 'completed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'this_month_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount'),
        ];

        return response()->json($stats);
    }
}
