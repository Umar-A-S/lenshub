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
use Illuminate\Support\Str; // Tambahkan ini
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'user_id', 'gear_id', 'whatsapp', 'alamat', 
        'start_time', 'purpose', 'payment_method', 'start_date', 
        'end_date', 'total_price', 'status', 'note', 'foto_ktp',
        'returned_at', 'cancelled_at', 'penalty_amount', 'total_days_late', 'final_amount'
    ];

    protected $casts = [
        'duration' => 'integer',
        'total_price' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // --- AUTOMATION BOOTED ---
    protected static function booted()
    {
        // 1. Buat Booking Code Otomatis saat user baru isi form
        static::creating(function ($rental) {
            $rental->booking_code = 'RENT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        });

        // 2. Trigger Update Stok Otomatis berdasarkan status
        static::updated(function ($rental) {
            // Jika dibatalkan atau selesai, stok bertambah
            if ($rental->wasChanged('status') && $rental->status === 'active') {
                $rental->gear->update(['status' => 'rented']);
            }
            
            // Jika status berubah jadi active (barang dibawa), stok berkurang
            if ($rental->wasChanged('status') && in_array($rental->status, ['completed', 'cancelled'])) {
                $rental->gear->update(['status' => 'available']);
            }
        });
    }

    // --- RELATIONSHIPS ---
    public function gear() { return $this->belongsTo(Gear::class); }
    public function user() { return $this->belongsTo(User::class); }

    // --- ACCESSORS ---
    
    // Accessor Anda sudah bagus, saya rapikan sedikit
    protected function penaltyDetails(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status === 'completed') {
                    return ['is_late' => $this->total_days_late > 0, 'days' => $this->total_days_late, 'total' => $this->penalty_amount];
                }

                $dueDate = $this->end_date;
                $gracePeriod = $dueDate->copy()->addHours(2); 
                $now = Carbon::now();

                if ($now->gt($gracePeriod)) {
                    $hoursLate = $now->diffInHours($gracePeriod);
                    $daysLate = (int) ceil($hoursLate / 24);
                    $feePerDay = $this->gear->penalty_fee ?? 50000;

                    return [
                        'is_late' => true,
                        'days'    => $daysLate,
                        'total'   => $daysLate * $feePerDay
                    ];
                }
                return ['is_late' => false, 'days' => 0, 'total' => 0];
            }
        );
    }

    protected function grandTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_price + $this->penalty_details['total']
        );
    }
}