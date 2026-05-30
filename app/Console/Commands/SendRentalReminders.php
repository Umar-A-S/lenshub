<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendRentalReminders extends Command
{
    protected $signature   = 'rental:send-reminders';
    protected $description = 'Kirim pengingat WA otomatis saat 2 jam, 1 jam, dan 30 menit sebelum jatuh tempo';

    // Interval reminder dalam menit
    private const REMINDERS = [120, 60, 30];

    public function handle(): void
    {
        $now = Carbon::now(config('app.timezone'));

        $rentals = Rental::with('items.equipment')
            ->where('status', 'aktif')
            ->get();

        foreach ($rentals as $rental) {
            $jatuhTempo = $rental->jatuh_tempo instanceof Carbon
                ? $rental->jatuh_tempo
                : Carbon::parse($rental->jatuh_tempo, config('app.timezone'));

            $sisaMenit = $now->diffInMinutes($jatuhTempo, false); // negatif = sudah lewat

            foreach (self::REMINDERS as $target) {
                // Cocok jika sisa waktu dalam window ±2 menit dari target
                if ($sisaMenit > ($target - 2) && $sisaMenit <= $target) {
                    $cacheKey = "reminder_{$rental->id}_{$target}";

                    // Pastikan hanya kirim sekali per rental per target
                    if (!Cache::has($cacheKey)) {
                        $sent = WhatsAppService::kirimPengingatJatuhTempo($rental, $target);

                        if ($sent) {
                            // Tandai sudah terkirim, cache 30 menit
                            Cache::put($cacheKey, true, now()->addMinutes(30));
                            $this->info("✅ Reminder {$target}m → {$rental->kode_sewa} ({$rental->whatsapp})");
                            Log::info("[Reminder] Sent {$target}m reminder for {$rental->kode_sewa}");
                        } else {
                            $this->warn("❌ Gagal kirim reminder ke {$rental->whatsapp}");
                        }
                    }
                }
            }
        }

        $this->info("Selesai memproses {$rentals->count()} sewa aktif.");
    }
}
