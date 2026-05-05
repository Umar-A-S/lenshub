<?php

/*******************************************************************
 * Migration untuk tabel categories.
 * Digunakan untuk pengelompokan jenis barang (Kamera, Lensa, dll).
 *******************************************************************
 * Kolom:
 * - id: Primary key, auto-increment.
 * - name: Nama kategori (string).
 * - slug: Versi URL-friendly dari nama kategori (string, unique).
 * - prefix: Prefix untuk unit_code otomatis (string, max 5 karakter).
 * - timestamps: created_at dan updated_at.
 *******************************************************************
 * Contoh data:
 * | id | name   | slug   | prefix |
 * |----|--------|--------|--------|
 * | 1  | Kamera | kamera | CAM    |
 * | 2  | Lensa  | lensa  | LENS   |
 ******************************************************************* 
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Kamera, Lensa
            $table->string('slug')->unique(); // Contoh: kamera, lensa
            $table->string('prefix', 5); // Contoh: CAM, LENS (untuk unit_code otomatis)
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('categories');
    }
};