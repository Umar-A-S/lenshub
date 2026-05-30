<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'category_id',
        'nama',
        'deskripsi',
        'stok',
        'harga_harian',
        'foto',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class);
    }
}