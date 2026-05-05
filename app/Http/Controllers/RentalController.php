<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// =========================================================

// CONTROLLER RENTAL = Controller logika bisnis terkait :
//  1. Transaksi penyewaan, 
//  2. Fitur menyelesaikan transaksi (mengembalikan barang) dan,
//  3. Menghitung denda jika terlambat. 
// Controller ini akan berinteraksi dengan model Rental untuk memperbarui status penyewaan dan menghitung denda.

// =========================================================


class RentalController extends Controller
{
    /**
     * 1. TAMPILIN DAFTAR RENTAL AKTIF + ESTIMASI DENDA
     * Buat dashboard admin
     */
    public function indexActive()
    {
        $rentals = Rental::with('gear', 'user')
            ->where('status', 'active')
            ->get()
            ->map(function ($rental) {
                // Hitung estimasi denda real-time buat dashboard
                $penalty = $this->calculatePenalty($rental);
                $rental->estimated_penalty = $penalty['total'];
                $rental->days_late = $penalty['hari'];
                return $rental;
            });

        return response()->json([
            'data' => $rentals,
            'total_estimasi_denda' => $rentals->sum('estimated_penalty')
        ]);
    }

    /**
     * 2. LIHAT DETAIL TAGIHAN SEBELUM SELESAI
     * Admin buka dulu buat mastiin denda sebelum klik "Selesai"
     */
    public function showInvoice($id)
    {
        $rental = Rental::with('gear', 'user')->findOrFail($id);

        if ($rental->status === 'completed') {
            return response()->json([
                'message' => 'Rental sudah selesai',
                'data' => $rental
            ], 400);
        }

        $penalty = $this->calculatePenalty($rental);
        $totalSewa = $rental->total_price; // Asumsi udah ada di tabel
        $grandTotal = $totalSewa + $penalty['total'];

        return response()->json([
            'rental' => $rental,
            'rincian_biaya' => [
                'biaya_sewa' => $totalSewa,
                'hari_terlambat' => $penalty['hari'],
                'denda_per_hari' => $rental->gear->penalty_fee ?? 50000,
                'total_denda' => $penalty['total'],
                'grand_total' => $grandTotal
            ]
        ]);
    }

    /**
     * 3. SELESAIKAN SEWA - TOMBOL "SELESAI" DIKLIK ADMIN
     * Ini yang ngunci denda final + ganti status
     */
    public function completeRental($id)
    {
        $rental = Rental::with('gear')->findOrFail($id);

        if ($rental->status === 'completed') {
            return response()->json(['message' => 'Rental sudah diselesaikan sebelumnya'], 400);
        }

        if ($rental->status === 'cancelled') {
            return response()->json(['message' => 'Rental sudah dibatalkan'], 400);
        }

        DB::beginTransaction();
        try {
            // Hitung denda final pake waktu sekarang
            $penalty = $this->calculatePenalty($rental);
            
            $rental->update([
                'status' => 'completed',
                'returned_at' => Carbon::now(),
                'penalty_amount' => $penalty['total'],
                'total_days_late' => $penalty['hari'],
                'final_amount' => $rental->total_price + $penalty['total'], // Total akhir
            ]);

            // Balikin stok barang biar bisa disewa lagi
            $rental->gear->increment('stock');

            DB::commit();

            return response()->json([
                'message' => 'Barang berhasil dikembalikan!',
                'data' => $rental->fresh(), // Ambil data terbaru
                'rincian' => [
                    'biaya_sewa' => $rental->total_price,
                    'hari_terlambat' => $penalty['hari'],
                    'total_denda' => $penalty['total'],
                    'total_bayar' => $rental->final_amount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 4. BATALKAN SEWA
     * Kalau customer batal sebelum ambil barang
     */
    public function cancelRental($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'active') {
            return response()->json(['message' => 'Hanya rental aktif yang bisa dibatalkan'], 400);
        }

        // Kalau udah lewat start_date, mungkin kena cancel fee. Ini opsional
        $rental->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now()
        ]);

        // Balikin stok karena batal
        $rental->gear->increment('stock');

        return response()->json(['message' => 'Rental berhasil dibatalkan']);
    }

    /**
     * PRIVATE FUNCTION: HITUNG DENDA
     * Dipake di semua method biar nggak copy paste rumus
     * Ini isinya logika "gratis 2 jam" yang sama kayak di Command
     */
    private function calculatePenalty($rental)
    {
        $dueDate = $rental->end_date; // Udah Carbon karena casts
        $batasGratis = $dueDate->copy()->addHours(2);
        $sekarang = Carbon::now();

        if ($sekarang->gt($batasGratis)) {
            $selisihJam = $sekarang->diffInHours($batasGratis);
            $hariTerlambat = (int) ceil($selisihJam / 24); // 1-24 jam = 1 hari
            $dendaPerHari = $rental->gear->penalty_fee ?? 50000;
            
            return [
                'hari' => $hariTerlambat,
                'total' => $hariTerlambat * $dendaPerHari
            ];
        }

        return ['hari' => 0, 'total' => 0];
    }
}