<?php

namespace App\Models;

/**
 * Model Gear
 * Mengelola data alat yang disewakan, termasuk informasi kategori, harga, status, dan kondisi.
 * Setiap baris mewakili satu unit alat dengan kode unik (unit_code).
 * Fitur Soft Delete digunakan untuk menjaga data tetap aman saat dihapus.
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini untuk fitur Soft Delete

class Gear extends Model
{
    use HasFactory, SoftDeletes; // Gunakan SoftDeletes agar data tidak benar-benar hilang saat dihapus

    /**
     * Update fillable:
     * - Hapus 'category' (karena sekarang pakai category_id)
     * - Hapus 'total_units' (karena 1 baris = 1 unit)
     * - Tambahkan unit_code, status, dan condition_status
     */
    protected $fillable = [
        'category_id',
        'name',
        'unit_code',
        'rent_price',
        'penalty_fee',
        'status',
        'condition_status',
        'photo'
    ];

    /**
     * Relasi: Satu unit barang masuk ke dalam satu kategori.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Satu unit barang bisa memiliki banyak catatan riwayat kondisi.
     */
    public function conditionLogs()
    {
        return $this->hasMany(GearConditionLog::class);
    }

    /**
     * Relasi: Satu unit barang bisa memiliki banyak transaksi penyewaan (Rentals).
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}