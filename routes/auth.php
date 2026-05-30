<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\WaPasswordResetController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// GUEST ROUTES
// ─────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {

    // Registrasi
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login (email / username / nomor WA + password)
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ── Lupa Sandi via OTP WA ────────────────────────────────
    Route::get('forgot-password', [WaPasswordResetController::class, 'showRequestForm'])
        ->name('password.request');

    Route::post('forgot-password', [WaPasswordResetController::class, 'sendOtp'])
        ->name('password.wa.send');

    Route::get('reset-password/verify', [WaPasswordResetController::class, 'showVerifyForm'])
        ->name('password.wa.verify.form');

    Route::post('reset-password/verify', [WaPasswordResetController::class, 'reset'])
        ->name('password.wa.reset');
});

// ─────────────────────────────────────────────────────────────
// AUTH ROUTES (sudah login)
// ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
