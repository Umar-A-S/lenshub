<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_rentals_table.php

    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang sewa
            $table->foreignId('gear_id')->constrained()->onDelete('cascade'); // Alat apa yang disewa
            
            $table->date('start_date'); // Tanggal mulai booking
            $table->date('end_date');   // Tanggal seharusnya kembali
            $table->timestamp('returned_at')->nullable(); // Tanggal aktual kembali
            
            $table->integer('total_price'); // Total harga sewa di awal
            $table->integer('penalty_amount')->default(0); // Denda yang terakumulasi
            
            // Status: booking (dipesan), active (barang dibawa), completed (kembali), cancelled (batal)
            $table->enum('status', ['booking', 'active', 'completed', 'cancelled'])->default('booking');
            
            $table->text('note')->nullable(); // Catatan jaminan (KTP/SIM) atau kondisi barang
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
