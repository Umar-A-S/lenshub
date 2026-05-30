<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {

            // cukup ubah status
            $table->string('status')
                ->default('pending')
                ->change();

            // TAMBAH HANYA YANG BELUM ADA
            if (!Schema::hasColumn('rentals', 'dikembalikan_at')) {
                $table->timestamp('dikembalikan_at')
                    ->nullable();
            }
        });

        Schema::table('fines', function (Blueprint $table) {

            if (!Schema::hasColumn('fines', 'terlambat')) {
                $table->boolean('terlambat')
                    ->default(false);
            }

            $table->integer('telat_jam')
                ->default(0)
                ->change();

            if (!Schema::hasColumn('fines', 'deskripsi_kerusakan')) {
                $table->text('deskripsi_kerusakan')
                    ->nullable();
            }

            if (!Schema::hasColumn('fines', 'biaya_kerusakan')) {
                $table->decimal(
                    'biaya_kerusakan',
                    12,
                    2
                )->default(0);
            }

            if (!Schema::hasColumn('fines', 'metode_bayar_denda')) {
                $table->string('metode_bayar_denda')
                    ->nullable();
            }
        });
    }
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'nama_penyewa','whatsapp','durasi','logistik','alamat_pengiriman',
                'jaminan_fisik','metode_bayar','status_bayar','catatan_kondisi','dikembalikan_at'
            ]);
        });
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn(['terlambat','deskripsi_kerusakan','biaya_kerusakan','metode_bayar_denda']);
        });
    }
};