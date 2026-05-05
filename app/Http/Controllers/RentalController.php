<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RentalController extends Controller
{
    //
    public function completeRental($id)
    {
        $rental = Rental::findOrFail($id);
        
        // Hitung selisih hari antara janji kembali (end_date) dengan hari ini (saat dikembalikan)
        $dueDate = Carbon::parse($rental->end_date);
        $now = now();

        if ($now->gt($dueDate)) {
            $hariTerlambat = $now->diffInDays($dueDate);
            $dendaPerHari = 50000; // Bisa diambil dari kolom 'penalty_fee' di tabel gears
            $rental->penalty_amount = $hariTerlambat * $dendaPerHari;
        }

        $rental->status = 'completed';
        $rental->returned_at = $now;
        $rental->save();

        return response()->json(['message' => 'Barang kembali! Total denda: ' . $rental->penalty_amount]);
    }
}
