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
| 1. HALAMAN UTAMA & AUTH (LOGOUT/LOGIN)
|--------------------------------------------------------------------------
| Bagian ini mengatur apa yang pertama kali dilihat pengunjung.
*/

Route::get('/', function () {
    // Kalau buka web, langsung diarahkan ke halaman login
    return view('auth.login');
});

Route::get('/register', function () {
    // Kalau buka halaman register, langsung diarahkan ke halaman login (karena kita tidak pakai fitur register)
    return redirect('/login');
})->name('register');

// Mengambil pengaturan akun dari file auth.php (bawaan Laravel)
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| 2. AREA KHUSUS PEMILIK (OWNER)
|--------------------------------------------------------------------------
| Hanya bisa dibuka oleh pengguna yang sudah login dan punya peran 'owner'.
*/

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('owner.dashboard');
    
    // Rencana kedepan: Laporan & Statistik (saat ini masih dimatikan)
    // Route::get('/laporan-keuangan', [ReportController::class, 'index']);
});


/*
|--------------------------------------------------------------------------
| 3. AREA KHUSUS ADMIN (PENGELOLA)
|--------------------------------------------------------------------------
| Bagian ini untuk admin mengelola transaksi, denda, dan inventaris alat.
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // --- Dashboard Admin ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // --- Manajemen Alat (Gear) ---
    // Di sini admin bisa Tambah, Edit, Update Status, dan Hapus alat.
    Route::controller(GearController::class)->prefix('gears')->group(function () {
        Route::get('/', 'index')->name('gears.index');          // Lihat daftar alat
        Route::post('/', 'store')->name('gears.store');         // Simpan alat baru
        Route::get('/{gear}/edit', 'edit')->name('gears.edit'); // Buka form edit
        Route::put('/{gear}', 'update')->name('gears.update');   // Proses simpan perubahan
        Route::delete('/{gear}', 'destroy')->name('gears.destroy'); // Hapus alat

        // Fitur ganti status (Tersedia/Rusak/Maintenance)
        Route::patch('/{gear}/status/{status}', 'updateStatus')->name('gears.update-status');
        Route::patch('/{gear}/condition/{condition}', 'updateCondition')->name('gears.update-condition');
    });

    // --- Manajemen Transaksi (Rental) ---
    // Mengatur penyewaan, pengembalian, dan denda otomatis.
    Route::controller(RentalController::class)->prefix('rentals')->group(function () {
        Route::get('/', 'index')->name('rentals.index');
        Route::post('/{rental}/return', 'returnGear')->name('rentals.return');
        
        // Tombol rahasia admin untuk simulasi telat balikin (untuk tes denda)
        Route::patch('/{rental}/simulate-overdue', 'simulateOverdue')->name('rentals.simulate');
    });

});


/*
|--------------------------------------------------------------------------
| 4. FITUR PEMBANTU / DEBUGGING (KHUSUS PENGEMBANG)
|--------------------------------------------------------------------------
| Fitur ini sebaiknya dihapus atau dimatikan jika web sudah online (Production).
*/

// Tombol manual untuk menyuruh "Robot" mengecek denda saat ini juga
Route::post('/run-penalty-check', function () {
    Artisan::call('app:auto-calculate-penalty');
    return back()->with('status', 'Robot Penalty telah dijalankan!');
});

// Halaman rahasia untuk melihat data mentah transaksi (Cek error data)
Route::get('/debug-rentals', function () {
    $rentals = Rental::with('gear', 'user')->get();
    return view('debug-rentals', compact('rentals'));
});