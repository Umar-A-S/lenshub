<?php

use Illuminate\Support\Facades\Route;
use App\Models\Rental;


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