<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table
                ->string('ktp')
                ->unique();

            $table->string('phone');

            $table->text('alamat');

            $table
                ->enum(
                    'status',
                    [
                        'aktif',
                        'nonaktif'
                    ]
                )
                ->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};