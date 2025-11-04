<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicResultsController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentAdminController;
use App\Http\Controllers\AcademicManagementController;
use App\Http\Controllers\PaymentManagementController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SystemAdministrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('student.login');
});

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Student Protected Routes (require student authentication)
Route::middleware('auth:student')->group(function () {
    Route::get('/dashboard', [AcademicResultsController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/academic-results', [AcademicResultsController::class, 'index'])->name('student.academic-results');
    Route::get('/fee-payment', [FeePaymentController::class, 'index'])->name('student.fee-payment');
    Route::get('/payment-history', [PaymentHistoryController::class, 'index'])->name('student.payment-history');
    Route::get('/payment-receipt/{id}', [PaymentHistoryController::class, 'show'])->name('student.payment-receipt');
    Route::get('/payment-receipt/{id}/download', [PaymentHistoryController::class, 'downloadReceipt'])->name('student.payment-receipt.download');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});

// Admin Panel Routes (require admin authentication)
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {

    // Dashboard Routes
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    // Student Management Routes
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentAdminController::class, 'index'])->name('index');
        Route::get('/create', [StudentAdminController::class, 'create'])->name('create');
        Route::post('/', [StudentAdminController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentAdminController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentAdminController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentAdminController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentAdminController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [StudentAdminController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{student}/academic-info', [StudentAdminController::class, 'updateAcademicInfo'])->name('academic-info');
        Route::get('/{student}/transcript', [StudentAdminController::class, 'generateTranscript'])->name('transcript');
        Route::get('/search/ajax', [StudentAdminController::class, 'search'])->name('search.ajax');
        Route::get('/export', [StudentAdminController::class, 'export'])->name('export');
    });

    // Academic Management Routes
    Route::prefix('academic')->name('academic.')->group(function () {

        // Courses Management
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [AcademicManagementController::class, 'coursesIndex'])->name('index');
            Route::get('/create', [AcademicManagementController::class, 'coursesCreate'])->name('create');
            Route::post('/', [AcademicManagementController::class, 'coursesStore'])->name('store');
            Route::get('/{course}/edit', [AcademicManagementController::class, 'coursesEdit'])->name('edit');
            Route::put('/{course}', [AcademicManagementController::class, 'coursesUpdate'])->name('update');
            Route::delete('/{course}', [AcademicManagementController::class, 'coursesDestroy'])->name('destroy');
        });

        // Enrollments Management
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [AcademicManagementController::class, 'enrollmentsIndex'])->name('index');
            Route::get('/create', [AcademicManagementController::class, 'enrollmentsCreate'])->name('create');
            Route::post('/', [AcademicManagementController::class, 'enrollmentsStore'])->name('store');
            Route::get('/{enrollment}/edit', [AcademicManagementController::class, 'enrollmentsEdit'])->name('edit');
            Route::put('/{enrollment}', [AcademicManagementController::class, 'enrollmentsUpdate'])->name('update');
            Route::delete('/{enrollment}', [AcademicManagementController::class, 'enrollmentsDestroy'])->name('destroy');
        });

        // Academic Records Management
        Route::prefix('records')->name('records.')->group(function () {
            Route::get('/', [AcademicManagementController::class, 'academicRecordsIndex'])->name('index');
            Route::get('/create', [AcademicManagementController::class, 'academicRecordsCreate'])->name('create');
            Route::post('/', [AcademicManagementController::class, 'academicRecordsStore'])->name('store');
            Route::get('/{academicRecord}/edit', [AcademicManagementController::class, 'academicRecordsEdit'])->name('edit');
            Route::put('/{academicRecord}', [AcademicManagementController::class, 'academicRecordsUpdate'])->name('update');
            Route::delete('/{academicRecord}', [AcademicManagementController::class, 'academicRecordsDestroy'])->name('destroy');
            Route::get('/bulk-entry', [AcademicManagementController::class, 'bulkGradeEntry'])->name('bulk-entry');
            Route::post('/bulk-entry', [AcademicManagementController::class, 'bulkGradeEntry'])->name('bulk-entry.store');
            Route::get('/enrollments/ajax', [AcademicManagementController::class, 'getEnrollmentsForGradeEntry'])->name('enrollments.ajax');
        });

        // Equivalent Courses Management
        Route::prefix('equivalent-courses')->name('equivalent-courses.')->group(function () {
            Route::get('/', [AcademicManagementController::class, 'equivalentCoursesIndex'])->name('index');
            Route::get('/create', [AcademicManagementController::class, 'equivalentCoursesCreate'])->name('create');
            Route::post('/', [AcademicManagementController::class, 'equivalentCoursesStore'])->name('store');
            Route::delete('/{equivalentCourse}', [AcademicManagementController::class, 'equivalentCoursesDestroy'])->name('destroy');
        });
    });

    // Financial Management Routes
    Route::prefix('financial')->name('financial.')->group(function () {

        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentManagementController::class, 'paymentsIndex'])->name('index');
            Route::get('/create', [PaymentManagementController::class, 'paymentsCreate'])->name('create');
            Route::post('/', [PaymentManagementController::class, 'paymentsStore'])->name('store');
            Route::get('/{payment}', [PaymentManagementController::class, 'paymentsShow'])->name('show');
            Route::get('/{payment}/edit', [PaymentManagementController::class, 'paymentsEdit'])->name('edit');
            Route::put('/{payment}', [PaymentManagementController::class, 'paymentsUpdate'])->name('update');
            Route::delete('/{payment}', [PaymentManagementController::class, 'paymentsDestroy'])->name('destroy');
            Route::get('/export', [PaymentManagementController::class, 'exportPayments'])->name('export');
        });

        // Financial Dashboard
        Route::get('/dashboard', [PaymentManagementController::class, 'financialDashboard'])->name('dashboard');

        // Reports
        Route::get('/reports', [PaymentManagementController::class, 'financialReports'])->name('reports');
        Route::post('/reports', [PaymentManagementController::class, 'financialReports'])->name('reports.generate');

        // Semester Fees Management
        Route::prefix('semester-fees')->name('semester-fees.')->group(function () {
            Route::get('/', [PaymentManagementController::class, 'semesterFeesIndex'])->name('index');
            Route::get('/create', [PaymentManagementController::class, 'semesterFeesCreate'])->name('create');
            Route::post('/', [PaymentManagementController::class, 'semesterFeesStore'])->name('store');
            Route::get('/{semesterFee}/edit', [PaymentManagementController::class, 'semesterFeesEdit'])->name('edit');
            Route::put('/{semesterFee}', [PaymentManagementController::class, 'semesterFeesUpdate'])->name('update');
            Route::delete('/{semesterFee}', [PaymentManagementController::class, 'semesterFeesDestroy'])->name('destroy');
        });

        // Major Pricing Management
        Route::prefix('major-pricing')->name('major-pricing.')->group(function () {
            Route::get('/', [PaymentManagementController::class, 'majorPricingIndex'])->name('index');
            Route::get('/create', [PaymentManagementController::class, 'majorPricingCreate'])->name('create');
            Route::post('/', [PaymentManagementController::class, 'majorPricingStore'])->name('store');
            Route::get('/{majorPricing}/edit', [PaymentManagementController::class, 'majorPricingEdit'])->name('edit');
            Route::put('/{majorPricing}', [PaymentManagementController::class, 'majorPricingUpdate'])->name('update');
            Route::delete('/{majorPricing}', [PaymentManagementController::class, 'majorPricingDestroy'])->name('destroy');
        });

        // Outstanding Payments
        Route::get('/outstanding-payments', [PaymentManagementController::class, 'outstandingPayments'])->name('outstanding-payments');
    });

    // System Administration Routes
    Route::prefix('system')->name('system.')->group(function () {

        // Logs Management
        Route::get('/logs', [SystemAdministrationController::class, 'systemLogs'])->name('logs');
        Route::get('/activity-logs', [SystemAdministrationController::class, 'userActivityLogs'])->name('activity-logs');
        Route::get('/security-logs', [SystemAdministrationController::class, 'securityLogs'])->name('security-logs');

        // Admin Users Management
        Route::prefix('admin-users')->name('admin-users.')->group(function () {
            Route::get('/', [SystemAdministrationController::class, 'adminUsers'])->name('index');
            Route::get('/create', [SystemAdministrationController::class, 'createAdmin'])->name('create');
            Route::post('/', [SystemAdministrationController::class, 'storeAdmin'])->name('store');
            Route::get('/{admin}/edit', [SystemAdministrationController::class, 'editAdmin'])->name('edit');
            Route::put('/{admin}', [SystemAdministrationController::class, 'updateAdmin'])->name('update');
            Route::delete('/{admin}', [SystemAdministrationController::class, 'destroyAdmin'])->name('destroy');
        });

        // System Settings
        Route::get('/settings', [SystemAdministrationController::class, 'systemSettings'])->name('settings');
        Route::put('/settings', [SystemAdministrationController::class, 'updateSystemSettings'])->name('settings.update');

        // Database Maintenance
        Route::get('/database-maintenance', [SystemAdministrationController::class, 'databaseMaintenance'])->name('database-maintenance');
        Route::post('/database-optimize', [SystemAdministrationController::class, 'optimizeDatabase'])->name('database-optimize');
        Route::post('/create-backup', [SystemAdministrationController::class, 'createBackup'])->name('create-backup');
        Route::post('/clear-cache', [SystemAdministrationController::class, 'clearCache'])->name('clear-cache');

        // System Health
        Route::get('/health-report', [SystemAdministrationController::class, 'systemHealthReport'])->name('health-report');
        Route::get('/performance-metrics', [SystemAdministrationController::class, 'getPerformanceMetrics'])->name('performance-metrics');
    });

    // Analytics & Reporting Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/students', [AdminDashboardController::class, 'getStudentStatistics'])->name('students');
        Route::get('/academic', [AdminDashboardController::class, 'getAcademicStatistics'])->name('academic');
        Route::get('/financial', [AdminDashboardController::class, 'getFinancialStatistics'])->name('financial');
        Route::get('/dashboard-data', [AdminDashboardController::class, 'getDashboardData'])->name('dashboard-data');
        Route::get('/alerts', [AdminDashboardController::class, 'getAlerts'])->name('alerts');
        Route::get('/generate-report', [AdminDashboardController::class, 'generateReport'])->name('generate-report');
    });

    // API Routes for AJAX functionality
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/payment-stats', [PaymentManagementController::class, 'getPaymentStats'])->name('payment-stats');
        Route::get('/system-health', [SystemAdministrationController::class, 'getSystemHealth'])->name('system-health');
    });
});
