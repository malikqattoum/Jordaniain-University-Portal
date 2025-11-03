<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicResultsController;
use App\Http\Controllers\FeePaymentController;

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
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require student authentication)
Route::middleware('auth:student')->group(function () {
    Route::get('/dashboard', [AcademicResultsController::class, 'dashboard'])->name('dashboard');
    Route::get('/academic-results', [AcademicResultsController::class, 'index'])->name('academic-results');
    Route::get('/fee-payment', [FeePaymentController::class, 'index'])->name('fee-payment');
});
