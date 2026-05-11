<?php

/**
 * RANGKUMAN MODEL:
 * File ini adalah "Otak Dasar" dari data Rental. 
 * Di sini tempat berkumpulnya semua rumus hitung-hitungan agar Controller tetap bersih.
 * Fitur Utama (Accessor):
 * 1. 'penalty_details': Otomatis menghitung apakah user telat (lewat toleransi 2 jam), 
 *    berapa hari telatnya, dan berapa total dendanya.
 * 2. 'grand_total': Otomatis menjumlahkan (Harga Sewa + Denda).
 * Dengan file ini, kita cukup panggil $rental->penalty_details di mana saja (Controller/View)
 * tanpa perlu menulis ulang rumus matematika yang rumit.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'gear_id', 'start_date', 'end_date', 'returned_at', 
        'cancelled_at', 'total_price', 'penalty_amount', 'total_days_late', 
        'final_amount', 'status', 'note'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function gear() { return $this->belongsTo(Gear::class); }
    public function user() { return $this->belongsTo(User::class); }

    // --- ACCESSORS ---

    /**
     * Menghitung rincian denda secara otomatis.
     * Akses: $rental->penalty_details
     */
    protected function penaltyDetails(): Attribute
    {
        return Attribute::make(
            get: function () {
                $dueDate = $this->end_date;
                $gracePeriod = $dueDate->copy()->addHours(2); // Toleransi 2 jam
                $now = Carbon::now();

                if ($now->gt($gracePeriod)) {
                    $hoursLate = $now->diffInHours($gracePeriod);
                    $daysLate = (int) ceil($hoursLate / 24);
                    $feePerDay = $this->gear->penalty_fee ?? 50000;

                    return [
                        'is_late' => true,
                        'days'    => $daysLate,
                        'hours'   => $now->diffInHours($gracePeriod) % 24,
                        'total'   => $daysLate * $feePerDay
                    ];
                }

                return ['is_late' => false, 'days' => 0, 'total' => 0];
            }
        );
    }

    /**
     * Menghitung total yang harus dibayar (Sewa + Denda).
     * Akses: $rental->grand_total
     */
    protected function grandTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_price + $this->penalty_details['total']
        );
    }
}