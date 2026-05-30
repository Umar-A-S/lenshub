<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\RentalRekapMail;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Mail;

class FineController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA DENDA
        |--------------------------------------------------------------------------
        */

        $fines = Fine::with([
            'rental.client',
            'rental.items.equipment'
        ])
        ->latest()
        ->get();


        /*
        |--------------------------------------------------------------------------
        | DENDA AKTIF
        |--------------------------------------------------------------------------
        */

        $aktif = $fines
            ->where('status', 'belum_lunas')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | RIWAYAT DENDA
        |--------------------------------------------------------------------------
        */

        $riwayat = $fines
            ->where('status', 'lunas')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | CARD
        |--------------------------------------------------------------------------
        */

        $aktifTerlambat =
            $aktif->count();


        $dendaBerjalan =
            $aktif->sum(
                'total_denda'
            );


        $totalDenda =
            $fines
                ->filter(function ($fine) {

                    return
                        $fine->created_at
                            ->month
                        ===
                        now()->month;

                })
                ->sum(
                    'total_denda'
                );


        $dendaLunas =
            $riwayat->count();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.denda',
            compact(
                'aktif',
                'riwayat',
                'aktifTerlambat',
                'dendaBerjalan',
                'totalDenda',
                'dendaLunas'
            )
        );
    }



    public function lunas(Fine $fine, Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'metode_bayar_denda' => ['nullable', 'in:tunai,transfer,qris'],
        ]);

        $fine->loadMissing('rental');

        // Only process if fine is currently belum_lunas to prevent duplicate processing
        $updated = Fine::where('id', $fine->id)
            ->where('status', 'belum_lunas')
            ->update([
                'status'             => 'lunas',
                'metode_bayar_denda' => $validated['metode_bayar_denda'] ?? $fine->metode_bayar_denda,
                'dibayar_pada'       => now(),
            ]);

        if ($updated && $fine->rental) {
            $rental = $fine->rental;
            $rental->update([
                'status'          => 'selesai',
                'status_denda'    => 'lunas',
                'status_bayar'    => 'lunas',
                'denda'           => $fine->total_denda,
                'dikembalikan_at' => $rental->dikembalikan_at ?? now(),
            ]);

            // Kirim rekap pesanan selesai via WA
            $rental->load(['items.equipment', 'fine']);
            WhatsAppService::kirimPesananSelesai($rental);

            // Kirim rekap selesai ke email user
            $user = $rental->user;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new RentalRekapMail($rental));
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'processed' => (bool) $updated]);
        }

        if ($updated) {
            return redirect()->route('denda.index')->with('success', 'Denda berhasil dilunasi.');
        }

        return redirect()->route('denda.index');
    }
}