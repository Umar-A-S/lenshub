<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\ReportController;
// use App\Http\Controllers\StatController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/owner/dashboard', [DashboardController::class, 'index'])->name('owner.dashboard');
    // Route::get('/owner/laporan-keuangan', [ReportController::class, 'index']); // Otomatis terjaga!
    // Route::get('/owner/statistik', [StatController::class, 'index']); // Otomatis terjaga!
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Route::get('/admin/laporan-keuangan', [ReportController::class, 'index']); // Otomatis terjaga!
    // Route::get('/admin/statistik', [StatController::class, 'index']); // Otomatis terjaga!
});


require __DIR__.'/auth.php';
