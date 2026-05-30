<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profil user.
     *
     * Aturan khusus:
     * - Username hanya bisa diganti 1x. Setelah itu dikunci.
     * - Jika nomor WA berubah, verifikasi WA direset (harus verif ulang).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        // ── USERNAME: kunci setelah 1x ganti ──────────────────────────
        if (isset($validated['username']) && $validated['username'] !== $user->username) {
            if ($user->username_changed) {
                return Redirect::back()
                    ->withErrors(['username' => 'Username hanya bisa diubah 1 kali.'])
                    ->withInput();
            }
            $validated['username_changed'] = true;
        }

        // ── PHONE: jika nomor berubah, reset verifikasi WA ───────────
        if (isset($validated['phone']) && $validated['phone'] !== $user->phone) {
            $validated['phone_verified_at']    = null;
            $validated['phone_otp']            = null;
            $validated['phone_otp_expires_at'] = null;
        }

        $user->fill($validated)->save();

        return Redirect::back()->with('status', 'profile-updated');
    }

    /**
     * Hapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
