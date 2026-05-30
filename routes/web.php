<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Owner\KlienController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Auth\PhoneOtpController;

use App\Http\Controllers\Owner\PengaturanController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/rules', fn() => view('rules'))->name('rules');
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{equipment}', [ProdukController::class, 'show'])->name('produk.show');
Route::get('/produk/{equipment}/sewa', [PermintaanController::class, 'formSewa'])->name('produk.sewa');
Route::post('/produk/{equipment}/sewa', [PermintaanController::class, 'store'])->name('produk.sewa.store');
Route::get('/sewa/sukses/{kode}', [PermintaanController::class, 'sukses'])->name('produk.sewa.sukses');

/*
|--------------------------------------------------------------------------
| USER (auth + role:user)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('akun')->name('akun.')
    ->group(function () {
        Route::get('/profil', [AccountController::class, 'profile'])->name('profil');
        Route::put('/profil', [AccountController::class, 'updateProfile'])->name('profil.update');
        Route::get('/pesanan', [AccountController::class, 'orders'])->name('pesanan');
        Route::get('/pesanan/partial', [AccountController::class, 'ordersPartial'])->name('pesanan.partial');
        Route::get('/notifikasi', [AccountController::class, 'notifications'])->name('notifikasi');
        Route::get('/poll-status', [AccountController::class, 'pollStatus'])->name('poll.status');
    });

/*
|--------------------------------------------------------------------------
| OTP VERIFIKASI WA (auth, semua role)
| Catatan: Email OTP dihapus — komunikasi fokus via WhatsApp (Fonnte)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('verifikasi')->name('otp.')->group(function () {
    Route::post('/wa/kirim',  [PhoneOtpController::class, 'send'])->name('phone.send');
    Route::post('/wa/verif',  [PhoneOtpController::class, 'verify'])->name('phone.verify');
});

/*
|--------------------------------------------------------------------------
| ADMIN (auth + role:admin,owner)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,owner'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Permintaan
        Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
        Route::get('/permintaan/partial', [PermintaanController::class, 'indexPartial'])->name('permintaan.partial');
        Route::post('/permintaan/{rental}/konfirmasi', [PermintaanController::class, 'konfirmasi'])->name('permintaan.konfirmasi');
        Route::delete('/permintaan/{rental}/tolak', [PermintaanController::class, 'tolak'])->name('permintaan.tolak');

        // Manajemen Sewa
        Route::get('/sewa', [SewaController::class, 'index'])->name('sewa');
        Route::post('/sewa/{rental}/pengembalian', [SewaController::class, 'pengembalian'])->name('sewa.pengembalian');
        Route::post('/sewa/{rental}/lunas-denda', [SewaController::class, 'lunasDenda'])->name('sewa.lunas-denda');
        Route::get('/sewa/{rental}/wa-reminder', [SewaController::class, 'waReminder'])->name('sewa.wa-reminder');

        // Transaksi
        Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
        Route::get('/poll-admin', [AccountController::class, 'pollAdmin'])->name('poll.admin');
        Route::get('/poll-dashboard', [AccountController::class, 'pollDashboard'])->name('poll.dashboard');

        // Inventory
        Route::controller(InventoryController::class)->prefix('inventory')->group(function () {
            Route::get('/', 'index')->name('inventory.index');
            Route::get('/create', 'create')->name('inventory.create');
            Route::post('/', 'store')->name('inventory.store');
            Route::get('/{equipment}', 'show')->name('inventory.show');
            Route::get('/{equipment}/edit', 'edit')->name('inventory.edit');
            Route::put('/{equipment}', 'update')->name('inventory.update');
            Route::delete('/{equipment}', 'destroy')->name('inventory.destroy');
        });

        // Denda
        Route::get('/denda', [FineController::class, 'index'])->name('denda.index');
        Route::patch('/denda/{fine}/lunas', [FineController::class, 'lunas'])->name('denda.lunas');
    });

/*
|--------------------------------------------------------------------------
| OWNER ONLY
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')->name('owner.')
    ->group(function () {
        Route::get('/klien', [KlienController::class, 'index'])->name('klien');
        Route::post('/klien/{user}/toggle-status', [KlienController::class, 'toggleStatus'])->name('klien.toggle-status');
        Route::delete('/klien/{user}', [KlienController::class, 'destroy'])->name('klien.destroy');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

        // Pengaturan owner
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
        Route::post('/pengaturan/admin', [PengaturanController::class, 'storeAdmin'])->name('pengaturan.admin.store');
        Route::put('/pengaturan/admin/{user}', [PengaturanController::class, 'updateAdmin'])->name('pengaturan.admin.update');
        Route::delete('/pengaturan/admin/{user}', [PengaturanController::class, 'destroyAdmin'])->name('pengaturan.admin.destroy');
    });

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
