<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Fine;
use App\Models\Rental;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::now(config('app.timezone'));
        $yesterday = $today->copy()->subDay();

        // ─── Pendapatan Hari Ini
        $pendapatanHariIni = Rental::where('status', 'selesai')
            ->whereDate('dikembalikan_at', $today->toDateString())
            ->sum('total');

        $pendapatanKemarin = Rental::where('status', 'selesai')
            ->whereDate('dikembalikan_at', $yesterday->toDateString())
            ->sum('total');

        $persenVsKemarin = $pendapatanKemarin > 0
            ? round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100, 1)
            : ($pendapatanHariIni > 0 ? 100 : 0);

        // ─── Sewa Aktif
        $sewaAktif = Rental::where('status', 'aktif')->count();
        $hampirJatuhTempo = Rental::where('status', 'aktif')
            ->whereBetween('jatuh_tempo', [$today, $today->copy()->addHours(24)])
            ->count();

        // ─── Denda Terkumpul (bulan ini)
        $dendaTerkumpul = Fine::where('status', 'lunas')
            ->whereMonth('dibayar_pada', $today->month)
            ->whereYear('dibayar_pada', $today->year)
            ->sum('total_denda');

        $terlambatCount = Rental::where('status', 'aktif')
            ->where('jatuh_tempo', '<', $today)
            ->count();

        // ─── Stok
        $totalStok    = Equipment::sum('stok');
        $sedangDisewa = Rental::where('status', 'aktif')
            ->withCount('items')->get()->sum('items_count');
        $stokTersedia = max(0, $totalStok - $sedangDisewa);

        // ─── Pendapatan 7 Hari
        $pendapatan7Hari = [];
        $labels7Hari     = [];
        $maxP = 1;
        for ($i = 6; $i >= 0; $i--) {
            $tgl = $today->copy()->subDays($i);
            $p   = Rental::where('status', 'selesai')
                ->whereDate('dikembalikan_at', $tgl->toDateString())
                ->sum('total');
            $pendapatan7Hari[] = $p;
            $labels7Hari[]     = $tgl->locale('id')->isoFormat('ddd');
            if ($p > $maxP) $maxP = $p;
        }
        $barHeights = array_map(fn($p) => max(8, round(($p / $maxP) * 100)), $pendapatan7Hari);

        // ─── Distribusi Kategori
        $distribusiKategori = Category::with(['equipments.rentalItems' => function ($q) {
            $q->whereHas('rental', fn($r) => $r->where('status', 'aktif'));
        }])->get()->map(function ($cat) {
            return ['nama' => $cat->nama, 'disewa' => $cat->equipments->flatMap->rentalItems->count()];
        })->filter(fn($c) => $c['disewa'] > 0)->sortByDesc('disewa')->values();

        $totalDisewa = $distribusiKategori->sum('disewa') ?: 1;
        $distribusiKategori = $distribusiKategori->map(function ($c) use ($totalDisewa) {
            $c['persen'] = round(($c['disewa'] / $totalDisewa) * 100);
            return $c;
        });

        // ─── Aktivitas Terkini
        $aktivitasTerkini = Rental::with('items.equipment')
            ->whereNotIn('status', ['pending', 'ditolak'])
            ->latest()->take(8)->get()
            ->map(function ($r) {
                $r->alat_nama = $r->items->pluck('equipment.nama')->filter()->join(', ');
                return $r;
            });

        return view('admin.dashboard', compact(
            'pendapatanHariIni', 'persenVsKemarin',
            'sewaAktif', 'hampirJatuhTempo',
            'dendaTerkumpul', 'terlambatCount',
            'stokTersedia', 'totalStok', 'sedangDisewa',
            'barHeights', 'labels7Hari', 'pendapatan7Hari',
            'distribusiKategori', 'aktivitasTerkini'
        ));
    }
}
