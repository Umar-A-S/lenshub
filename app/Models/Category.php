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
}