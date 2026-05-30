<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_items', function (Blueprint $table) {

            $table->id();

            $table
                ->foreignId('rental_id')
                ->constrained('rentals')
                ->cascadeOnDelete();

            $table
                ->foreignId('equipment_id')
                ->constrained('equipments')
                ->cascadeOnDelete();

            $table
                ->integer('qty')
                ->default(1);

            $table
                ->decimal(
                    'harga',
                    12,
                    2
                );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};