<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gear_id',
        'start_date',
        'end_date',
        'returned_at',
        'cancelled_at', // Udah ada di casts tapi belum di fillable
        'total_price',
        'penalty_amount',
        'total_days_late', // TAMBAH INI
        'final_amount',    // TAMBAH INI
        'status',
        'note'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime', 
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relasi Balik: Satu transaksi penyewaan dimiliki oleh satu Alat (Gear).
     */
    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    /**
     * Relasi Balik: Satu transaksi penyewaan dimiliki oleh satu Pengguna (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Cek apakah rental ini telat atau belum
     * Dipake: $rental->is_late
     */
    public function getIsLateAttribute()
    {
        if ($this->status !== 'active') return false;
        return Carbon::now()->gt($this->end_date->copy()->addHours(2));
    }
}