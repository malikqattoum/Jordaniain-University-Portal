<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Student;
use App\Models\Payment;
use App\Models\AcademicRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemAdministrationController extends Controller
{
    /**
     * Display system logs.
     */
    public function systemLogs(Request $request)
    {
        $logs = collect(); // Placeholder for actual log handling

        // For demonstration, we'll show some sample log data
        // In a real implementation, you would read from Laravel logs or a custom logs table

        $logData = [
            [
                'level' => 'info',
                'message' => 'تم تسجيل دخول المشرف',
                'context' => ['user' => 'admin@ju.edu.jo', 'ip' => '192.168.1.100'],
                'timestamp' => now()->subMinutes(5),
            ],
            [
                'level' => 'warning',
                'message' => 'محاولة تسجيل دخول فاشلة',
                'context' => ['email' => 'wrong@email.com', 'ip' => '192.168.1.101'],
                'timestamp' => now()->subMinutes(15),
            ],
            [
                'level' => 'error',
                'message' => 'فشل في معالجة الدفعة',
                'context' => ['payment_id' => 123, 'student_id' => 456],
                'timestamp' => now()->subMinutes(30),
            ],
            [
                'level' => 'info',
                'message' => 'تم إضافة طالب جديد',
                'context' => ['student_id' => 789, 'name' => 'أحمد محمد'],
                'timestamp' => now()->subHour(),
            ],
        ];

        $logs = collect($logData);

        // Filter by level
        if ($request->filled('level')) {
            $logs = $logs->where('level', $request->level);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $logs = $logs->where('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $logs = $logs->where('timestamp', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $logs = $logs->filter(function ($log) use ($search) {
                return stripos($log['message'], $search) !== false ||
                       stripos(json_encode($log['context']), $search) !== false;
            });
        }

        $logs = $logs->paginate(20);

        return view('admin.system.logs', compact('logs'));
    }

    /**
     * Display user activity logs.
     */
    public function userActivityLogs(Request $request)
    {
        // Get recent user activities
        $activities = [];

        // Recent student registrations
        $recentStudents = Student::with('admin:id,name')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($student) {
                return [
                    'type' => 'student_registration',
                    'user' => $student->admin ? $student->admin->name : 'نظام',
                    'description' => "تم تسجيل طالب جديد: {$student->name}",
                    'timestamp' => $student->created_at,
                    'user_id' => $student->id,
                ];
            });

        // Recent payments
        $recentPayments = Payment::with('student', 'admin:id,name')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment_processed',
                    'user' => $payment->admin ? $payment->admin->name : 'نظام',
                    'description' => "تم معالجة دفعة بقيمة {$payment->amount} للطالب {$payment->student->name}",
                    'timestamp' => $payment->created_at,
                    'user_id' => $payment->student_id,
                ];
            });

        // Recent academic records
        $recentRecords = AcademicRecord::with('student', 'course')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'type' => 'grade_entry',
                    'user' => 'نظام',
                    'description' => "تم إدخال درجة {$record->grade} للطالب {$record->student->name} في مادة {$record->course->course_name}",
                    'timestamp' => $record->created_at,
                    'user_id' => $record->student_id,
                ];
            });

        // Combine and sort activities
        $activities = $recentStudents->concat($recentPayments)
            ->concat($recentRecords)
            ->sortByDesc('timestamp')
            ->paginate(20);

        return view('admin.system.activity-logs', compact('activities'));
    }

    /**
     * Display admin users management.
     */
    public function adminUsers(Request $request)
    {
        $query = Admin::orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        $admins = $query->paginate(20)->withQueryString();

        $roles = [
            'super_admin' => 'مشرف عام',
            'academic_admin' => 'مشرف أكاديمي',
            'finance_admin' => 'مشرف مالي',
            'registrar' => 'مسجل',
        ];

        return view('admin.system.admin-users', compact('admins', 'roles'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function createAdmin()
    {
        $roles = [
            'super_admin' => 'مشرف عام',
            'academic_admin' => 'مشرف أكاديمي',
            'finance_admin' => 'مشرف مالي',
            'registrar' => 'مسجل',
        ];

        return view('admin.system.create-admin', compact('roles'));
    }

    /**
     * Store a newly created admin.
     */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,academic_admin,finance_admin,registrar',
        ]);

        Admin::create($validated);

        return redirect()->route('admin.system.admin-users')
            ->with('success', 'تم إنشاء حساب المشرف بنجاح.');
    }

    /**
     * Show the form for editing an admin.
     */
    public function editAdmin(Admin $admin)
    {
        $roles = [
            'super_admin' => 'مشرف عام',
            'academic_admin' => 'مشرف أكاديمي',
            'finance_admin' => 'مشرف مالي',
            'registrar' => 'مسجل',
        ];

        return view('admin.system.edit-admin', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin.
     */
    public function updateAdmin(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'role' => 'required|in:super_admin,academic_admin,finance_admin,registrar',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Remove password if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.system.admin-users')
            ->with('success', 'تم تحديث بيانات المشرف بنجاح.');
    }

    /**
     * Remove the specified admin.
     */
    public function destroyAdmin(Admin $admin)
    {
        // Prevent deleting the last super admin
        if ($admin->role === 'super_admin') {
            $superAdminCount = Admin::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.system.admin-users')
                    ->with('error', 'لا يمكن حذف آخر مشرف عام.');
            }
        }

        $admin->delete();

        return redirect()->route('admin.system.admin-users')
            ->with('success', 'تم حذف المشرف بنجاح.');
    }

    /**
     * Display system settings.
     */
    public function systemSettings()
    {
        $settings = [
            'system_name' => config('app.name', 'الجامعة الأردنية'),
            'system_version' => '1.0.0',
            'timezone' => config('app.timezone', 'Asia/Amman'),
            'default_language' => 'ar',
            'maintenance_mode' => false,
            'backup_frequency' => 'daily',
            'email_notifications' => true,
            'max_file_upload' => 10240, // 10MB
        ];

        return view('admin.system.settings', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'default_language' => 'required|in:ar,en',
            'backup_frequency' => 'required|in:hourly,daily,weekly,monthly',
            'email_notifications' => 'boolean',
            'maintenance_mode' => 'boolean',
            'max_file_upload' => 'required|integer|min:1024',
        ]);

        // Here you would typically save to a settings table or config file
        // For demonstration, we'll just flash a success message

        return redirect()->route('admin.system.settings')
            ->with('success', 'تم تحديث إعدادات النظام بنجاح.');
    }

    /**
     * Display database maintenance tools.
     */
    public function databaseMaintenance()
    {
        // Get database statistics
        $stats = [
            'total_students' => Student::count(),
            'total_courses' => DB::table('courses')->count(),
            'total_payments' => Payment::count(),
            'database_size' => $this->getDatabaseSize(),
            'last_backup' => '2025-11-03 12:00:00', // Placeholder
            'optimization_status' => 'healthy',
        ];

        return view('admin.system.database-maintenance', compact('stats'));
    }

    /**
     * Perform database optimization.
     */
    public function optimizeDatabase(Request $request)
    {
        // This would typically run database optimization commands
        // For demonstration, we'll simulate the process

        try {
            // In a real implementation, you would run:
            // DB::statement('OPTIMIZE TABLE students, courses, payments, academic_records');

            // Simulate processing time
            sleep(2);

            Log::info('Database optimization completed by admin: ' . auth('admin')->user()->name);

            return response()->json([
                'success' => true,
                'message' => 'تم تحسين قاعدة البيانات بنجاح.',
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Database optimization failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في تحسين قاعدة البيانات.',
            ], 500);
        }
    }

    /**
     * Create system backup.
     */
    public function createBackup(Request $request)
    {
        try {
            // This would typically trigger a backup process
            // For demonstration, we'll simulate it

            // Log the backup activity
            Log::info('System backup initiated by admin: ' . auth('admin')->user()->name);

            // In a real implementation, you might use Laravel packages like spatie/laravel-backup

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء نسخة احتياطية بنجاح.',
                'backup_name' => 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql',
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء النسخة الاحتياطية.',
            ], 500);
        }
    }

    /**
     * Clear system cache.
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear various caches
            Cache::flush();

            // Clear route cache
            // \Artisan::call('route:cache');

            // Clear config cache
            // \Artisan::call('config:cache');

            // Clear view cache
            // \Artisan::call('view:clear');

            Log::info('System cache cleared by admin: ' . auth('admin')->user()->name);

            return response()->json([
                'success' => true,
                'message' => 'تم مسح ذاكرة التخزين المؤقت بنجاح.',
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Cache clearing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في مسح ذاكرة التخزين المؤقت.',
            ], 500);
        }
    }

    /**
     * Get system performance metrics.
     */
    public function getPerformanceMetrics(Request $request)
    {
        $metrics = [
            'response_time' => rand(100, 500), // Placeholder
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'cpu_load' => sys_getloadavg()[0],
            'disk_usage' => disk_free_space('/') / disk_total_space('/'),
            'active_users' => rand(10, 50), // Placeholder
            'database_queries' => DB::getQueryLog(),
            'cache_hit_rate' => rand(85, 95), // Placeholder
        ];

        return response()->json($metrics);
    }

    /**
     * Display security logs.
     */
    public function securityLogs(Request $request)
    {
        // Simulate security events
        $securityEvents = [
            [
                'type' => 'login_attempt',
                'severity' => 'medium',
                'message' => 'محاولة تسجيل دخول من عنوان IP غير معتاد',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'timestamp' => now()->subMinutes(10),
                'status' => 'blocked',
            ],
            [
                'type' => 'permission_denied',
                'severity' => 'high',
                'message' => 'محاولة الوصول لصفحة غير مسموحة',
                'user' => 'user@example.com',
                'resource' => '/admin/system/users',
                'timestamp' => now()->subHour(),
                'status' => 'blocked',
            ],
            [
                'type' => 'bulk_operation',
                'severity' => 'low',
                'message' => 'عملية حذف جماعية لـ 50 طالب',
                'admin' => 'admin@ju.edu.jo',
                'timestamp' => now()->subDays(1),
                'status' => 'success',
            ],
        ];

        $events = collect($securityEvents);

        // Filter by severity
        if ($request->filled('severity')) {
            $events = $events->where('severity', $request->severity);
        }

        // Filter by type
        if ($request->filled('type')) {
            $events = $events->where('type', $request->type);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $events = $events->where('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $events = $events->where('timestamp', '<=', $request->date_to);
        }

        $events = $events->paginate(20);

        return view('admin.system.security-logs', compact('events'));
    }

    /**
     * Generate system health report.
     */
    public function systemHealthReport(Request $request)
    {
        $health = [
            'overall_status' => 'healthy',
            'checks' => [
                'database' => 'healthy',
                'cache' => 'healthy',
                'storage' => 'healthy',
                'email' => 'healthy',
                'backup' => 'healthy',
                'security' => 'warning',
            ],
            'last_check' => now(),
            'uptime' => '15 days, 7 hours',
            'version' => '1.0.0',
            'issues' => [
                [
                    'severity' => 'medium',
                    'description' => 'تم رصد محاولات تسجيل دخول فاشلة متعددة',
                    'recommendation' => 'مراجعة إعدادات الأمان',
                ],
            ],
        ];

        if ($request->format === 'json') {
            return response()->json($health);
        }

        return view('admin.system.health-report', compact('health'));
    }

    // Private helper methods

    private function getDatabaseSize()
    {
        // This would typically query the database to get actual size
        return '2.5 MB'; // Placeholder
    }
}
