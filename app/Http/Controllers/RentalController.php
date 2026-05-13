<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Gear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * Dashboard Admin: Ringkasan Aktivitas (Fitur 1) & Monitoring (Fitur 2)
     */
    public function index()
    {
        // 1. Ringkasan Aktivitas Harian
        $stats = [
            'ambil_hari_ini'   => Rental::where('status', 'booking')->whereDate('start_date', today())->count(),
            'kembali_hari_ini' => Rental::where('status', 'active')->whereDate('end_date', today())->count(),
            'terlambat'        => Rental::where('status', 'active')->whereDate('end_date', '<', today())->count(),
            'total_booking'    => Rental::where('status', 'booking')->count(),
        ];

        // 2. Data Tabel Transaksi
        $permohonan = Rental::with(['gear.category', 'user'])
            ->where('status', 'booking')
            ->orderBy('start_date', 'asc')
            ->get();

        $monitoring = Rental::with(['gear', 'user'])
            ->where('status', 'active')
            ->orderBy('end_date', 'asc')
            ->get();

        return view('admin.rentals.dashboard', compact('stats', 'permohonan', 'monitoring'));
    }

    /**
     * Tampilkan Form Sewa (Sisi Customer)
     */
    public function create()
    {
        // Ambil gear dengan relasi rental untuk cek jadwal di frontend
        // Di RentalController@create
        $gears = Gear::where('status', 'available')
                    ->where('condition_status', 'baik')
                    ->get();

        return view('rentals.create', compact('gears'));
    }

    /**
     * Proses Simpan Sewa (Sisi Customer)
     */
    public function store(Request $request)
    {
        $request->validate([
            'gear_id'        => 'required|exists:gears,id',
            'start_date'     => 'required|date|after_or_equal:today',
            'start_time'     => 'required|date_format:H:i',
            'duration'       => 'required|integer|min:1',
            'foto_ktp'       => 'required|image|max:2048',
            'whatsapp'       => 'required',
            'alamat'         => 'required',
            'purpose'        => 'required',
            'payment_method' => 'required',
        ]);

        // Pencegahan Overlap (Sisi Server)
        $startReq = Carbon::parse($request->start_date);
        $endReq   = $startReq->copy()->addDays((int) $request->duration);

        $isConflict = Rental::where('gear_id', $request->gear_id)
            ->whereIn('status', ['booking', 'active'])
            ->where(function ($query) use ($startReq, $endReq) {
                $query->where('start_date', '<', $endReq)
                      ->where('end_date', '>', $startReq);
            })->exists();

        if ($isConflict) {
            return back()->with('error', 'Maaf, jadwal alat sudah terisi pada tanggal tersebut.');
        }

        // Simpan foto KTP ke storage local (bukan public) untuk keamanan
        if ($request->hasFile('foto_ktp')) {
            // gunakan ktp karena root local di app/private, jadi akan tersimpan di storage/app/private/ktp
            $fotoPath = $request->file('foto_ktp')->store('ktp', 'local');
            $validatedData['foto_ktp'] = $fotoPath;
        }
        
        $gear = Gear::findOrFail($request->gear_id);

        $rental = Rental::create([
            'user_id'        => auth()->id(),
            'gear_id'        => $request->gear_id,
            'whatsapp'       => $request->whatsapp,
            'alamat'         => $request->alamat,
            'start_time'     => $request->start_time,
            'purpose'        => $request->purpose,
            'payment_method' => $request->payment_method,
            'start_date'     => $request->start_date,
            'end_date'       => $endReq,
            'duration'       => (int) $request->duration,
            'total_price'    => (int) $gear->rent_price * (int) $request->duration,
            'status'         => 'booking',
            'foto_ktp'       => $fotoPath,
            'note'           => 'Pendaftaran Online',
        ]);

        return redirect()->route('rentals.success', $rental->id);
    }

    public function showSuccess($id)
    {
        $rental = Rental::with('gear')->findOrFail($id);
        if ($rental->user_id !== auth()->id()) abort(403);

        return view('rentals.success', compact('rental'));
    }

    /**
     * Aksi Admin: Verifikasi & Aktifkan Rental
     */
    public function konfirmasiPembayaran($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'booking') {
            return back()->with('error', 'Status tidak valid.');
        }

        $rental->update([
            'status' => 'active',
            'note'   => $rental->note . ' | Aktif oleh Admin: ' . auth()->user()->name . ' pada ' . now()
        ]);

        return back()->with('success', 'Rental berhasil diaktifkan!');
    }

    /**
     * Aksi Admin: Selesaikan Rental & Hitung Denda
     */
    public function selesaiRental($id)
    {
        $rental = Rental::findOrFail($id);
        
        // Logika denda dari Accessor Model (pastikan model punya penalty_details)
        $penalty = $rental->penalty_details; 

        $rental->update([
            'status'          => 'completed',
            'returned_at'     => now(),
            'penalty_amount'  => $penalty['total'],
            'total_days_late' => $penalty['days'],
            'final_amount'    => $rental->total_price + $penalty['total'],
        ]);

        return back()->with('success', 'Rental selesai. Stok alat telah kembali.');
    }

    public function clearExpiredBookings()
    {
        Rental::where('status', 'booking')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'cancelled', 'note' => 'Expired otomatis']);

        return back()->with('success', 'Booking kadaluarsa dibersihkan.');
    }

    public function viewKtp($id)
    {
        $rental = Rental::findOrFail($id);
        
        // Cek apakah file ada di storage/app/private/ktp
        if (!\Storage::disk('local')->exists($rental->foto_ktp)) {
            abort(404, 'File KTP tidak ditemukan di :' . $rental->foto_ktp);
        }

        $path = \Storage::disk('local')->path($rental->foto_ktp);

        // Ambil file dan tampilkan sebagai gambar
        return response()->file($path);
    }
}