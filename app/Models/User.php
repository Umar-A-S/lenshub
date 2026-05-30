<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'username_changed',
        'name',
        'email',
        // Nomor WA + verifikasi (wajib untuk sewa)
        'phone',
        'phone_verified_at',
        'phone_otp',
        'phone_otp_expires_at',
        // OTP untuk reset password via WA
        'wa_reset_otp',
        'wa_reset_otp_expires_at',
        // Profil
        'photo',
        'role',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'phone_otp',
        'wa_reset_otp',
    ];

    protected $casts = [
        'phone_verified_at'       => 'datetime',
        'phone_otp_expires_at'    => 'datetime',
        'wa_reset_otp_expires_at' => 'datetime',
        'username_changed'        => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────
    // STATUS AKUN
    // ─────────────────────────────────────────────────────────────

    public function isAktif(): bool
    {
        return ($this->status ?? 'aktif') === 'aktif';
    }

    // ─────────────────────────────────────────────────────────────
    // NOMOR WA
    // ─────────────────────────────────────────────────────────────

    /** Nomor WA sudah diverifikasi via OTP */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    // ─────────────────────────────────────────────────────────────
    // USERNAME
    // ─────────────────────────────────────────────────────────────

    /** Username masih bisa diganti (belum pernah diganti sebelumnya) */
    public function canChangeUsername(): bool
    {
        return ! $this->username_changed;
    }

    // ─────────────────────────────────────────────────────────────
    // KELENGKAPAN PROFIL (syarat bisa pesan)
    // ─────────────────────────────────────────────────────────────

    /**
     * Cek apakah profil sudah lengkap untuk bisa melakukan pemesanan.
     *
     * Syarat minimum:
     *  - Username (untuk identifikasi)
     *  - Nomor WhatsApp + sudah diverifikasi (untuk komunikasi pesanan)
     *
     * Gmail/email hanya digunakan sebagai opsi login dan penerima rekap sewa otomatis.
     *
     * @return string[] daftar kekurangan, kosong = sudah lengkap
     */
    public function profilKurang(): array
    {
        $kurang = [];

        if (empty($this->username)) {
            $kurang[] = 'Username belum diisi di profil.';
        }

        if (empty($this->phone)) {
            $kurang[] = 'Nomor WhatsApp belum diisi di profil.';
        } elseif (! $this->hasVerifiedPhone()) {
            $kurang[] = 'Nomor WhatsApp belum diverifikasi (cek OTP di WA).';
        }

        return $kurang;
    }

    public function profilLengkap(): bool
    {
        return empty($this->profilKurang());
    }
}
