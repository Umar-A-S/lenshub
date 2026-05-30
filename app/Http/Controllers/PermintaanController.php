<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Mail\RentalRekapMail;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PermintaanController extends Controller
{
    // ---------------------------------------------------------------
    // FRONTEND: Form sewa
    // ---------------------------------------------------------------

    public function formSewa(Equipment $equipment)
    {
        if (!auth()->check()) {
            session(['url.intended' => route('produk.sewa', $equipment)]);
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek akun dinonaktifkan
        if (($user->status ?? 'aktif') === 'nonaktif') {
            return redirect()->route('produk.show', $equipment)
                ->with('error_nonaktif', 'Akun Anda dinonaktifkan oleh admin. Silahkan hubungi admin untuk detailnya.');
        }

        if ($user->role !== 'user') {
            return redirect()->route('produk.show', $equipment)
                ->with('error', 'Admin/Owner tidak dapat membuat pesanan sewa.');
        }

        // ── Cek kelengkapan profil ────────────────────────────────────
        $kurang = $user->profilKurang();
        if (!empty($kurang)) {
            return redirect()->route('akun.profil')
                ->with('profil_tidak_lengkap', $kurang);
        }

        return view('produk.sewa', compact('equipment'));
    }

    public function store(Request $request, Equipment $equipment)
    {
        if (!auth()->check()) return redirect()->route('login');

        $user = auth()->user();

        if (($user->status ?? 'aktif') === 'nonaktif') {
            return back()->withErrors(['akun' => 'Akun Anda dinonaktifkan. Hubungi owner.']);
        }

        // ── Double-check profil lengkap (mencegah bypass lewat direct POST) ─
        $kurang = $user->profilKurang();
        if (!empty($kurang)) {
            return redirect()->route('akun.profil')
                ->with('profil_tidak_lengkap', $kurang);
        }

        $data = $request->validate([
            'nama_penyewa'      => ['required', 'string', 'max:255'],
            'whatsapp'          => ['required', 'string', 'max:20'],
            'tanggal_mulai'     => ['required', 'date_format:Y-m-d'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'durasi'            => ['required', 'in:12jam,1hari,3hari,5hari,7hari'],
            'logistik'          => ['required', 'in:ambil,cod'],
            'alamat_pengiriman' => ['nullable', 'string'],
        ]);

        $mulai = Carbon::createFromFormat('Y-m-d H:i', "{$data['tanggal_mulai']} {$data['jam_mulai']}", config('app.timezone'));
        if ($mulai->lt(now())) {
            return back()->withErrors(['tanggal_mulai' => 'Waktu pinjam tidak boleh sudah terlewati.'])->withInput();
        }

        $jatuhTempo = Rental::hitungJatuhTempo($mulai, $data['durasi']);
        $harga      = Rental::hitungHarga($equipment->harga_harian, $data['durasi']);

        $rentalAktifCount = RentalItem::where('equipment_id', $equipment->id)
            ->whereHas('rental', function ($q) use ($mulai, $jatuhTempo) {
                $q->whereIn('status', ['pending', 'aktif', 'menunggu_pelunasan'])
                  ->where('mulai', '<', $jatuhTempo)
                  ->where('jatuh_tempo', '>', $mulai);
            })->sum('qty');

        if ($rentalAktifCount >= $equipment->stok) {
            return back()->withErrors(['stok' => 'Stok tidak tersedia pada tanggal tersebut.'])->withInput();
        }

        $kode = 'LH-' . strtoupper(substr(uniqid(), -6));

        $rental = Rental::create([
            'kode_sewa'         => $kode,
            'client_id'         => null,
            'nama_penyewa'      => $data['nama_penyewa'],
            'whatsapp'          => $data['whatsapp'],
            'mulai'             => $mulai,
            'jatuh_tempo'       => $jatuhTempo,
            'durasi'            => $data['durasi'],
            'logistik'          => $data['logistik'],
            'alamat_pengiriman' => $data['alamat_pengiriman'] ?? null,
            'total'             => $harga,
            'status'            => 'pending',
            'user_id'           => auth()->id(),
        ]);

        RentalItem::create([
            'rental_id'    => $rental->id,
            'equipment_id' => $equipment->id,
            'qty'          => 1,
            'harga'        => $harga,
        ]);

        return redirect()->route('produk.sewa.sukses', $rental->kode_sewa);
    }

    public function sukses(string $kode)
    {
        $rental = Rental::where('kode_sewa', $kode)->firstOrFail();
        return view('produk.sewa-sukses', compact('rental'));
    }

    // ---------------------------------------------------------------
    // ADMIN: List permintaan
    // ---------------------------------------------------------------

    public function index(Request $request)
    {
        $query = Rental::with('items.equipment')
            ->where('status', 'pending')
            ->latest();

        if ($request->filled('search')) {
            $kw = strtolower(trim($request->search));
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(kode_sewa) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(nama_penyewa) LIKE ?', ["%{$kw}%"]);
            });
        }

        $permintaan = $query->get()->map(function ($r) {
            $r->alat_nama = $r->items->pluck('equipment.nama')->filter()->join(', ');
            return $r;
        });

        return view('admin.permintaan', compact('permintaan'));
    }

    public function indexPartial(Request $request)
    {
        $query = Rental::with('items.equipment')
            ->where('status', 'pending')->latest();

        if ($request->filled('search')) {
            $kw = strtolower(trim($request->search));
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(kode_sewa) LIKE ?', ["%{$kw}%"])
                  ->orWhereRaw('LOWER(nama_penyewa) LIKE ?', ["%{$kw}%"]);
            });
        }

        $permintaan = $query->get()->map(function ($r) {
            $r->alat_nama = $r->items->pluck('equipment.nama')->filter()->join(', ');
            return $r;
        });

        $html = view('admin.permintaan_rows', compact('permintaan'))->render();
        return response()->json(['html' => $html, 'count' => $permintaan->count()]);
    }

    // ---------------------------------------------------------------
    // ADMIN: Konfirmasi → aktifkan sewa + kirim WA ke penyewa
    // ---------------------------------------------------------------

    public function konfirmasi(Request $request, Rental $rental)
    {
        $request->validate([
            'jaminan_fisik'   => ['required', 'array', 'min:1'],
            'jaminan_fisik.*' => ['in:ktp,sim,ktm,deposit,kartu_pelajar,paspor,npwp,bpkb,stnk,lainnya'],
            'jaminan_lainnya' => ['nullable', 'string', 'max:100'],
            'metode_bayar'    => ['required', 'in:tunai,transfer,qris'],
            'catatan_kondisi' => ['nullable', 'string'],
        ]);

        $jaminanList = $request->jaminan_fisik;
        if (in_array('lainnya', $jaminanList) && $request->filled('jaminan_lainnya')) {
            $jaminanList = array_map(
                fn($j) => $j === 'lainnya' ? 'lainnya:' . $request->jaminan_lainnya : $j,
                $jaminanList
            );
        }

        // Use conditional update to avoid duplicate processing if button clicked multiple times.
        $payload = [
            'status'          => 'aktif',
            'jaminan_fisik'   => implode(',', $jaminanList),
            'metode_bayar'    => $request->metode_bayar,
            'status_bayar'    => 'lunas',
            'catatan_kondisi' => $request->catatan_kondisi,
        ];

        $updated = Rental::where('id', $rental->id)
            ->where('status', 'pending')
            ->update($payload);

        // If no rows updated, another request already processed this rental — avoid re-sending WA/email
        if ($updated) {
            $rental->refresh();
            $rental->load('items.equipment');
            WhatsAppService::kirimKonfirmasiPesanan($rental);

            // Kirim rekap konfirmasi ke email user (jika email tersedia)
            $user = $rental->user;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new RentalRekapMail($rental));
            }
        }

        return response()->json(['success' => true, 'processed' => (bool) $updated]);
    }

    // ---------------------------------------------------------------
    // ADMIN: Tolak permintaan
    // ---------------------------------------------------------------

    public function tolak(Rental $rental)
    {
        $rental->update(['status' => 'ditolak']);
        return response()->json(['success' => true]);
    }
}
