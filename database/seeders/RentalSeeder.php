<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\User;
use App\Models\Gear;
use Carbon\Carbon;

/**
 * RentalSeeder
 * Digunakan untuk mengisi data simulasi transaksi penyewaan.
 */
class RentalSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada User dan Gear terlebih dahulu
        $user = User::first() ?? User::factory()->create(['name' => 'Umar Alfi']);
        $camera = Gear::where('unit_code', 'LIKE', 'CAM%')->first();
        $lens = Gear::where('unit_code', 'LIKE', 'LENS%')->first();

        if (!$camera || !$lens) {
            $this->command->error("Gear tidak ditemukan! Jalankan InventorySeeder terlebih dahulu.");
            return;
        }

        // Skenario 1: Transaksi Masih Booking (Akan datang)
        Rental::create([
            'user_id' => $user->id,
            'gear_id' => $camera->id,
            'start_date' => Carbon::now()->addDays(2),
            'end_date' => Carbon::now()->addDays(4),
            'total_price' => $camera->rent_price * 2,
            'status' => 'booking',
            'note' => 'Jaminan KTP asli',
        ]);

        // Skenario 2: Transaksi Aktif (Sedang dibawa, belum telat)
        Rental::create([
            'user_id' => $user->id,
            'gear_id' => $lens->id,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
            'total_price' => $lens->rent_price * 2,
            'status' => 'active',
            'note' => 'Jaminan SIM A',
        ]);

        // Skenario 3: Transaksi Selesai (Sudah kembali tepat waktu)
        Rental::create([
            'user_id' => $user->id,
            'gear_id' => $camera->id,
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->subDays(3),
            'returned_at' => Carbon::now()->subDays(3),
            'total_price' => $camera->rent_price * 2,
            'status' => 'completed',
            'note' => 'Barang kembali dalam kondisi sangat baik',
        ]);

        // Skenario 4: Transaksi Aktif (SIAP DIUJI TELAT)
        // Kita set end_date kemarin agar muncul denda saat halaman di-refresh
        Rental::create([
            'user_id' => $user->id,
            'gear_id' => $camera->id,
            'start_date' => Carbon::now()->subDays(3),
            'end_date' => Carbon::now()->subDay(),
            'total_price' => $camera->rent_price * 2,
            'status' => 'active',
            'note' => 'Cek denda otomatis untuk unit ini',
        ]);
    }
} 
