@extends('layouts.admin')

@section('title', 'Dashboard Monitor')
@php $subtitleTanggal = 'Hari ini, ' . now()->locale('id')->isoFormat('D MMMM YYYY'); @endphp
@section('subtitle', $subtitleTanggal)

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-4 gap-6 mb-10">
    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Pendapatan Hari Ini</p>
        <h3 id="stat-pendapatan" class="text-3xl font-bold mt-3">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
        <p id="stat-persen" class="text-xs mt-2 {{ $persenVsKemarin >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $persenVsKemarin >= 0 ? '+' : '' }}{{ $persenVsKemarin }}% vs kemarin
        </p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Sewa Aktif</p>
        <h3 id="stat-sewa-aktif" class="text-3xl font-bold mt-3">{{ $sewaAktif }}</h3>
        <p id="stat-jatuh-tempo" class="text-xs mt-2 {{ $hampirJatuhTempo > 0 ? 'text-red-500' : 'text-green-600' }}">
            {{ $hampirJatuhTempo > 0 ? $hampirJatuhTempo . ' hampir jatuh tempo' : 'Semua aman' }}
        </p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Denda Terkumpul</p>
        <h3 id="stat-denda" class="text-3xl font-bold mt-3">Rp {{ number_format($dendaTerkumpul, 0, ',', '.') }}</h3>
        <p id="stat-terlambat" class="text-xs mt-2 {{ $terlambatCount > 0 ? 'text-red-500' : 'text-green-600' }}">
            {{ $terlambatCount > 0 ? $terlambatCount . ' penyewa terlambat' : 'Tidak ada keterlambatan' }}
        </p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Stok Tersedia</p>
        <h3 id="stat-stok" class="text-3xl font-bold mt-3">{{ $stokTersedia }} / {{ $totalStok }}</h3>
        <p id="stat-disewa" class="text-xs mt-2 {{ $sedangDisewa > 0 ? 'text-blue-500' : 'text-green-600' }}">
            {{ $sedangDisewa > 0 ? $sedangDisewa . ' sedang disewa' : 'Semua tersedia' }}
        </p>
    </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-2 gap-6 mb-10">

    {{-- Pendapatan 7 Hari --}}
    <div class="bg-white rounded-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Pendapatan 7 Hari</h3>
            <a href="{{ route('transaksi.index') }}"
               class="text-sm px-4 py-2 rounded-xl bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
               Laporan penuh
            </a>
        </div>

        @php $maxP = max(array_filter($pendapatan7Hari) ?: [0]); @endphp

        <div class="h-[260px] flex items-end gap-3">
            @foreach ($pendapatan7Hari as $i => $nilai)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] text-slate-400 text-center leading-tight">
                        {{ $nilai > 0 ? 'Rp ' . number_format($nilai/1000, 0, ',', '.') . 'k' : 'Rp 0' }}
                    </span>
                    @if($maxP > 0 && $nilai > 0)
                        @php $tinggi = max(12, round(($nilai / $maxP) * 200)); @endphp
                        <div class="w-full bg-blue-700 rounded-t-lg hover:bg-blue-500 transition-all duration-500"
                             style="height: {{ $tinggi }}px"
                             title="Rp {{ number_format($nilai, 0, ',', '.') }}"></div>
                    @else
                        <div class="w-full bg-slate-200 rounded-t-lg" style="height: 4px"></div>
                    @endif
                    <span class="text-xs text-slate-500">{{ $labels7Hari[$i] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Distribusi Kategori --}}
    <div class="bg-white rounded-3xl p-6">
        <h3 class="text-xl font-bold mb-4">Distribusi Kategori</h3>
        @if($distribusiKategori->isEmpty())
            <div class="h-[260px] flex items-center justify-center text-slate-400 flex-col gap-2">
                <span class="text-4xl">📊</span>
                <p>Belum ada data sewa aktif</p>
            </div>
        @else
            <div class="space-y-4 mt-2">
                @php $colors = ['bg-blue-900','bg-blue-600','bg-blue-400','bg-blue-300','bg-blue-200','bg-slate-300']; @endphp
                @foreach ($distribusiKategori as $i => $kat)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-slate-700">{{ $kat['nama'] }}</span>
                            <span class="text-slate-400 text-xs">{{ $kat['persen'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="{{ $colors[$i % count($colors)] }} h-3 rounded-full"
                                 style="width: {{ $kat['persen'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- AKTIVITAS TERKINI --}}
<div class="bg-white rounded-3xl p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-xl font-bold">Aktivitas Sewa Terkini</h3>
        <a href="{{ route('sewa') }}"
           class="text-sm px-4 py-2 rounded-xl bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
           Lihat Semua
        </a>
    </div>

    <table class="w-full text-sm">
        <thead class="text-slate-500 border-b">
            <tr>
                <th class="text-left py-3">Klien</th>
                <th class="text-left py-3">Alat</th>
                <th class="text-left py-3">Mulai</th>
                <th class="text-left py-3">Durasi</th>
                <th class="text-left py-3">Total</th>
                <th class="text-left py-3">Status</th>
            </tr>
        </thead>
        <tbody id="aktivitas-tbody">
            @forelse ($aktivitasTerkini as $r)
                <tr class="border-b hover:bg-slate-50">
                    <td class="py-4 font-medium">{{ $r->nama_penyewa }}</td>
                    <td class="text-slate-600">{{ Str::limit($r->alat_nama, 30) }}</td>
                    <td class="text-slate-600">{{ $r->mulai?->format('d M Y') }}</td>
                    <td class="text-slate-600">{{ $r->durasi }}</td>
                    <td class="font-medium">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $badge = match($r->status) {
                                'aktif'              => 'bg-blue-100 text-blue-700',
                                'selesai'            => 'bg-green-100 text-green-700',
                                'menunggu_pelunasan' => 'bg-orange-100 text-orange-700',
                                default              => 'bg-slate-100 text-slate-600',
                            };
                            $label = match($r->status) {
                                'aktif'              => 'Aktif',
                                'selesai'            => 'Selesai',
                                'menunggu_pelunasan' => 'Terlambat',
                                default              => ucfirst($r->status),
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $label }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-400">Belum ada aktivitas sewa</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
const POLL_DASHBOARD_URL = "{{ route('poll.dashboard') }}";
const POLL_ADMIN_URL     = "{{ route('poll.admin') }}";

// ── Update stat cards dari JSON ──────────────────────────────────────
async function refreshDashboard() {
    try {
        const res  = await fetch(POLL_DASHBOARD_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        // Cards
        document.getElementById('stat-pendapatan').textContent  = data.pendapatan_hari_ini;
        document.getElementById('stat-sewa-aktif').textContent  = data.sewa_aktif;
        document.getElementById('stat-denda').textContent       = data.denda_terkumpul;
        document.getElementById('stat-stok').textContent        = data.stok_tersedia + ' / ' + data.total_stok;

        const elPersen = document.getElementById('stat-persen');
        const persen   = data.persen_vs_kemarin;
        elPersen.textContent  = (persen >= 0 ? '+' : '') + persen + '% vs kemarin';
        elPersen.className    = 'text-xs mt-2 ' + (persen >= 0 ? 'text-green-600' : 'text-red-500');

        const elJT = document.getElementById('stat-jatuh-tempo');
        elJT.textContent = data.hampir_jatuh_tempo > 0
            ? data.hampir_jatuh_tempo + ' hampir jatuh tempo'
            : 'Semua aman';
        elJT.className = 'text-xs mt-2 ' + (data.hampir_jatuh_tempo > 0 ? 'text-red-500' : 'text-green-600');

        const elTlb = document.getElementById('stat-terlambat');
        elTlb.textContent = data.terlambat_count > 0
            ? data.terlambat_count + ' penyewa terlambat'
            : 'Tidak ada keterlambatan';
        elTlb.className = 'text-xs mt-2 ' + (data.terlambat_count > 0 ? 'text-red-500' : 'text-green-600');

        const elDisewa = document.getElementById('stat-disewa');
        elDisewa.textContent = data.sedang_disewa > 0
            ? data.sedang_disewa + ' sedang disewa'
            : 'Semua tersedia';
        elDisewa.className = 'text-xs mt-2 ' + (data.sedang_disewa > 0 ? 'text-blue-500' : 'text-green-600');

        // Tabel aktivitas terkini
        const tbody = document.getElementById('aktivitas-tbody');
        if (data.aktivitas && data.aktivitas.length > 0) {
            const badgeMap = { blue: 'bg-blue-100 text-blue-700', green: 'bg-green-100 text-green-700', orange: 'bg-orange-100 text-orange-700', slate: 'bg-slate-100 text-slate-600' };
            tbody.innerHTML = data.aktivitas.map(r => `
                <tr class="border-b hover:bg-slate-50">
                    <td class="py-4 font-medium">${r.nama}</td>
                    <td class="text-slate-600">${r.alat}</td>
                    <td class="text-slate-600">${r.mulai}</td>
                    <td class="text-slate-600">${r.durasi}</td>
                    <td class="font-medium">${r.total}</td>
                    <td><span class="px-3 py-1 rounded-full text-xs font-medium ${badgeMap[r.badge] || badgeMap.slate}">${r.label}</span></td>
                </tr>
            `).join('');
        }

    } catch (e) {
        console.warn('Dashboard poll error:', e);
    }
}

// ── Update badge pending di sidebar ─────────────────────────────────
async function pollAdmin() {
    try {
        const res  = await fetch(POLL_ADMIN_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        const badge = document.getElementById('admin-pending-count');
        if (badge) {
            if (data.pending_count > 0) {
                badge.textContent = data.pending_count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    } catch (e) {
        console.warn('Poll admin error:', e);
    }
}

// ── Jalankan polling setiap 15 detik ────────────────────────────────
setInterval(() => {
    refreshDashboard();
    pollAdmin();
}, 15000);
</script>
@endpush

@endsection
