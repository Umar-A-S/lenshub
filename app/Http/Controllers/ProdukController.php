<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'rentalItems.rental'])->latest();

        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->search));

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('LOWER(deskripsi) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereHas('category', function ($cat) use ($keyword) {
                        $cat->whereRaw('LOWER(nama) LIKE ?', ['%' . $keyword . '%']);
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $equipments = $query->get()->map(function ($item) {
            // ─── FIX: disewa_count sekarang hanya hitung yang sedang aktif
            //         (untuk menentukan stok tersedia saat ini),
            //         sedangkan total_disewa_count adalah total kumulatif semua
            //         yang pernah disewa (termasuk selesai).
            $activeCount = $item->rentalItems
                ->filter(fn($ri) => in_array($ri->rental?->status, ['aktif', 'terlambat', 'menunggu_pelunasan']))
                ->sum('qty');

            // Kumulatif: semua sewa yang pernah terjadi (aktif + selesai)
            $totalCount = $item->rentalItems
                ->filter(fn($ri) => in_array($ri->rental?->status, [
                    'aktif', 'terlambat', 'menunggu_pelunasan', 'selesai'
                ]))
                ->sum('qty');

            $stokTersedia = max($item->stok - $activeCount, 0);

            $item->disewa_count  = $totalCount;      // tampil di kartu: "Disewa Nx"
            $item->active_count  = $activeCount;     // untuk logika stok
            $item->stok_tersedia = $stokTersedia;
            $item->status_stok   = $stokTersedia > 0 ? 'tersedia' : 'tidak tersedia';

            return $item;
        });

        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $equipments = $equipments->where('status_stok', 'tersedia')->values();
            }

            if ($request->status === 'tidak tersedia') {
                $equipments = $equipments->where('status_stok', 'tidak tersedia')->values();
            }
        }

        $categories = Category::orderBy('nama', 'asc')->get();

        return view('produk.index', compact('equipments', 'categories'));
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['category', 'rentalItems.rental']);

        // Hanya yang sedang aktif untuk hitung stok tersedia
        $activeCount = $equipment->rentalItems
            ->filter(fn($ri) => in_array($ri->rental?->status, ['aktif', 'terlambat', 'menunggu_pelunasan']))
            ->sum('qty');

        // Kumulatif untuk tampilan "Disewa Nx"
        $disewaCount = $equipment->rentalItems
            ->filter(fn($ri) => in_array($ri->rental?->status, [
                'aktif', 'terlambat', 'menunggu_pelunasan', 'selesai'
            ]))
            ->sum('qty');

        $stokTersedia = max($equipment->stok - $activeCount, 0);

        return view('produk.show', compact('equipment', 'disewaCount', 'stokTersedia'));
    }
}
