<?php

/*******************************************************************
 * Migration untuk menambahkan kolom penalty_amount, total_days_late, dan final_amount pada tabel rentals.
 * Digunakan untuk menyimpan informasi tentang denda keterlambatan dan jumlah akhir yang harus dibayar.
 *******************************************************************
 * Kolom yang ditambahkan:
 * - penalty_amount: Jumlah denda keterlambatan (bigInteger, default 0).
 * - total_days_late: Total hari keterlambatan (integer, default 0).
 * - final_amount: Jumlah akhir yang harus dibayar setelah ditambahkan denda (bigInteger, default 0).
 *******************************************************************
 * Contoh penggunaan:
 * Saat pelanggan mengembalikan barang terlambat, sistem akan menghitung total_days_late dan penalty_amount,
 * kemudian menghitung final_amount sebagai total_price + penalty_amount.
 ******************************************************************* 
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->integer('total_days_late')->default(0)->after('penalty_amount');
            $table->bigInteger('final_amount')->default(0)->after('total_price');
        });
    }

    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['total_days_late', 'final_amount']);
        });
    }
};
