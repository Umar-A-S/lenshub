<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'rental_id',
        'terlambat',
        'telat_jam',
        'tarif_per_jam',
        'deskripsi_kerusakan',
        'biaya_kerusakan',
        'total_denda',
        'metode_bayar_denda',
        'dibayar_pada',
        'status',
    ];

    protected $casts = [
        'terlambat'           => 'boolean',
        'telat_jam'           => 'integer',
        'tarif_per_jam'       => 'decimal:2',
        'biaya_kerusakan'     => 'decimal:2',
        'total_denda'         => 'decimal:2',
        'dibayar_pada'        => 'datetime',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
