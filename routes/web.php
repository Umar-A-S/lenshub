<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\Rental;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| 1. HALAMAN UTAMA & AUTH
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return redirect('/login');
})->name('register');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| 2. AREA UMUM (DASHBOARD & PROFILE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 3. AREA CUSTOMER (PENYEWA)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/sewa', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/sewa', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/sewa/success/{id}', [RentalController::class, 'showSuccess'])->name('rentals.success');
});

/*
|--------------------------------------------------------------------------
| 4. AREA KHUSUS ADMIN (PENGELOLA)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin & Manajemen Sewa
    Route::get('rentals/dashboard', [RentalController::class, 'index'])->name('rentals.dashboard');
    Route::post('rentals/{id}/aktifkan', [RentalController::class, 'konfirmasiPembayaran'])->name('rentals.aktifkan');
    Route::post('rentals/{id}/selesai', [RentalController::class, 'selesaiRental'])->name('rentals.selesai');
    Route::post('rentals/clear-expired', [RentalController::class, 'clearExpiredBookings'])->name('rentals.clear-expired');

    // View KTP
    Route::get('view-ktp/{id}', [RentalController::class, 'viewKtp'])->name('view-ktp');

    // Manajemen Alat (Gears)
    Route::controller(GearController::class)->prefix('gears')->name('gears.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{gear}/edit', 'edit')->name('edit');
        Route::put('/{gear}', 'update')->name('update');
        Route::delete('/{gear}', 'destroy')->name('destroy');
        Route::patch('/{gear}/status/{status}', 'updateStatus')->name('update-status');
        Route::patch('/{id}/update-condition', 'updateCondition')->name('update-condition');
    });
});

/*
|--------------------------------------------------------------------------
| 5. AREA KHUSUS OWNER (OWNER)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| 6. DEBUGGING & UTILITY
|--------------------------------------------------------------------------
*/

Route::post('/run-penalty-check', function () {
    Artisan::call('app:auto-calculate-penalty');
    return back()->with('status', 'Robot Penalty telah dijalankan!');
});