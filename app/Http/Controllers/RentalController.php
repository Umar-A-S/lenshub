<?php

/**
 * RANGKUMAN CONTROLLER:
 * File ini bertugas sebagai "Polisi Lalu Lintas". 
 * Dia tidak menghitung denda secara manual, melainkan hanya memanggil "label/accessor" 
 * yang sudah disiapkan di Model Rental. 
 * Tugas utamanya: 
 * 1. Menampilkan daftar sewa (index).
 * 2. Menampilkan detail biaya sebelum admin klik selesai (detailTagihan).
 * 3. Menjalankan proses transaksi 'Selesai' atau 'Batal' yang akan 
 *    mengunci angka denda ke database dan mengembalikan stok barang.
 */

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * Menampilkan semua daftar rental.
     */
    public function index()
    {
        $rentals = Rental::with(['gear', 'user'])->latest()->get();
        return view('admin.rentals.index', compact('rentals'));
    }

    /**
     * Detail tagihan untuk pengecekan admin sebelum selesai.
     */
    public function detailTagihan($id)
    {
        $rental = Rental::with('gear')->findOrFail($id);

        return response()->json([
            'status'  => $rental->status,
            'rincian' => [
                'biaya_sewa'     => $rental->total_price,
                'hari_terlambat' => $rental->penalty_details['days'],
                'total_denda'    => $rental->penalty_details['total'],
                'total_bayar'    => $rental->grand_total
            ]
        ]);
    }

    /**
     * Mengunci transaksi (Selesai).
     */
    public function selesaiRental($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'active') {
            return response()->json(['message' => 'Hanya rental aktif yang bisa diselesaikan'], 400);
        }

        DB::transaction(function () use ($rental) {
            // Ambil data denda dari Accessor Model
            $penalty = $rental->penalty_details;

            $rental->update([
                'status'            => 'completed',
                'returned_at'       => Carbon::now(),
                'penalty_amount'    => $penalty['total'],
                'total_days_late'   => $penalty['days'],
                'final_amount'      => $rental->grand_total,
            ]);

            // Kembalikan stok gear
            $rental->gear->increment('stock');
        });

        return response()->json(['message' => 'Pengembalian berhasil dicatat!']);
    }

    /**
     * Membatalkan pesanan.
     */
    public function cancelRental($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'active') {
            return response()->json(['message' => 'Gagal, status tidak aktif'], 400);
        }

        DB::transaction(function () use ($rental) {
            $rental->update([
                'status'       => 'cancelled',
                'cancelled_at' => Carbon::now()
            ]);
            $rental->gear->increment('stock');
        });

        return response()->json(['message' => 'Rental dibatalkan']);
    }
}