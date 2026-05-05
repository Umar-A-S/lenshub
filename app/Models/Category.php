<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Category
 * Mengelola kategori barang seperti Kamera, Lensa, dll.
 * Menyimpan 'prefix' untuk kebutuhan pembuatan unit_code otomatis.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'prefix'
    ];

    /**
     * Relasi: Satu kategori memiliki banyak barang (Gears).
     */
    public function gears()
    {
        return $this->hasMany(Gear::class);
    }

    /**
     * Fungsi untuk mendapatkan nomor urut berikutnya untuk kategori ini.
     * Berguna untuk pembuatan unit_code otomatis.
     */
    public function getNextUnitNumber()
    {
        // Mengambil gear terakhir yang dibuat dalam kategori ini, termasuk yang di-soft delete
        $lastGear = $this->gears()->withTrashed()->latest('id')->first();

        if (!$lastGear) {
            return 1;
        }

        // Mengambil angka dari unit_code (misal CAM_005 -> ambil 5)
        $lastNumber = (int) explode('_', $lastGear->unit_code)[1];
        
        return $lastNumber + 1;
    }
}