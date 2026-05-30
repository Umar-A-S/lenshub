<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class WaPasswordResetController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // STEP 1 – Form input nomor WA / username / email
    // ─────────────────────────────────────────────────────────────

    public function showRequestForm(): View
    {
        return view('auth.wa-forgot-password');
    }

    /**
     * Cari user, kirim OTP ke nomor WA, simpan di session.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ], [
            'identifier.required' => 'Masukkan email, username, atau nomor WA.',
        ]);

        $identifier = trim($request->input('identifier'));

        // Throttle: maks 3 kali per 5 menit per IP
        $throttleKey = 'wa-reset:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'identifier' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($throttleKey, 300);

        // Cari user
        $user = $this->findUser($identifier);

        // Selalu tampilkan pesan sukses agar tidak bocor informasi akun
        if (! $user || empty($user->phone)) {
            return back()->with('wa_otp_sent', true)
                ->with('wa_masked', 'Jika data ditemukan, OTP dikirim via WA.');
        }

        // Cek akun aktif
        if (($user->status ?? 'aktif') === 'nonaktif') {
            return back()->withErrors([
                'identifier' => 'Akun ini dinonaktifkan. Hubungi admin.',
            ]);
        }

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'wa_reset_otp'            => $otp,
            'wa_reset_otp_expires_at' => now()->addMinutes(5),
        ]);

        // Kirim OTP via WA
        WhatsAppService::kirimOtpReset($user->phone, $otp, $user->name);

        // Simpan user_id di session (aman karena session di DB)
        session(['wa_reset_user_id' => $user->id]);

        // Mask nomor WA untuk ditampilkan ke user
        $masked = $this->maskPhone($user->phone);

        return redirect()->route('password.wa.verify.form')
            ->with('wa_masked', $masked);
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 2 – Form verifikasi OTP + input password baru
    // ─────────────────────────────────────────────────────────────

    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        if (! session('wa_reset_user_id')) {
            return redirect()->route('password.wa.request')
                ->withErrors(['session' => 'Sesi reset habis. Mulai ulang.']);
        }

        return view('auth.wa-reset-password', [
            'masked' => session('wa_masked'),
        ]);
    }

    /**
     * Verifikasi OTP dan reset password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'otp'                   => ['required', 'string', 'digits:6'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'otp.required'         => 'Kode OTP wajib diisi.',
            'otp.digits'           => 'Kode OTP harus 6 digit.',
            'password.required'    => 'Password baru wajib diisi.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        $userId = session('wa_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.wa.request')
                ->withErrors(['session' => 'Sesi reset habis. Mulai ulang.']);
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('password.wa.request')
                ->withErrors(['session' => 'User tidak ditemukan.']);
        }

        // Cek OTP kosong / expired / salah
        if (empty($user->wa_reset_otp)) {
            return back()->withErrors(['otp' => 'OTP belum dikirim. Mulai ulang proses.']);
        }

        if ($user->wa_reset_otp_expires_at && $user->wa_reset_otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'OTP sudah kadaluarsa. Kirim OTP baru.']);
        }

        if ($request->otp !== $user->wa_reset_otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.'])->withInput();
        }

        // Reset password dan hapus OTP
        $user->forceFill([
            'password'                => Hash::make($request->password),
            'wa_reset_otp'            => null,
            'wa_reset_otp_expires_at' => null,
        ])->save();

        // Hapus session reset
        session()->forget(['wa_reset_user_id', 'wa_masked']);

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login.');
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function findUser(string $identifier): ?User
    {
        // Coba email
        $user = User::where('email', $identifier)->first();
        if ($user) return $user;

        // Coba username
        $user = User::where('username', $identifier)->first();
        if ($user) return $user;

        // Coba nomor WA (normalisasi)
        $normalized = preg_replace('/[^0-9]/', '', $identifier);
        if (str_starts_with($normalized, '62')) {
            $normalized = '0' . substr($normalized, 2);
        }
        return User::where('phone', $normalized)
            ->orWhere('phone', $identifier)
            ->first();
    }

    private function maskPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($clean) <= 4) return str_repeat('*', strlen($clean));
        return substr($clean, 0, 3) . str_repeat('*', strlen($clean) - 5) . substr($clean, -2);
    }
}
