<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:auto-calculate-penalty')]
#[Description('Command description')]
class AutoCalculatePenalty extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua rental yang statusnya masih 'active' dan sudah melewati 'end_date'
        $overdueRentals = Rental::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        foreach ($overdueRentals as $rental) {
            $hariTerlambat = now()->diffInDays($rental->end_date);
            
            // Misal denda flat Rp 50.000 per hari
            $dendaPerHari = 50000; 
            $totalDenda = $hariTerlambat * $dendaPerHari;

            $rental->update([
                'penalty_amount' => $totalDenda
            ]);

            $this->info("User {$rental->user_id} telat {$hariTerlambat} hari. Denda: {$totalDenda}");
        }
    }
}
