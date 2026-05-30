<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            // client_id nullable — sewa mandiri via login tidak wajib punya client
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            // user_id — siapa yang login dan membuat pesanan
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('kode_sewa')->unique();

            // Data penyewa
            $table->string('nama_penyewa')->nullable();
            $table->string('whatsapp', 20)->nullable();

            // Waktu sewa
            $table->dateTime('mulai');
            $table->dateTime('jatuh_tempo');
            $table->string('durasi', 20)->nullable();

            // Logistik
            $table->string('logistik', 20)->nullable();
            $table->text('alamat_pengiriman')->nullable();

            // Keuangan
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('denda', 12, 2)->default(0);

            // Status transaksi
            $table->string('status')->default('pending');
            $table->string('status_bayar', 50)->default('pending');

            // Konfirmasi tatap muka
            $table->string('jaminan_fisik', 50)->nullable();
            $table->string('metode_bayar', 50)->nullable();
            $table->text('catatan_kondisi')->nullable();

            // Pengembalian
            $table->timestamp('dikembalikan_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};