<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }

        // User nonaktif: tetap login tapi blokir akses, kasih info
        if ($user->role === 'user' && ($user->status ?? 'aktif') === 'nonaktif') {
            return redirect('/')
                ->with('error_nonaktif', 'Akun Anda dinonaktifkan oleh admin. Silahkan hubungi admin untuk detailnya.');
        }

        return $next($request);
    }
}