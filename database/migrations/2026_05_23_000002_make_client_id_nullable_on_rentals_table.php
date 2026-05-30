<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan client_id nullable agar sewa mandiri (tanpa client)
     * bisa dilakukan langsung oleh user yang sudah login.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Drop foreign key lama dulu, lalu recreate sebagai nullable
            $table->dropForeign(['client_id']);
            $table->foreignId('client_id')
                  ->nullable()
                  ->change();
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreignId('client_id')
                  ->nullable(false)
                  ->change();
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->cascadeOnDelete();
        });
    }
};
