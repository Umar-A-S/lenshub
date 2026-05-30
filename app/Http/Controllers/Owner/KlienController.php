<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KlienController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $kw = strtolower(trim($request->search));
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$kw}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->get();

        $totalKlien    = User::where('role', 'user')->count();
        $aktifBulanIni = User::where('role', 'user')
            ->where('status', 'aktif')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return view('owner.klien', compact('users', 'totalKlien', 'aktifBulanIni'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->role !== 'user') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => $newStatus === 'aktif'
                ? 'Pengguna berhasil diaktifkan kembali.'
                : 'Pengguna berhasil dinonaktifkan.',
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'user') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }
}
