<?php

/*******************************************************************
 * Migration untuk tabel gears.
 * Digunakan untuk menyimpan data alat yang disewakan.
 *******************************************************************
 * Kolom:
 * - id: Primary key, auto-increment.
 * - category_id: Foreign key ke tabel categories (kategori alat).
 * - name: Nama alat (string).
 * - unit_code: Kode unik untuk setiap unit (string, unique).
 * - category: Kategori alat (string, contoh: Kamera, Lensa).
 * - total_units: Jumlah stok fisik (integer).
 * - rent_price: Harga sewa per hari (integer).
 * - penalty_fee: Denda per hari jika telat (integer).
 * - description: Deskripsi alat (text, nullable).
 * - status: Enum (available, booked, rented, maintenance) untuk status alat.
 * - condition: Enum (baik, rusak, hilang, maintenance) untuk kondisi alat.
 * - image_path: Path untuk menyimpan gambar alat (string, nullable).
 * - timestamps: created_at dan updated_at.
 *******************************************************************
 * Contoh penggunaan:
 * Saat admin menambahkan alat baru, status akan menjadi 'available'.
 * Saat pelanggan melakukan booking, status berubah menjadi 'booked'.
 * Saat pelanggan mengambil barang, status berubah menjadi 'rented'.
 * Saat barang dikembalikan atau masuk perbaikan, status berubah sesuai kondisi.
 ******************************************************************* 
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained(); // Kategori alat (relasi ke tabel categories)
            $table->string('name');
            $table->string('unit_code')->unique(); // Kode unik untuk setiap unit (otomatis dari kategori + nomor urut)
            $table->integer('rent_price'); // Harga sewa per hari
            $table->integer('penalty_fee'); // Denda per hari jika telat
            $table->text('description')->nullable();
            $table->timestamps();

            $table->enum('status', ['available', 'booked', 'rented', 'maintenance'])
                ->default('available'); // Status alat (available (tersedia), booked (dipesan), rented (disewa), maintenance (perbaikan))

            $table->enum('condition_status', ['baik', 'rusak', 'hilang', 'maintenance'])
                ->default('baik'); // Kondisi alat (baik, rusak, hilang, maintenance)

            $table->string('image_path')->nullable(); // Path untuk menyimpan gambar alat
            $table->softDeletes(); // Fitur Soft Delete agar data aman
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gears');
    }
};
