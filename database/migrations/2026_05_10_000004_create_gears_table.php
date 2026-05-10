<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gears', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 100);
            $table->unsignedInteger('stock_total')->default(1);
            $table->unsignedInteger('stock_available')->default(1);
            $table->decimal('price_per_hour', 12, 2);
            $table->decimal('price_per_day', 12, 2);
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gears');
    }
};
