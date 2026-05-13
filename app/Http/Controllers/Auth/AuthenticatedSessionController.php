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
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // cek kredensial dan login
        $request->session()->regenerate();  // session baru 

        $role = $request->user()->role; // cek role pengguna setelah login

        // list tujuan redirect berdasarkan role
        return match ($role) {
            'owner' => redirect()->intended('/owner/dashboard'),
            'admin' => redirect()->intended('/admin/dashboard'),
            'user'  => redirect()->intended('/sewa'), 
            default => redirect('/'),
        };

        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
