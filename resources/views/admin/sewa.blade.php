@extends('layouts.admin')

@section('title', 'Manajemen Sewa')
@section('subtitle', 'Monitoring Real-Time · ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="p-8">

    {{-- SEARCH --}}
    <form action="{{ route('sewa') }}" method="GET" class="mb-8 flex items-center gap-5">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari sewa, klien, alat..."
            class="h-[58px] flex-1 rounded-2xl bg-white px-8 outline-none shadow-sm">
        <select name="status" class="h-[58px] w-[220px] rounded-2xl bg-white px-5 shadow-sm">
            <option value="">Semua Status</option>
            <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
            <option value="menunggu_pelunasan" @selected(request('status')==='menunggu_pelunasan')>Menunggu Pelunasan</option>
        </select>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
            class="h-[58px] w-[220px] rounded-2xl bg-white px-5 shadow-sm">
        <button type="submit" class="h-[58px] rounded-2xl bg-[#073090] px-8 text-white">Cari</button>
        <a href="{{ route('sewa') }}" class="flex h-[58px] items-center rounded-2xl bg-gray-300 px-8">Reset</a>
    </form>

    {{-- INDIKATOR --}}
    <div class="mb-8 grid grid-cols-4 gap-6">
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Sewa Aktif</p>
            <h3 class="mt-3 text-4xl font-bold text-blue-700">{{ $sewaAktif }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Hampir Jatuh Tempo</p>
            <h3 class="mt-3 text-4xl font-bold text-yellow-500">{{ $hampirJatuhTempo }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Terlambat</p>
            <h3 class="mt-3 text-4xl font-bold text-red-600">{{ $terlambat }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Selesai Bulan Ini</p>
            <h3 class="mt-3 text-4xl font-bold text-green-600">{{ $selesaiBulanIni }}</h3>
        </div>
    </div>

    {{-- COUNTDOWN CARDS --}}
    @if($sewaCards->count())
    <div class="mb-8 grid grid-cols-3 gap-6">
        @foreach ($sewaCards as $sewa)
            @php
                $detik  = $sewa->sisa_detik;
                $lewat  = $sewa->sudah_lewat;
                $warna  = $lewat ? 'border-red-400 bg-red-50' : ($detik < 86400 ? 'border-yellow-400 bg-yellow-50' : 'border-blue-200 bg-white');
                $badge  = $lewat ? 'bg-red-100 text-red-700' : ($detik < 86400 ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700');
                $timer  = $lewat ? 'text-red-600' : ($detik < 86400 ? 'text-yellow-600' : 'text-blue-700');
            @endphp
            <div class="rounded-[28px] border-2 {{ $warna }} p-6 shadow-sm">
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $sewa->nama_penyewa ?? $sewa->client?->nama ?? '-' }}</h3>
                        <p class="text-sm text-slate-500">{{ $sewa->alat_nama ?: '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $sewa->kode_sewa }}</p>
                    </div>
                    <span class="rounded-full {{ $badge }} px-3 py-1 text-xs font-medium">
                        {{ $lewat ? 'TERLAMBAT' : ($detik < 86400 ? 'HAMPIR HABIS' : 'AMAN') }}
                    </span>
                </div>

                <div class="my-4 text-3xl font-bold {{ $timer }}"
                    data-countdown="{{ $sewa->jatuh_tempo->copy()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i:sP') }}">
                    {{ $sewa->sisa_waktu }}
                </div>

                <div class="mb-4 text-xs text-slate-400 flex justify-between">
                    <span>Mulai: {{ $sewa->mulai->format('d M H:i') }}</span>
                    <span>Tempo: {{ $sewa->jatuh_tempo->format('d M H:i') }}</span>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('sewa.wa-reminder', $sewa) }}" target="_blank"
                        class="flex-1 rounded-xl bg-green-100 py-2 text-center text-xs font-medium text-green-700 hover:bg-green-200 transition">
                        📲 WA Reminder
                    </a>
                    <button
                        onclick='openPengembalian({{ $sewa->id }}, @json($sewa->nama_penyewa ?? $sewa->client?->nama), @json($sewa->alat_nama), @json($sewa->jatuh_tempo->copy()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i:sP')))'
                        class="flex-1 rounded-xl bg-[#073090] py-2 text-center text-xs font-medium text-white hover:bg-blue-800 transition">
                        Proses Pengembalian
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- TABEL SEMUA AKTIF --}}
    <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h3 class="mb-6 text-2xl font-bold">Semua Sewa Aktif & Menunggu Pelunasan</h3>

        <div class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">ID Transaksi</th>
                        <th class="px-5 py-4 text-left">Nama & WA</th>
                        <th class="px-5 py-4 text-left">Alat</th>
                        <th class="px-5 py-4 text-left">Jaminan</th>
                        <th class="px-5 py-4 text-left">Sisa Waktu</th>
                        <th class="px-5 py-4 text-left">Denda</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rentals as $rental)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-blue-700">{{ $rental->kode_sewa }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $rental->nama_penyewa ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $rental->whatsapp }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $rental->alat_nama ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs uppercase">
                                    {{ $rental->jaminan_fisik ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="{{ $rental->sudah_lewat ? 'text-red-600 font-bold' : ($rental->sisa_detik < 86400 ? 'text-yellow-600 font-semibold' : 'text-blue-700') }}">
                                    {{ $rental->sisa_waktu }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                {{ $rental->total_denda > 0 ? 'Rp ' . number_format($rental->total_denda, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($rental->status === 'aktif')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs text-yellow-700 font-medium">Menunggu Pelunasan Denda</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex gap-2 flex-wrap">
                                    <a href="{{ route('sewa.wa-reminder', $rental) }}" target="_blank"
                                        class="rounded-xl bg-green-50 px-3 py-2 text-xs text-green-700 hover:bg-green-100">
                                        📲 WA
                                    </a>
                                    @if($rental->status === 'aktif')
                                        <button
                                            onclick='openPengembalian({{ $rental->id }}, @json($rental->nama_penyewa), @json($rental->alat_nama), @json($rental->jatuh_tempo->copy()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i:sP')))'
                                            class="rounded-xl bg-[#073090] px-3 py-2 text-xs text-white hover:bg-blue-800">
                                            Pengembalian
                                        </button>
                                    @else
                                        <button
                                            onclick='openLunasDenda({{ $rental->id }}, @json($rental->nama_penyewa), {{ $rental->total_denda }})'
                                            class="rounded-xl bg-yellow-500 px-3 py-2 text-xs text-white hover:bg-yellow-600">
                                            Lunasi Denda
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                                <p class="text-4xl mb-3">✅</p>
                                <p>Tidak ada sewa aktif saat ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PROSES PENGEMBALIAN --}}
<div id="modalPengembalian" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-lg rounded-3xl bg-white p-8 shadow-2xl mx-4 my-auto">
        <h2 class="mb-1 text-2xl font-bold text-slate-900">Proses Pengembalian</h2>
        <p class="mb-4 text-sm text-slate-500">Periksa kondisi barang dan waktu pengembalian.</p>

        <div class="mb-5 rounded-2xl bg-slate-50 px-5 py-4 text-sm">
            <p class="text-slate-500">Penyewa: <span class="font-bold text-slate-900" id="prNama"></span></p>
            <p class="text-slate-500 mt-1">Alat: <span id="prAlat" class="font-medium"></span></p>
            <p class="text-slate-500 mt-1">Jatuh Tempo: <span id="prTempo" class="font-medium"></span></p>
            <p class="mt-2 text-xs font-semibold" id="prStatusWaktu"></p>
        </div>

        <form id="formPengembalian" method="POST" class="space-y-5">
            @csrf

            {{-- JENIS PELANGGARAN --}}
            <div class="rounded-2xl border border-slate-200 p-4 space-y-3">
                <p class="font-semibold text-slate-700">Jenis Pelanggaran (centang jika ada)</p>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="terlambat" id="cbTerlambat" value="1"
                        class="w-4 h-4 accent-blue-700"
                        onchange="toggleTerlambat()">
                    <span class="text-sm font-medium">Terlambat</span>
                </label>

                <div id="sectionTerlambat" class="hidden pl-7 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500">Jumlah Jam Telat</label>
                            <input type="number" name="telat_jam" id="telatJam" min="0" placeholder="0"
                                class="w-full mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                oninput="hitungDenda()">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Tarif / Jam (Rp)</label>
                            <input type="number" name="tarif_per_jam" id="tarifJam" min="0" placeholder="10000"
                                class="w-full mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                oninput="hitungDenda()">
                        </div>
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="rusak" id="cbRusak" value="1"
                        class="w-4 h-4 accent-red-600"
                        onchange="toggleRusak()">
                    <span class="text-sm font-medium">Rusak / Kotor</span>
                </label>

                <div id="sectionRusak" class="hidden pl-7 space-y-3">
                    <div>
                        <label class="text-xs text-slate-500">Deskripsi Kerusakan</label>
                        <textarea name="deskripsi_kerusakan" rows="2" placeholder="Contoh: Layar retak, bodi kotor berat..."
                            class="w-full mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Biaya Kompensasi (Rp)</label>
                        <input type="number" name="biaya_kerusakan" id="biayaRusak" min="0" placeholder="0"
                            class="w-full mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                            oninput="hitungDenda()">
                    </div>
                </div>
            </div>

            {{-- TOTAL DENDA --}}
            <div class="rounded-2xl bg-red-50 px-5 py-4 flex items-center justify-between">
                <span class="font-semibold text-slate-700">Total Denda</span>
                <span class="text-2xl font-bold text-red-600">Rp <span id="totalDendaDisplay">0</span></span>
            </div>

            {{-- SKENARIO PEMBAYARAN --}}
            <div id="sectionPembayaran" class="hidden space-y-3">
                <p class="font-semibold text-slate-700">Metode Pembayaran Denda</p>
                <select name="metode_bayar_denda"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">-- Pilih Metode --</option>
                    <option value="tunai">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                </select>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="submitPengembalian('lunas', this)"
                        class="rounded-2xl bg-green-600 py-3 text-sm text-white font-semibold hover:bg-green-700 transition">
                        ✅ Denda Lunas di Tempat
                    </button>
                    <button type="button" onclick="submitPengembalian('belum_bayar', this)"
                        class="rounded-2xl bg-yellow-500 py-3 text-sm text-white font-semibold hover:bg-yellow-600 transition">
                        ⏳ Simpan Status Denda (Belum Bayar)
                    </button>
                </div>
                <p class="text-xs text-slate-400 text-center">Jika belum bayar: KTP/jaminan ditahan, barang kembali ke stok, data tetap di Manajemen Sewa.</p>
            </div>

            {{-- TOMBOL JIKA TIDAK ADA DENDA --}}
            <div id="sectionSelesaiNormal">
                <button type="button" onclick="submitPengembalian('lunas', this)"
                    class="w-full rounded-2xl bg-green-600 py-3 text-white font-semibold hover:bg-green-700 transition">
                    ✅ Selesai Normal (Tidak Ada Denda)
                </button>
            </div>

            <input type="hidden" name="status_denda" id="inputStatusDenda" value="lunas">

            <button type="button" onclick="closePengembalian()"
                class="w-full rounded-2xl border border-slate-200 py-3 text-slate-600 hover:bg-slate-50 transition">
                Batal
            </button>
        </form>
    </div>
</div>

{{-- MODAL LUNASI DENDA --}}
<div id="modalLunasDenda" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl mx-4">
        <h2 class="mb-1 text-2xl font-bold text-slate-900">Pelunasan Denda</h2>
        <p class="mb-5 text-sm text-slate-500">Pelanggan datang kembali untuk melunasi denda.</p>

        <div class="mb-5 rounded-2xl bg-yellow-50 px-5 py-4">
            <p class="text-sm">Penyewa: <span class="font-bold" id="ldNama"></span></p>
            <p class="text-sm mt-1">Total Denda: <span class="font-bold text-red-600">Rp <span id="ldDenda"></span></span></p>
        </div>

        <form id="formLunasDenda" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1 text-sm font-medium text-slate-700">Metode Pembayaran</label>
                <select name="metode_bayar_denda" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-3">
                    <option value="tunai">💵 Tunai</option>
                    <option value="transfer">🏦 Transfer</option>
                    <option value="qris">📱 QRIS</option>
                </select>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-[#073090] py-3 text-white font-semibold hover:bg-blue-800 transition">
                ✅ Konfirmasi Selesai Transaksi
            </button>
            <button type="button" onclick="closeLunasDenda()"
                class="w-full rounded-2xl border border-slate-200 py-3 text-slate-600 hover:bg-slate-50 transition">
                Batal
            </button>
        </form>
    </div>
</div>

<style>
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}
</style>

<script>
// Disable mouse wheel changing number inputs
document.querySelectorAll('input[type=number]').forEach(el => {
    el.addEventListener('wheel', function(e) { e.preventDefault(); }, { passive: false });
});

// ── COUNTDOWN TIMER ──────────────────────────────────────────────
document.querySelectorAll('[data-countdown]').forEach(el => {
    function tick() {
        const target = new Date(el.dataset.countdown).getTime();
        const now    = Date.now();
        const diff   = Math.floor((target - now) / 1000);
        if (diff <= 0) {
            const abs = Math.abs(diff);
            const h   = Math.floor(abs / 3600);
            const m   = Math.floor((abs % 3600) / 60);
            const s   = abs % 60;
            el.textContent = `TERLAMBAT ${h}J ${m}M ${s}DTK`;
            el.classList.add('text-red-600');
        } else {
            const d = Math.floor(diff / 86400);
            const h = Math.floor((diff % 86400) / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            el.textContent = (d > 0 ? d + 'H ' : '') + h + 'J ' + m + 'M ' + s + 'DTK';
        }
    }
    tick(); setInterval(tick, 1000);
});

// ── MODAL PENGEMBALIAN ────────────────────────────────────────────
let totalDenda = 0;

function openPengembalian(id, nama, alat, tempoIso) {
    document.getElementById('prNama').textContent   = nama;
    document.getElementById('prAlat').textContent   = alat;
    const tempoDate = new Date(tempoIso);
    document.getElementById('prTempo').textContent  = tempoDate.toLocaleString('id-ID');
    const now = Date.now();
    const diff = (tempoDate.getTime() - now) / 1000;
    const statusEl = document.getElementById('prStatusWaktu');
    if (diff < 0) {
        const h = Math.floor(Math.abs(diff) / 3600);
        statusEl.textContent = `⚠️ Terlambat ${h} jam`;
        statusEl.className   = 'mt-2 text-xs font-semibold text-red-600';
    } else {
        statusEl.textContent = '✅ Tepat Waktu';
        statusEl.className   = 'mt-2 text-xs font-semibold text-green-600';
    }
    document.getElementById('formPengembalian').action = '/sewa/' + id + '/pengembalian';
    document.getElementById('cbTerlambat').checked  = false;
    document.getElementById('cbRusak').checked      = false;
    document.getElementById('sectionTerlambat').classList.add('hidden');
    document.getElementById('sectionRusak').classList.add('hidden');
    document.getElementById('totalDendaDisplay').textContent = '0';
    document.getElementById('sectionPembayaran').classList.add('hidden');
    document.getElementById('sectionSelesaiNormal').classList.remove('hidden');
    totalDenda = 0;
    document.getElementById('modalPengembalian').classList.remove('hidden');
    document.getElementById('modalPengembalian').classList.add('flex');
}

function closePengembalian() {
    document.getElementById('modalPengembalian').classList.add('hidden');
    document.getElementById('modalPengembalian').classList.remove('flex');
}

function toggleTerlambat() {
    const show = document.getElementById('cbTerlambat').checked;
    document.getElementById('sectionTerlambat').classList.toggle('hidden', !show);
    hitungDenda();
}

function toggleRusak() {
    const show = document.getElementById('cbRusak').checked;
    document.getElementById('sectionRusak').classList.toggle('hidden', !show);
    hitungDenda();
}

function hitungDenda() {
    let total = 0;
    if (document.getElementById('cbTerlambat').checked) {
        const jam   = parseInt(document.getElementById('telatJam').value) || 0;
        const tarif = parseInt(document.getElementById('tarifJam').value) || 0;
        total += jam * tarif;
    }
    if (document.getElementById('cbRusak').checked) {
        total += parseInt(document.getElementById('biayaRusak').value) || 0;
    }
    totalDenda = total;
    document.getElementById('totalDendaDisplay').textContent =
        new Intl.NumberFormat('id-ID').format(total);

    const adaDenda = document.getElementById('cbTerlambat').checked || document.getElementById('cbRusak').checked;
    document.getElementById('sectionPembayaran').classList.toggle('hidden', !adaDenda);
    document.getElementById('sectionSelesaiNormal').classList.toggle('hidden', adaDenda);
}

function submitPengembalian(statusDenda, btn) {
    document.getElementById('inputStatusDenda').value = statusDenda;
    const form = document.getElementById('formPengembalian');
    const data = new FormData(form);

    // disable clicked button to avoid duplicates
    if (btn) { btn.disabled = true; btn.dataset.origText = btn.textContent; btn.textContent = 'Memproses...'; }

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { closePengembalian(); window.location.reload(); }
    })
    .catch(() => alert('Terjadi kesalahan.'))
    .finally(() => { if (btn) { btn.disabled = false; btn.textContent = btn.dataset.origText || btn.textContent; } });
}

