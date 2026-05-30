<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Rental;
use App\Mail\RentalRekapMail;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SewaController extends Controller
{
    // ---------------------------------------------------------------
    // TAB 2: Manajemen Sewa (aktif + menunggu_pelunasan)
    // ---------------------------------------------------------------

    public function index(Request $request)
    {
        $today = Carbon::now(config('app.timezone'));

        $sewaAktif = Rental::whereIn('status', ['aktif'])->count();

        $hampirJatuhTempo = Rental::where('status', 'aktif')
            ->whereBetween('jatuh_tempo', [$today, $today->copy()->addHours(24)])
            ->count();

        $terlambat = Rental::where('status', 'aktif')
            ->where('jatuh_tempo', '<', $today)
            ->count();

        $selesaiBulanIni = Rental::where('status', 'selesai')
            ->whereMonth('updated_at', $today->month)
            ->whereYear('updated_at', $today->year)
            ->count();

        $query = Rental::with(['items.equipment', 'fine'])
            ->whereIn('status', ['aktif', 'menunggu_pelunasan']);

        if ($request->filled('search')) {
            $kw = strtolower(trim($request->search));
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(kode_sewa) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(nama_penyewa) LIKE ?', ["%{$kw}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('mulai', $request->tanggal);
        }

        $rentals = $query->latest()->get()->map(function ($rental) use ($today) {
            $rental->alat_nama = $rental->items->pluck('equipment.nama')->filter()->join(', ');

            $jatuhTempo = $rental->jatuh_tempo instanceof Carbon
                ? $rental->jatuh_tempo->copy()
                : Carbon::parse($rental->jatuh_tempo, config('app.timezone'));
            $diff       = $today->diffInSeconds($jatuhTempo, false);

            $rental->sisa_detik  = $diff;
            $rental->sudah_lewat = $diff < 0;

            if ($diff <= 0) {
                $rental->sisa_waktu = 'Terlambat';
            } else {
                $hari = intdiv($diff, 86400);
                $jam  = intdiv($diff % 86400, 3600);
                $mnt  = intdiv($diff % 3600, 60);
                $rental->sisa_waktu = trim(
                    ($hari > 0 ? $hari . 'H ' : '') .
                    ($jam  > 0 ? $jam  . 'J ' : '') .
                    $mnt . 'M'
                );
            }

            $rental->total_denda = $rental->fine?->total_denda ?? 0;

            return $rental;
        });

        $sewaCards = $rentals->where('status', 'aktif')->take(6)->values();

        return view('admin.sewa', compact(
            'rentals', 'sewaCards',
            'sewaAktif', 'hampirJatuhTempo', 'terlambat', 'selesaiBulanIni'
        ));
    }

    // ---------------------------------------------------------------
    // Pop-up Proses Pengembalian → POST
    // ---------------------------------------------------------------

    public function pengembalian(Request $request, Rental $rental)
    {
        $request->validate([
            'ada_denda'           => ['nullable'],
            'terlambat'           => ['nullable', 'boolean'],
            'telat_jam'           => ['nullable', 'integer', 'min:0'],
            'tarif_per_jam'       => ['nullable', 'numeric', 'min:0'],
            'rusak'               => ['nullable', 'boolean'],
            'deskripsi_kerusakan' => ['nullable', 'string'],
            'biaya_kerusakan'     => ['nullable', 'numeric', 'min:0'],
            'metode_bayar_denda'  => ['nullable', 'in:tunai,transfer,qris'],
            'status_denda'        => ['required', 'in:lunas,belum_bayar'],
        ]);

        $adaDenda    = $request->boolean('terlambat') || $request->boolean('rusak');
        $totalDenda  = 0;
        $telat       = (int) $request->input('telat_jam', 0);
        $tarifPerJam = (float) $request->input('tarif_per_jam', 0);
        $biayaRusak  = (float) $request->input('biaya_kerusakan', 0);

        if ($request->boolean('terlambat')) {
            $totalDenda += $telat * $tarifPerJam;
        }
        if ($request->boolean('rusak')) {
            $totalDenda += $biayaRusak;
        }

        $statusDenda = $request->input('status_denda');

        // Buat/update fine jika ada denda
        if ($adaDenda && $totalDenda > 0) {
            Fine::updateOrCreate(
                ['rental_id' => $rental->id],
                [
                    'terlambat'           => $request->boolean('terlambat'),
                    'telat_jam'           => $telat,
                    'tarif_per_jam'       => $tarifPerJam,
                    'deskripsi_kerusakan' => $request->input('deskripsi_kerusakan'),
                    'biaya_kerusakan'     => $biayaRusak,
                    'total_denda'         => $totalDenda,
                    'metode_bayar_denda'  => $request->input('metode_bayar_denda'),
                    'status'              => $statusDenda === 'lunas' ? 'lunas' : 'belum_lunas',
                    'dibayar_pada'        => $statusDenda === 'lunas' ? now() : null,
                ]
            );
        }

        if (! $adaDenda || $statusDenda === 'lunas') {
            // Selesai normal atau denda lunas di tempat
            $payload = [
                'status'          => 'selesai',
                'status_denda'    => $totalDenda > 0 ? 'lunas' : 'tidak_ada',
                'denda'           => $totalDenda,
                'dikembalikan_at' => now(),
            ];

            $updated = Rental::where('id', $rental->id)
                ->whereIn('status', ['aktif', 'menunggu_pelunasan'])
                ->update($payload);

            if ($updated) {
                // Kirim rekap pesanan selesai via WA
                $rental = Rental::with(['items.equipment', 'fine', 'user'])->find($rental->id);
                WhatsAppService::kirimPesananSelesai($rental);

                // Kirim rekap selesai ke email user
                $user = $rental->user;
                if ($user && $user->email) {
                    Mail::to($user->email)->send(new RentalRekapMail($rental));
                }

                return response()->json(['success' => true, 'processed' => true, 'status' => $rental->status]);
            }

            // Nothing updated -> already processed
            return response()->json(['success' => true, 'processed' => false, 'status' => $rental->fresh()->status]);

        } else {
            // Denda belum dibayar → transaksi tertahan
            $payload = [
                'status'          => 'menunggu_pelunasan',
                'status_denda'    => 'belum_lunas',
                'denda'           => $totalDenda,
                'dikembalikan_at' => now(),
            ];

            $updated = Rental::where('id', $rental->id)
                ->whereIn('status', ['aktif', 'menunggu_pelunasan'])
                ->update($payload);

            if ($updated) {
                return response()->json(['success' => true, 'processed' => true, 'status' => Rental::find($rental->id)->status]);
            }

            return response()->json(['success' => true, 'processed' => false, 'status' => $rental->fresh()->status]);
        }
    }

    // ---------------------------------------------------------------
    // Pelunasan denda kemudian hari
    // ---------------------------------------------------------------

    public function lunasDenda(Request $request, Rental $rental)
    {
        $request->validate([
            'metode_bayar_denda' => ['required', 'in:tunai,transfer,qris'],
        ]);

        $updatedAny = 0;

        // Try to update fine only if it's still belum_lunas
        if ($rental->fine) {
            $updatedAny += Fine::where('id', $rental->fine->id)
                ->where('status', 'belum_lunas')
                ->update([
                    'status'             => 'lunas',
                    'metode_bayar_denda' => $request->metode_bayar_denda,
                    'dibayar_pada'       => now(),
                ]);
        }

        // Update rental to selesai only if not already selesai
        $updatedAny += Rental::where('id', $rental->id)
            ->where('status', '!=', 'selesai')
            ->update([
                'status'          => 'selesai',
                'status_denda'    => 'lunas',
                'status_bayar'    => 'lunas',
                'dikembalikan_at' => $rental->dikembalikan_at ?? now(),
            ]);

        if ($updatedAny > 0) {
            // Kirim rekap pesanan selesai via WA (denda sudah lunas)
            $rental = Rental::with(['items.equipment', 'fine', 'user'])->find($rental->id);
            WhatsAppService::kirimPesananSelesai($rental);

            // Kirim rekap selesai ke email user
            $user = $rental->user;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new RentalRekapMail($rental));
            }

            return response()->json(['success' => true, 'processed' => true]);
        }

        return response()->json(['success' => true, 'processed' => false]);
    }

    // ---------------------------------------------------------------
    // Tombol pintas WhatsApp manual
    // ---------------------------------------------------------------

    public function waReminder(Rental $rental)
    {
        $alat   = $rental->items->pluck('equipment.nama')->join(', ');
        $waktu  = $rental->jatuh_tempo instanceof Carbon
            ? $rental->jatuh_tempo->format('d M Y H:i')
            : Carbon::parse($rental->jatuh_tempo, config('app.timezone'))->format('d M Y H:i');
        $pesan  = urlencode(
            "Halo *{$rental->nama_penyewa}*, ini pengingat dari *LensHub*.\n\n" .
            "Sewa alat *{$alat}* Anda jatuh tempo pada *{$waktu}*.\n" .
            "Mohon segera kembalikan alat tepat waktu. Terima kasih 🙏"
        );
        $nomor  = preg_replace('/[^0-9]/', '', $rental->whatsapp);
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        return redirect("https://wa.me/{$nomor}?text={$pesan}");
    }
}
