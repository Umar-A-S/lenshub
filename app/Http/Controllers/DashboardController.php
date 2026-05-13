<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // dashboard controller dibuat untuk menangani tampilan dashboard yang berbeda untuk setiap role (owner, admin, user)

    // menampilkan dashboard sesuai dengan role user yang login
    // 1. Ownwer (owner/index.blade.php)
    // 2. Admin (admin/index.blade.php)
    // 3. User biasa (redirect ke halaman lain, misal homepage atau profile)
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'owner') {
            return view('owner.dashboard');
        } elseif ($user->role === 'admin') {
            return view('admin.rentals.dashboard');
        } elseif ($user->role === 'user') {
            return redirect('/sewa'); // atau halaman lain untuk user biasa
        }else {
            return redirect('/'); // atau halaman lain untuk user biasa
        }
    }
}
