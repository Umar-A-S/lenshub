<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Rental extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'kode_sewa',
        'nama_penyewa',
        'whatsapp',
        'mulai',
        'jatuh_tempo',
        'durasi',
        'logistik',
        'alamat_pengiriman',
        'total',
        'denda',
        'status',
        'status_denda',
        'jaminan_fisik',
        'metode_bayar',
        'status_bayar',
        'catatan_kondisi',
        'dikembalikan_at',
    ];

    protected $casts = [
        'mulai'           => 'datetime',
        'jatuh_tempo'     => 'datetime',
        'dikembalikan_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(RentalItem::class);
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    // Hitung harga berdasarkan durasi
    public static function hitungHarga(float $hargaHarian, string $durasi): float
    {
        return match ($durasi) {
            '12jam' => $hargaHarian * 0.80,
            '1hari' => $hargaHarian,
            '3hari' => $hargaHarian * 3 * 0.90,
            '5hari' => $hargaHarian * 5 * 0.85,
            '7hari' => $hargaHarian * 7 * 0.83,
            default => $hargaHarian,
        };
    }

    // Hitung jatuh tempo
    public static function hitungJatuhTempo(Carbon $mulai, string $durasi): Carbon
    {
        return match ($durasi) {
            '12jam' => $mulai->copy()->addHours(12),
            '1hari' => $mulai->copy()->addDay(),
            '3hari' => $mulai->copy()->addDays(3),
            '5hari' => $mulai->copy()->addDays(5),
            '7hari' => $mulai->copy()->addDays(7),
            default => $mulai->copy()->addDay(),
        };
    }
}
