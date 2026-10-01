<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if auth, else login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Step 1 – Enter email & receive OTP
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');

    // Step 2 – Verify OTP
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('password.otp.verify');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.verify.submit');

    // Step 3 – Set new password
    Route::get('/reset-password-otp', [AuthController::class, 'showOtpResetPassword'])->name('password.otp.reset');
    Route::post('/reset-password-otp', [AuthController::class, 'resetPasswordOtp'])->name('password.otp.reset.submit');

});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('api.dashboard.chart');

    // Expense CSV Export (Must be before resource show route)
    Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');

    // Expenses Resource CRUD
    Route::resource('expenses', ExpenseController::class);

    // Incomes Resource CRUD
    Route::resource('incomes', IncomeController::class);

    // All Unified Transactions Page
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Categories Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Profile & User Preferences
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Support & Help
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');

    // Admin Panel Foundation (Guarded by AdminMiddleware)
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // User Management CRUD Routes
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Support Ticket CRUD Routes
        Route::get('/support', [AdminController::class, 'supportMessages'])->name('support');
        Route::post('/support', [AdminController::class, 'storeSupportMessage'])->name('support.store');
        Route::put('/support/{supportMessage}', [AdminController::class, 'updateSupportStatus'])->name('support.update');
        Route::delete('/support/{supportMessage}', [AdminController::class, 'destroySupportMessage'])->name('support.destroy');
    });
});
