<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Carbon\Carbon;

class AutoCalculatePenalty extends Command
{
    protected $signature = 'app:auto-calculate-penalty';
    protected $description = 'Menghitung denda otomatis untuk penyewaan yang melewati batas waktu';

    public function handle()
    {
        // 1. Ambil rental yang 'active' DAN udah lewat 2 jam dari end_date
        $batasWaktuKenaDenda = Carbon::now()->subHours(2);
        
        $dataRentalTelat = Rental::where('status', 'active')
            ->where('end_date', '<', $batasWaktuKenaDenda)
            ->with('gear') // Eager load biar nggak N+1 query
            ->get();

        $this->info("Ditemukan " . $dataRentalTelat->count() . " transaksi kena denda.");

        foreach ($dataRentalTelat as $rental) {
            // 2. $rental->end_date otomatis Carbon kalau di Model udah di casts
            $dueDate = $rental->end_date; 
            $batasGratis = $dueDate->copy()->addHours(2);
            $sekarang = Carbon::now();

            // 3. Hitung selisih hari. Lewat 1 detik dari batas = 1 hari
            $selisihJam = $sekarang->diffInHours($batasGratis);
            $hariTerlambat = 0;
            
            if ($selisihJam >= 0) { // Pastikan emang udah lewat
                $hariTerlambat = (int) ceil($selisihJam / 24); // 1 jam = 1 hari, 25 jam = 2 hari
            }

            if ($hariTerlambat > 0) {
                $dendaPerHari = $rental->gear->penalty_fee ?? 0; 
                $totalDenda = $hariTerlambat * $dendaPerHari;

                // 4. Update database
                $rental->update([
                    'penalty_amount' => $totalDenda,
                    'total_days_late' => $hariTerlambat, // Simpen sekalian
                    'note' => "Sistem: Terlambat {$hariTerlambat} hari. Denda update " . Carbon::now()->format('d-m-Y H:i')
                ]);

                $this->info("ID {$rental->id} ({$rental->gear->name}): Telat {$hariTerlambat} hari. Denda Rp" . number_format($totalDenda));
            }
        }

        $this->info("Proses selesai.");
    }
}