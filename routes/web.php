<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DataEntryController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ROUTES ============
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// ============ AUTHENTICATED ROUTES ============
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Root redirect to dashboard
    Route::get('/', function () { 
        return redirect('/dashboard');  
    })->name('welcome');

    // ============ ALL ROLES CAN ACCESS (viewer, user, admin) ============
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-data', [DashboardController::class, 'chart'])->name('chart.data');
    Route::get('/top-data', [DashboardController::class, 'topData'])->name('top.data');
    Route::get('/dashboard/trend-analysis', [DashboardController::class, 'trendAnalysis'])->name('trend.analysis');
    Route::get('/comparison-data', [DashboardController::class, 'comparisonData'])->name('comparison.data');
    Route::get('/all-shippers-data', [DashboardController::class, 'allShippersData'])->name('all.shippers.data');
    
    // Export (All roles can export)
    Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('export.excel');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('export.pdf');
    
    // Comparison
    Route::get('/dashboard/comparison', [DashboardController::class, 'comparison'])->name('dashboard.comparison');
    Route::get('/dashboard/comparison-data', [DashboardController::class, 'comparisonChartData'])->name('comparison.chart.data');
    Route::get('/comparison-summary', [DashboardController::class, 'comparisonSummary'])->name('comparison.summary');
    Route::get('/comparison-per-shipper', [DashboardController::class, 'comparisonPerShipper'])->name('comparison.per.shipper');

    // ============ USER & ADMIN ONLY ============
    Route::middleware(['role:user,admin'])->group(function () {
        // Input Data Manual
        Route::get('/input-data', [DataEntryController::class, 'create'])->name('data.create');
        Route::post('/input-data', [DataEntryController::class, 'store'])->name('data.store');
        
        // Upload CSV
        Route::get('/upload-data', [DataEntryController::class, 'uploadForm'])->name('data.upload');
        Route::post('/upload-data', [DataEntryController::class, 'uploadCsv'])->name('data.upload.post');
        
        // My Submissions
        Route::get('/my-submissions', [DataEntryController::class, 'mySubmissions'])->name('my.submissions');
        
        // Delete own data (users can only delete their own)
        Route::delete('/data/{id}', [DataEntryController::class, 'destroy'])->name('data.delete');
    });

    // ============ ADMIN ONLY ============
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::resource('users', UserController::class);
        
        // Audit Logs
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');
    });
});