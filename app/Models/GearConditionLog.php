<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model GearConditionLog 
 * Mencatat riwayat setiap perubahan kondisi fisik unit gear.
 * Setiap kali ada transaksi penyewaan atau pengembalian, admin dapat mencatat kondisi sebelum dan sesudahnya,
 * serta memberikan catatan tambahan jika ada kerusakan atau kehilangan.
 */
class GearConditionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'gear_id',
        'condition_before',
        'condition_after',
        'note'
    ];

    /**
     * Relasi balik ke model Gear.
     */
    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }
}