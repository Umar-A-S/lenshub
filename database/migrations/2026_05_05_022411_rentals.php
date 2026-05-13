<?php

/********************************************************************
 * Migration untuk tabel rentals.
 * Digunakan untuk menyimpan data transaksi penyewaan alat.
 *******************************************************************
 * Kolom:
 * - id: Primary key, auto-increment.
 * - user_id: Foreign key ke tabel users (siapa yang sewa).
 * - gear_id: Foreign key ke tabel gears (alat apa yang disewa).
 * - start_date: Tanggal mulai booking.
 * - end_date: Tanggal seharusnya kembali.
 * - returned_at: Tanggal aktual kembali (nullable).
 * - total_price: Total harga sewa di awal (integer).
 * - penalty_amount: Denda yang terakumulasi (integer, default 0).
 * - status: Enum (booking, active, completed, cancelled) untuk status transaksi.
 * - note: Catatan jaminan (KTP/SIM) atau kondisi barang (nullable).
 * - timestamps: created_at dan updated_at.
 *******************************************************************
 * Contoh penggunaan:
 * Saat pelanggan melakukan booking, status akan menjadi 'booking'.
 * Saat pelanggan mengambil barang, status berubah menjadi 'active'.
 * Saat pelanggan mengembalikan barang, status berubah menjadi 'completed' dan returned_at diisi.
 ******************************************************************* 
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            // Unique Identifier untuk konfirmasi WA
            $table->string('booking_code')->unique(); 
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gear_id')->constrained()->onDelete('cascade');

            $table->string('whatsapp');
            $table->text('alamat');
            $table->time('start_time'); // Untuk detail jam ambil
            $table->string('purpose');
            $table->string('payment_method');
            
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('cancelled_at')->nullable(); // Ditambahkan untuk log pembatalan
            
            $table->integer('total_price'); 
            $table->integer('penalty_amount')->default(0);
            $table->integer('total_days_late')->default(0); // Ditambahkan
            $table->integer('final_amount')->nullable(); // Nilai terkunci saat selesai

            $table->string('foto_ktp')->nullable(); // Simpan path foto KTP

            $table->enum('status', ['booking', 'active', 'completed', 'cancelled'])->default('booking');
            
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('rentals');
    }
};
