<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => $request->redirect]);
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Tentukan tujuan redirect berdasarkan role dulu
        $roleDestination = match ($user->role) {
            'owner', 'admin' => '/dashboard',
            default          => '/akun/profil',
        };

        // Pakai intended URL hanya jika valid untuk role user
        // (intended hanya dari ?redirect= saat klik Sewa — pasti URL publik/user)
        $intended = session()->pull('url.intended');

        if ($intended && $user->role === 'user') {
            // Pastikan intended bukan halaman admin
            $isAdminUrl = str_starts_with(ltrim($intended, '/'), 'dashboard')
                       || str_starts_with(ltrim($intended, '/'), 'sewa')
                       || str_starts_with(ltrim($intended, '/'), 'permintaan')
                       || str_starts_with(ltrim($intended, '/'), 'owner');

            if (!$isAdminUrl) {
                // Force save session ke DB sebelum redirect
                $request->session()->save();
                return redirect($intended);
            }
        }

        // Force save session ke DB sebelum redirect
        // Mencegah race condition pada database session driver
        $request->session()->save();

        return redirect($roleDestination);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}