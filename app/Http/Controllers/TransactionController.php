<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with(['items.equipment', 'fine'])
            ->where('status', 'selesai');

        if ($request->filled('search')) {
            $kw = strtolower(trim($request->search));
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(kode_sewa) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(nama_penyewa) LIKE ?', ["%{$kw}%"]);
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('dikembalikan_at', $request->tanggal);
        }

        $transactions = $query->latest()->get()->map(function ($trx) {
            $trx->alat_nama   = $trx->items->pluck('equipment.nama')->filter()->join(', ');
            $trx->total_denda = $trx->fine?->total_denda ?? $trx->denda ?? 0;
            $trx->label       = $trx->total_denda > 0
                ? ($trx->fine?->status === 'lunas' ? 'Selesai Berdenda (Lunas)' : 'Selesai')
                : 'Selesai Normal';
            return $trx;
        });

        $totalPendapatan = $transactions->sum('total');
        $totalDenda      = $transactions->sum('total_denda');
        $aktif           = Rental::whereIn('status', ['aktif', 'menunggu_pelunasan'])->count();

        return view('admin.transaksi', compact('transactions', 'totalPendapatan', 'totalDenda', 'aktif'));
    }
}
