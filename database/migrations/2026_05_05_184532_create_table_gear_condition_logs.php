<?php
/*******************************************************************
 * Migration untuk tabel gear_condition_logs.
 * Digunakan untuk mencatat perubahan kondisi alat setiap kali ada transaksi.
 *******************************************************************
 * Kolom:
 * - id: Primary key, auto-increment.
 * - gear_id: Foreign key ke tabel gears (alat yang kondisinya dicatat).
 * - condition_before: Kondisi alat sebelum transaksi (string).
 * - condition_after: Kondisi alat setelah transaksi (string).
 * - note: Keterangan fleksibel dari admin (text, nullable).
 * - timestamps: created_at dan updated_at.
 *******************************************************************
 * Contoh penggunaan:
 * Saat pelanggan mengembalikan barang, admin mencatat kondisi sebelum dan sesudahnya,
 * serta memberikan catatan jika ada kerusakan atau kehilangan.
 ******************************************************************* 
*/
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gear_condition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gear_id')->constrained()->onDelete('cascade');
            $table->string('condition_before');
            $table->string('condition_after');
            $table->text('note')->nullable(); // Keterangan fleksibel dari admin
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gear_condition_logs');
    }
};