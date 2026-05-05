<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Rental;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GearController;
// use App\Http\Controllers\ReportController;
// use App\Http\Controllers\StatController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-rentals', function () {
    $rentals = Rental::with('gear', 'user')->get();
    return view('debug-rentals', compact('rentals'));
});

// Route khusus untuk memicu "Robot" Penalty via tombol
Route::post('/run-penalty-check', function () {
    Artisan::call('app:auto-calculate-penalty');
    return back()->with('status', 'Robot Penalty telah dijalankan!');
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

// Route untuk admin mengelola alat (CRUD)
Route::prefix('admin/gears')->group(function () {
    // Update status operasional (available, maintenance, etc.)
    Route::patch('/{gear}/status/{status}', [GearController::class, 'updateStatus'])->name('gears.update-status');
    
    // Update kondisi fisik (baik, rusak, etc.)
    Route::patch('/{gear}/condition/{condition}', [GearController::class, 'updateCondition'])->name('gears.update-condition');
});

require __DIR__.'/auth.php';
