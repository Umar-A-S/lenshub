<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gear extends Model
{
    use HasFactory;

    /**
     * fillable digunakan untuk menentukan kolom mana saja yang boleh diisi
     * secara massal (Mass Assignment). Ini penting untuk keamanan.
     */
    protected $fillable = [
        'name',
        'category',
        'total_units',
        'rent_price',
        'penalty_fee',
        'description'
    ];

    /**
     * Relasi: Satu alat (Gear) bisa memiliki banyak transaksi penyewaan (Rentals).
     * Kita menggunakan ::class untuk merujuk ke model Rental.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}