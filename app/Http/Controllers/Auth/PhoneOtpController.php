<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PhoneOtpController extends Controller
{
    /**
     * Kirim OTP ke nomor WA user yang sedang login.
     * Rate-limited: maks 3 request per menit per user.
     */
    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        if (empty($user->phone)) {
            return response()->json(['message' => 'Isi nomor WhatsApp di profil terlebih dahulu.'], 422);
        }

        if ($user->hasVerifiedPhone()) {
            return response()->json(['message' => 'Nomor WhatsApp sudah terverifikasi.'], 200);
        }

        $throttleKey = 'phone-otp:' . $user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        $kode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'phone_otp'            => $kode,
            'phone_otp_expires_at' => now()->addMinutes(5),
        ]);

        $terkirim = WhatsAppService::kirimOTP($user->phone, $kode);

        if (! $terkirim) {
            return response()->json(['message' => 'Gagal mengirim OTP via WhatsApp. Coba lagi.'], 500);
        }

        return response()->json(['message' => 'OTP telah dikirim ke WhatsApp Anda.']);
    }

    /**
     * Verifikasi OTP WA yang diinput user.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedPhone()) {
            return response()->json(['message' => 'Nomor WhatsApp sudah terverifikasi.'], 200);
        }

        if (empty($user->phone_otp)) {
            return response()->json(['message' => 'Belum ada OTP yang dikirim. Kirim OTP terlebih dahulu.'], 422);
        }

        if ($user->phone_otp_expires_at && $user->phone_otp_expires_at->isPast()) {
            return response()->json(['message' => 'OTP sudah kadaluarsa. Kirim OTP baru.'], 422);
        }

        if ($request->otp !== $user->phone_otp) {
            return response()->json(['message' => 'Kode OTP salah.'], 422);
        }

        // Verifikasi berhasil
        $user->update([
            'phone_verified_at'    => now(),
            'phone_otp'            => null,
            'phone_otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'Nomor WhatsApp berhasil diverifikasi!']);
    }
}
