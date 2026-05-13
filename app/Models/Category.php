<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'prefix'];

    public function gears()
    {
        return $this->hasMany(Gear::class);
    }

    // Fungsi simpel untuk generate kode baru
    public function generateNewCode()
    {
        // Hitung gear yang sudah ada di kategori ini (termasuk yang di-softdelete agar nomor tidak bentrok)
        $nextNumber = $this->gears()->withTrashed()->count() + 1;
        
        // Gabungkan PREFIX + 00 + urutan (Contoh: CAM_001)
        return $this->prefix . '_' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}