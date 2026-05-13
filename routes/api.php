<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GearController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes - LensHub
|--------------------------------------------------------------------------
|
| Semua API endpoint menggunakan JSON response dengan format:
| {
|   "status": "success|error",
|   "message": "Pesan hasil operasi",
|   "data": {...}
| }
|
*/

/*
|--------------------------------------------------------------------------
| 1. AUTHENTICATION ROUTES (Public - Tidak perlu token)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-token', [AuthController::class, 'verifyToken']);
});

/*
|--------------------------------------------------------------------------
| 2. PROTECTED ROUTES (Perlu authentication token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |---------- Auth Routes ----------|
    */
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    /*
    |---------- User Routes ----------|
    */
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/role/{role}', [UserController::class, 'byRole']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}/password', [UserController::class, 'updatePassword']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    /*
    |---------- Category Routes ----------|
    */
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::get('/{id}/gears', [CategoryController::class, 'gears']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    /*
    |---------- Gear Routes ----------|
    */
    Route::prefix('gears')->group(function () {
        Route::get('/', [GearController::class, 'index']);
        Route::get('/{id}', [GearController::class, 'show']);
        Route::get('/{id}/condition-history', [GearController::class, 'conditionHistory']);
        Route::post('/', [GearController::class, 'store']);
        Route::put('/{id}', [GearController::class, 'update']);
        Route::delete('/{id}', [GearController::class, 'destroy']);
        Route::patch('/{id}/status', [GearController::class, 'updateStatus']);
        Route::patch('/{id}/condition', [GearController::class, 'updateCondition']);
        Route::post('/{id}/duplicate', [GearController::class, 'duplicate']);
    });

    /*
    |---------- Rental Routes ----------|
    */
    Route::prefix('rentals')->group(function () {
        Route::get('/', [RentalController::class, 'index']);
        Route::get('/stats/dashboard', [RentalController::class, 'dashboardStats']);
        Route::get('/user/{userId}', [RentalController::class, 'userRentals']);
        Route::get('/gear/{gearId}', [RentalController::class, 'gearRentals']);
        Route::get('/{id}', [RentalController::class, 'show']);
        Route::get('/{id}/ktp', [RentalController::class, 'downloadKtp']);
        Route::post('/', [RentalController::class, 'store']);
        Route::put('/{id}', [RentalController::class, 'update']);
        Route::patch('/{id}/confirm-payment', [RentalController::class, 'confirmPayment']);
        Route::patch('/{id}/complete', [RentalController::class, 'complete']);
        Route::patch('/{id}/cancel', [RentalController::class, 'cancel']);
        Route::delete('/{id}', [RentalController::class, 'destroy']);
    });

});

/*
|---------- Legacy / Fallback ----------|
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
