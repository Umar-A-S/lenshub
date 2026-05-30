<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('[Google OAuth] Callback error: ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['google' => 'Login Google gagal. Silakan coba lagi.']);
        }

        // Cari user yang sudah punya google_id ini
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // Cari berdasarkan email (mungkin sudah daftar manual dulu)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Tautkan google_id ke akun yang ada
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // Buat akun baru
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(), // Email Google sudah terverifikasi
                    'password'          => null,  // Tidak pakai password
                ]);
            }
        }

        // Cek status akun
        if (($user->status ?? 'aktif') === 'nonaktif') {
            return redirect()->route('login')
                ->withErrors(['google' => 'Akun Anda dinonaktifkan. Hubungi admin.']);
        }

        Auth::login($user, remember: true);

        // Redirect berdasarkan role
        if (in_array($user->role, ['admin', 'owner'])) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('akun.profil');
    }
}
