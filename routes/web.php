<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// GANTI INI - redirect root ke dashboard
Route::get('/', function () { 
    return redirect('/dashboard');  // ✅ redirect ke dashboard
})->name('welcome');

// Atau kalau mau langsung ke dashboard tanpa redirect:
// Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Chart endpoints
Route::get('/chart-data', [DashboardController::class, 'chart'])->name('chart.data');

// Top 5 filtered data
Route::get('/top-data', [DashboardController::class, 'topData'])->name('top.data');

// Trend Analysis
Route::get('/dashboard/trend-analysis', [DashboardController::class, 'trendAnalysis'])->name('trend.analysis');

// Comparison between shippers
Route::get('/comparison-data', [DashboardController::class, 'comparisonData'])->name('comparison.data');

// Export Routes
Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('export.excel');
Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('export.pdf');
Route::get('/all-shippers-data', [DashboardController::class, 'allShippersData'])->name('all.shippers.data');

// Comparison Penerimaan vs Penyaluran
Route::get('/dashboard/comparison', [DashboardController::class, 'comparison'])->name('dashboard.comparison');
Route::get('/dashboard/comparison-data', [DashboardController::class, 'comparisonChartData'])->name('comparison.chart.data');

Route::get('/comparison-summary', [DashboardController::class, 'comparisonSummary'])->name('comparison.summary');
Route::get('/comparison-per-shipper', [DashboardController::class, 'comparisonPerShipper'])->name('comparison.per.shipper');