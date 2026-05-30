<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {

            if (!Schema::hasColumn('rentals','no_wa')) {
                $table->string('no_wa',30)->nullable()->after('status');
            }

            if (!Schema::hasColumn('rentals','metode_logistik')) {
                $table->string('metode_logistik',30)->nullable()->after('no_wa');
            }

            if (!Schema::hasColumn('rentals','alamat_pengiriman')) {
                $table->text('alamat_pengiriman')->nullable()->after('metode_logistik');
            }

            if (!Schema::hasColumn('rentals','jenis_jaminan')) {
                $table->string('jenis_jaminan',50)->nullable();
            }

            if (!Schema::hasColumn('rentals','metode_pembayaran')) {
                $table->string('metode_pembayaran',50)->nullable();
            }

            if (!Schema::hasColumn('rentals','status_pembayaran')) {
                $table->string('status_pembayaran',50)
                    ->default('pending');
            }

            if (!Schema::hasColumn('rentals','catatan_kondisi')) {
                $table->text('catatan_kondisi')->nullable();
            }

            if (!Schema::hasColumn('rentals','status_pengembalian')) {
                $table->string('status_pengembalian',50)
                    ->default('menunggu');
            }

            if (!Schema::hasColumn('rentals','status_denda')) {
                $table->string('status_denda',50)
                    ->default('tidak_ada');
            }

            if (!Schema::hasColumn('rentals','booking_until')) {
                $table->dateTime('booking_until')->nullable();
            }

            if (!Schema::hasColumn('rentals','durasi_label')) {
                $table->string('durasi_label',20)->nullable();
            }

            if (!Schema::hasColumn('rentals','diskon_persen')) {
                $table->decimal('diskon_persen',5,2)->default(0);
            }

            if (!Schema::hasColumn('rentals','subtotal')) {
                $table->decimal('subtotal',12,2)->default(0);
            }

        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'no_wa', 'metode_logistik', 'alamat_pengiriman', 'jenis_jaminan',
                'metode_pembayaran', 'status_pembayaran', 'catatan_kondisi',
                'status_pengembalian', 'status_denda', 'booking_until',
                'durasi_label', 'diskon_persen', 'subtotal'
            ]);
        });
    }
};