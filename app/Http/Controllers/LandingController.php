<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Rental;
use App\Models\RentalItem;

class LandingController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | LIVE DASHBOARD
        |--------------------------------------------------------------------------
        */

        $sewaAktif = Rental::where('status', 'aktif')->count();

        // Total barang disewa = akumulasi qty dari transaksi yang SUDAH SELESAI
        $barangDisewa = RentalItem::whereHas('rental', function ($query) {
            $query->where('status', 'selesai');
        })->sum('qty');

        /*
        |--------------------------------------------------------------------------
        | UTILISASI STOK
        |--------------------------------------------------------------------------
        */

        $totalStok = Equipment::sum('stok');

        $barangDipakai = RentalItem::whereHas('rental', function ($query) {
            $query->where('status', 'aktif');
        })->sum('qty');

        $stokTersedia = max($totalStok - $barangDipakai, 0);

        $persenUtilisasi = $totalStok > 0
            ? round(($stokTersedia / $totalStok) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | TOP 10
        |--------------------------------------------------------------------------
        */

        $topAlat = Equipment::withCount([
            'rentalItems as total_disewa' => function ($query) {
                $query->whereHas('rental', function ($rental) {
                    $rental->whereIn('status', ['aktif', 'terlambat', 'selesai']);
                });
            }
        ])
        ->orderByDesc('total_disewa')
        ->take(10)
        ->get();

        return view('landing', compact(
            'sewaAktif',
            'barangDisewa',
            'stokTersedia',
            'totalStok',
            'persenUtilisasi',
            'topAlat'
        ));
    }
}