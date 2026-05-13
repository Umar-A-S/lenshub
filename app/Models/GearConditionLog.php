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
        'old_condition',
        'new_condition',
        'notes',
        'changed_by'
    ];

    /**
     * Relasi balik ke model Gear.
     */
    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    /**
     * Relasi ke model User yang melakukan perubahan.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}