// ── MODAL LUNASI DENDA ────────────────────────────────────────────
function openLunasDenda(id, nama, denda) {
    document.getElementById('ldNama').textContent  = nama;
    document.getElementById('ldDenda').textContent = new Intl.NumberFormat('id-ID').format(denda);
    document.getElementById('formLunasDenda').action = '/sewa/' + id + '/lunas-denda';
    document.getElementById('modalLunasDenda').classList.remove('hidden');
    document.getElementById('modalLunasDenda').classList.add('flex');
}

function closeLunasDenda() {
    document.getElementById('modalLunasDenda').classList.add('hidden');
    document.getElementById('modalLunasDenda').classList.remove('flex');
}

document.getElementById('formLunasDenda').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const data = new FormData(form);

    // Disable submit to avoid duplicate clicks
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.dataset.origText = submitBtn.textContent; submitBtn.textContent = 'Memproses...'; }

    fetch(form.action, {
        method: 'POST',
        cache: 'no-store',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (res.processed === false) {
                alert('Denda sudah diproses sebelumnya.');
            }
            closeLunasDenda();
            window.location.reload();
        }
    })
    .catch(() => alert('Terjadi kesalahan saat melunasi denda.'))
    .finally(() => {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.origText || submitBtn.textContent; }
    });
});
</script>
@endsection
