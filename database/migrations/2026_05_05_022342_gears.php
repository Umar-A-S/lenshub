<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_gears_table.php

    public function up(): void
    {
        Schema::create('gears', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Kamera, Lensa, Tripod, dll
            $table->integer('total_units')->default(1); // Jumlah stok fisik
            $table->integer('rent_price'); // Harga sewa per hari
            $table->integer('penalty_fee'); // Denda per hari jika telat
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
