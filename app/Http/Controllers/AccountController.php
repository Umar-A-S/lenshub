<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user() ?? abort(401);
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user() ?? abort(401);

        $data = $request->validate([
            // username nullable — boleh kosong saat pertama kali, tapi unique jika diisi
            'username' => ['nullable', 'string', 'min:3', 'max:30',
                           'regex:/^[a-z0-9_.]+$/',
                           'unique:users,username,' . $user->id],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'username.regex' => 'Username hanya boleh huruf kecil, angka, titik (.), dan underscore (_).',
        ]);

        // ── USERNAME: kunci setelah 1x diubah ────────────────────────
        if (!empty($data['username']) && $data['username'] !== $user->username) {
            if ($user->username_changed) {
                return back()
                    ->withErrors(['username' => 'Username hanya bisa diubah 1 kali.'])
                    ->withInput();
            }
            $data['username_changed'] = true;
        }

        // ── PHONE: reset verifikasi WA jika nomor berubah ─────────────
        if (array_key_exists('phone', $data) && $data['phone'] !== $user->phone) {
            $data['phone_verified_at']    = null;
            $data['phone_otp']            = null;
            $data['phone_otp_expires_at'] = null;
        }

        // ── FOTO ───────────────────────────────────────────────────────
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ---------------------------------------------------------------
    // Helpers (tidak berubah)
    // ---------------------------------------------------------------

    private function formatCountdown(Carbon $now, Carbon $jatuhTempo): array
    {
        $diff = $now->diffInSeconds($jatuhTempo, false);
        $abs  = abs($diff);
        $hari = intdiv($abs, 86400);
        $jam  = intdiv($abs % 86400, 3600);
        $mnt  = intdiv($abs % 3600, 60);
        $teks = trim(($hari > 0 ? $hari . 'H ' : '') . $jam . 'J ' . $mnt . 'M');

        if ($diff <= 0) {
            return ['sisa_waktu' => 'Terlambat ' . $teks, 'waktu_warna' => 'red',    'sisa_detik' => $diff];
        }
        return ['sisa_waktu' => $teks, 'waktu_warna' => $diff < 86400 ? 'yellow' : 'blue', 'sisa_detik' => $diff];
    }

    private function buildPesanan(int $userId): \Illuminate\Support\Collection
    {
        return Rental::with(['items.equipment', 'fine'])
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($rental) {
                $rental->alat_nama = $rental->items->pluck('equipment.nama')->filter()->join(', ');
                $rental->alat_foto = $rental->items->first()?->equipment?->foto;

                if (in_array($rental->status, ['aktif', 'menunggu_pelunasan'])) {
                    $now        = Carbon::now(config('app.timezone'));
                    $jatuhTempo = $rental->jatuh_tempo instanceof Carbon
                        ? $rental->jatuh_tempo->copy()
                        : Carbon::parse($rental->jatuh_tempo, config('app.timezone'));
                    $countdown  = $this->formatCountdown($now, $jatuhTempo);
                    $rental->sisa_waktu  = $countdown['sisa_waktu'];
                    $rental->waktu_warna = $countdown['waktu_warna'];
                    $rental->sisa_detik  = $countdown['sisa_detik'];
                }

                return $rental;
            });
    }

    public function orders()
    {
        /** @var \App\Models\User $user */
        $user    = auth()->user() ?? abort(401);
        $pesanan = $this->buildPesanan($user->id);
        $aktif   = $pesanan->whereIn('status', ['aktif']);
        $pending = $pesanan->where('status', 'pending');
        $proses  = $pesanan->where('status', 'menunggu_pelunasan');
        $history = $pesanan->whereIn('status', ['selesai', 'ditolak']);
        return view('user.orders', compact('pesanan', 'aktif', 'pending', 'proses', 'history'));
    }

    public function ordersPartial()
    {
        /** @var \App\Models\User $user */
        $user    = auth()->user() ?? abort(401);
        $pesanan = $this->buildPesanan($user->id);
        $aktif   = $pesanan->whereIn('status', ['aktif']);
        $pending = $pesanan->where('status', 'pending');
        $proses  = $pesanan->where('status', 'menunggu_pelunasan');
        $history = $pesanan->whereIn('status', ['selesai', 'ditolak']);

        return response()->json([
            'berlangsung_count' => $aktif->count() + $pending->count() + $proses->count(),
            'history_count'     => $history->count(),
            'html_berlangsung'  => view('user._orders_berlangsung', compact('aktif', 'pending', 'proses'))->render(),
            'html_history'      => view('user._orders_history', compact('history'))->render(),
        ]);
    }

    public function notifications()
    {
        return view('user.notifications');
    }

    public function pollStatus()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user() ?? abort(401);

        $pending  = Rental::where('user_id', $user->id)->where('status', 'pending')->count();
        $aktif    = Rental::where('user_id', $user->id)->where('status', 'aktif')->count();
        $menunggu = Rental::where('user_id', $user->id)->where('status', 'menunggu_pelunasan')->count();

        $recentChange = Rental::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->whereIn('status', ['aktif', 'menunggu_pelunasan', 'selesai', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->first(['kode_sewa', 'status', 'updated_at']);

        return response()->json([
            'pending'       => $pending,
            'aktif'         => $aktif,
            'menunggu'      => $menunggu,
            'recent_change' => $recentChange ? [
                'kode'   => $recentChange->kode_sewa,
                'status' => $recentChange->status,
                'time'   => $recentChange->updated_at->diffForHumans(),
            ] : null,
        ]);
    }

    public function pollAdmin()
    {
        $pendingCount  = Rental::where('status', 'pending')->count();
        $recentPending = Rental::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderBy('created_at', 'desc')
            ->first(['kode_sewa', 'nama_penyewa', 'created_at']);

        return response()->json([
            'pending_count'  => $pendingCount,
            'recent_request' => $recentPending ? [
                'kode' => $recentPending->kode_sewa,
                'nama' => $recentPending->nama_penyewa,
                'time' => $recentPending->created_at->diffForHumans(),
            ] : null,
        ]);
    }

    public function pollDashboard()
    {
        $today     = Carbon::now(config('app.timezone'));
        $yesterday = $today->copy()->subDay();

        $pendapatanHariIni = Rental::where('status', 'selesai')
            ->whereDate('dikembalikan_at', $today->toDateString())->sum('total');
        $pendapatanKemarin = Rental::where('status', 'selesai')
            ->whereDate('dikembalikan_at', $yesterday->toDateString())->sum('total');

        $persenVsKemarin = $pendapatanKemarin > 0
            ? round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100, 1)
            : ($pendapatanHariIni > 0 ? 100 : 0);

        $sewaAktif        = Rental::where('status', 'aktif')->count();
        $hampirJatuhTempo = Rental::where('status', 'aktif')
            ->whereBetween('jatuh_tempo', [$today, $today->copy()->addHours(24)])->count();
        $dendaTerkumpul   = \App\Models\Fine::where('status', 'lunas')
            ->whereMonth('dibayar_pada', $today->month)->whereYear('dibayar_pada', $today->year)->sum('total_denda');
        $terlambatCount   = Rental::where('status', 'aktif')->where('jatuh_tempo', '<', $today)->count();
        $totalStok        = \App\Models\Equipment::sum('stok');
        $sedangDisewa     = Rental::where('status', 'aktif')->withCount('items')->get()->sum('items_count');
        $stokTersedia     = max(0, $totalStok - $sedangDisewa);

        $aktivitas = Rental::with('items.equipment')
            ->whereNotIn('status', ['pending', 'ditolak'])
            ->latest()->take(8)->get()
            ->map(fn($r) => [
                'nama'   => $r->nama_penyewa,
                'alat'   => \Illuminate\Support\Str::limit($r->items->pluck('equipment.nama')->filter()->join(', '), 30),
                'mulai'  => $r->mulai?->format('d M Y') ?? '-',
                'durasi' => $r->durasi,
                'total'  => 'Rp ' . number_format($r->total, 0, ',', '.'),
                'status' => $r->status,
                'label'  => match($r->status) {
                    'aktif'              => 'Aktif',
                    'selesai'            => 'Selesai',
                    'menunggu_pelunasan' => 'Terlambat',
                    default              => ucfirst($r->status),
                },
                'badge'  => match($r->status) {
                    'aktif'              => 'blue',
                    'selesai'            => 'green',
                    'menunggu_pelunasan' => 'orange',
                    default              => 'slate',
                },
            ]);

        return response()->json([
            'pendapatan_hari_ini' => 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'),
            'persen_vs_kemarin'   => $persenVsKemarin,
            'sewa_aktif'          => $sewaAktif,
            'hampir_jatuh_tempo'  => $hampirJatuhTempo,
            'denda_terkumpul'     => 'Rp ' . number_format($dendaTerkumpul, 0, ',', '.'),
            'terlambat_count'     => $terlambatCount,
            'stok_tersedia'       => $stokTersedia,
            'total_stok'          => $totalStok,
            'sedang_disewa'       => $sedangDisewa,
            'aktivitas'           => $aktivitas,
        ]);
    }
}
