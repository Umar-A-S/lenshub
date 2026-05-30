<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {

            $table->id();

            $table
                ->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('nama');

            $table
                ->text('deskripsi')
                ->nullable();

            $table
                ->integer('stok')
                ->default(0);

            $table
                ->decimal(
                    'harga_harian',
                    12,
                    2
                );

            $table
                ->string('foto')
                ->nullable();

            $table
                ->enum(
                    'status',
                    [
                        'tersedia',
                        'disewa',
                        'maintenance'
                    ]
                )
                ->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};