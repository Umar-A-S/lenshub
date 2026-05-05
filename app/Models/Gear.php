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

    /**
     * Method boot untuk menjalankan logika otomatis saat model berinteraksi dengan database.
     */
    protected static function booted()
    {
        static::creating(function ($gear) {
            // Hanya jalankan jika unit_code belum diisi secara manual
            if (!$gear->unit_code) {
                $category = $gear->category; // Mengambil relasi kategori
                
                if ($category) {
                    // Ambil nomor urut berikutnya
                    $nextNumber = $category->getNextUnitNumber();
                    
                    // Format nomor menjadi 3 digit (misal: 1 jadi 001)
                    $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    
                    // Gabungkan prefix kategori dengan nomor urut
                    $gear->unit_code = "{$category->prefix}_{$formattedNumber}";
                }
            }
        });
    }

    /**
     * Scope untuk menyaring barang berdasarkan kategori.
     * Memudahkan pemanggilan: Gear::byCategory(1)->get();
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope untuk menyaring barang yang sedang tersedia.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}