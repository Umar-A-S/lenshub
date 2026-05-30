<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->after('total_denda');
            $table->string('status_pembayaran', 50)->default('belum_lunas')->after('deskripsi');
            $table->string('metode_pembayaran', 50)->nullable()->after('status_pembayaran');
            $table->dateTime('dibayar_pada')->nullable()->after('metode_pembayaran');
            $table->string('tipe_pelanggaran', 50)->nullable()->after('dibayar_pada');
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi', 'status_pembayaran', 'metode_pembayaran', 'dibayar_pada', 'tipe_pelanggaran'
            ]);
        });
    }
};
