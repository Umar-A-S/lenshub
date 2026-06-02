@extends('layouts.admin')

@section('title', 'Dashboard Monitor')
@php $subtitleTanggal = 'Hari ini, ' . now()->locale('id')->isoFormat('D MMMM YYYY'); @endphp
@section('subtitle', $subtitleTanggal)

@section('content')

<div class="p-4 sm:p-6 space-y-6">

{{-- STAT CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 mt-4">
    @php
        $cards = [
            ['title' => 'Pendapatan Hari Ini', 'value' => 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'), 'id' => 'stat-pendapatan', 'sub_id' => 'stat-persen', 'sub' => ($persenVsKemarin >= 0 ? '+' : '') . $persenVsKemarin . '% vs kemarin', 'sub_class' => ($persenVsKemarin >= 0 ? 'text-[#16A34A]' : 'text-[#EF4444]')],
            ['title' => 'Sewa Aktif', 'value' => $sewaAktif, 'id' => 'stat-sewa-aktif', 'sub_id' => 'stat-jatuh-tempo', 'sub' => ($hampirJatuhTempo > 0 ? $hampirJatuhTempo . ' hampir jatuh tempo' : 'Semua aman'), 'sub_class' => ($hampirJatuhTempo > 0 ? 'text-[#CA8A04]' : 'text-[#16A34A]')],
            ['title' => 'Denda Terkumpul', 'value' => 'Rp ' . number_format($dendaTerkumpul, 0, ',', '.'), 'id' => 'stat-denda', 'sub_id' => 'stat-terlambat', 'sub' => ($terlambatCount > 0 ? $terlambatCount . ' penyewa terlambat' : 'Tidak ada keterlambatan'), 'sub_class' => ($terlambatCount > 0 ? 'text-[#EF4444]' : 'text-[#16A34A]')],
            ['title' => 'Stok Tersedia', 'value' => $stokTersedia . ' / ' . $totalStok, 'id' => 'stat-stok', 'sub_id' => 'stat-disewa', 'sub' => ($sedangDisewa > 0 ? $sedangDisewa . ' sedang disewa' : 'Semua tersedia'), 'sub_class' => ($sedangDisewa > 0 ? 'text-[#2B4EFF]' : 'text-[#16A34A]')],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-5 shadow-sm border border-[var(--border-default)] hover:shadow-md transition-all duration-300">
        <p class="text-[var(--fs-small)] font-semibold text-[var(--text-muted)] tracking-wider">{{ $card['title'] }}</p>
        <h4 id="{{ $card['id'] }}" class="text-2xl font-extrabold mt-3 text-[var(--text-primary)] font-mono-numbers">{{ $card['value'] }}</h4>
        <p id="{{ $card['sub_id'] }}" class="text-sm mt-3 font-semibold {{ $card['sub_class'] }}">
            {{ $card['sub'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Pendapatan 7 Hari --}}
    <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 border border-[var(--border-default)] shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-[var(--fs-h2)] font-bold text-[var(--text-primary)]">Pendapatan 7 Hari Terakhir</h3>
            <a href="{{ route('transaksi.index') }}"
               class="text-[var(--fs-small)] px-3 py-1.5 rounded-lg bg-[#F9FAFB] text-[var(--text-secondary)] hover:bg-[#EFF6FF] hover:text-[#2B4EFF] transition">
               Lihat Detail
            </a>
        </div>

        @php $maxP = max(array_filter($pendapatan7Hari) ?: [0]); @endphp

        <div class="h-[200px] flex items-end gap-3">
            @foreach ($pendapatan7Hari as $i => $nilai)
                <div class="flex-1 flex flex-col items-center gap-1 group">
                    <span class="text-[var(--fs-small)] text-[var(--text-muted)] font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ $nilai > 0 ? number_format($nilai/1000, 0, ',', '.') . 'k' : '0' }}
                    </span>
                    @if($maxP > 0 && $nilai > 0)
                        @php $tinggi = max(8, round(($nilai / $maxP) * 160)); @endphp
                        <div class="w-full bg-[#2B4EFF] rounded-md hover:bg-[#1A3ACC] transition-all duration-300"
                             style="height: {{ $tinggi }}px"
                             title="Rp {{ number_format($nilai, 0, ',', '.') }}"></div>
                    @else
                        <div class="w-full bg-[#E5E7EB] rounded-md" style="height: 4px"></div>
                    @endif
                    <span class="text-[var(--fs-small)] text-[var(--text-secondary)] font-medium">{{ substr($labels7Hari[$i], 0, 3) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Distribusi Kategori --}}
    <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 border border-[var(--border-default)] shadow-sm">
        <h3 class="text-[var(--fs-h2)] font-bold text-[var(--text-primary)] mb-6">Distribusi Kategori</h3>
        @if($distribusiKategori->isEmpty())
            <div class="h-[200px] flex items-center justify-center text-[var(--text-muted)] flex-col gap-2 text-[var(--fs-body)]">
                <span>Belum ada data sewa aktif</span>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($distribusiKategori as $i => $kat)
                    <div>
                        <div class="flex justify-between text-[var(--fs-small)] mb-1.5">
                            <span class="font-semibold text-[var(--text-primary)]">{{ $kat['nama'] }}</span>
                            <span class="text-[var(--fs-small)] text-[var(--text-secondary)] font-medium">{{ $kat['persen'] }}%</span>
                        </div>
                        <div class="w-full bg-[#E5E7EB] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#2B4EFF] h-2 rounded-full transition-all duration-500"
                                 style="width: {{ $kat['persen'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- AKTIVITAS TERKINI --}}
<div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 border border-[var(--border-default)] shadow-sm">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-[var(--fs-h2)] font-bold text-[var(--text-primary)]">Aktivitas Sewa Terkini</h3>
        <a href="{{ route('sewa') }}"
           class="text-[var(--fs-small)] px-3 py-1.5 rounded-lg bg-[#F9FAFB] text-[var(--text-secondary)] hover:bg-[#EFF6FF] hover:text-[#2B4EFF] transition">
           Lihat Semua
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[var(--fs-body)]">
            <thead class="bg-[#F9FAFB] text-[var(--text-muted)] uppercase text-[var(--fs-small)] tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left font-bold">Klien</th>
                    <th class="px-6 py-4 text-left font-bold">Alat</th>
                    <th class="px-6 py-4 text-left font-bold">Mulai</th>
                    <th class="px-6 py-4 text-left font-bold">Total</th>
                    <th class="px-6 py-4 text-left font-bold">Status</th>
                </tr>
            </thead>
            <tbody id="aktivitas-tbody" class="divide-y divide-[var(--border-default)]">
                @forelse ($aktivitasTerkini as $r)
                    <tr class="hover:bg-[#F9FAFB] transition-colors">
                        <td class="px-6 py-4 font-semibold text-[var(--text-primary)]">{{ $r->nama_penyewa }}</td>
                        <td class="px-6 py-4 text-[var(--text-secondary)]">{{ Str::limit($r->alat_nama, 25) }}</td>
                        <td class="px-6 py-4 text-[var(--text-secondary)]">{{ $r->mulai?->format('d/m') }}</td>
                        <td class="px-6 py-4 font-semibold text-[var(--text-primary)] font-mono-numbers">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badge = match($r->status) {
                                    'aktif'              => 'bg-[#DCFCE7] text-[#16A34A]',
                                    'selesai'            => 'bg-[#F1F5F9] text-[#64748B]',
                                    'menunggu_pelunasan' => 'bg-[#FEF9C3] text-[#CA8A04]',
                                    default              => 'bg-[#F1F5F9] text-[#64748B]',
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-[var(--border-radius-badge)] text-[var(--fs-small)] font-bold uppercase tracking-wider {{ $badge }}">{{ $r->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-[var(--text-muted)]">Belum ada aktivitas sewa</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

@push('scripts')
<script>
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
        elPersen.className    = 'text-[11px] mt-2 font-semibold ' + (persen >= 0 ? 'text-green-600' : 'text-red-500');

        const elJT = document.getElementById('stat-jatuh-tempo');
        elJT.textContent = data.hampir_jatuh_tempo > 0
            ? data.hampir_jatuh_tempo + ' hampir jatuh tempo'
            : 'Semua aman';
        elJT.className = 'text-[11px] mt-2 font-semibold ' + (data.hampir_jatuh_tempo > 0 ? 'text-red-500' : 'text-green-600');

        const elTlb = document.getElementById('stat-terlambat');
        elTlb.textContent = data.terlambat_count > 0
            ? data.terlambat_count + ' penyewa terlambat'
            : 'Tidak ada keterlambatan';
        elTlb.className = 'text-[11px] mt-2 font-semibold ' + (data.terlambat_count > 0 ? 'text-red-500' : 'text-green-600');

        const elDisewa = document.getElementById('stat-disewa');
        elDisewa.textContent = data.sedang_disewa > 0
            ? data.sedang_disewa + ' sedang disewa'
            : 'Semua tersedia';
        elDisewa.className = 'text-[11px] mt-2 font-semibold ' + (data.sedang_disewa > 0 ? 'text-blue-500' : 'text-green-600');

        // Tabel aktivitas terkini
        const tbody = document.getElementById('aktivitas-tbody');
        if (data.aktivitas && data.aktivitas.length > 0) {
            const badgeMap = { blue: 'bg-blue-50 text-blue-600', green: 'bg-green-50 text-green-600', orange: 'bg-orange-50 text-orange-600', slate: 'bg-slate-50 text-slate-500' };
            tbody.innerHTML = data.aktivitas.map(r => `
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="py-3 font-medium text-slate-700">${r.nama}</td>
                    <td class="text-slate-500">${r.alat}</td>
                    <td class="text-slate-500">${r.mulai}</td>
                    <td class="font-medium text-slate-700">${r.total}</td>
                    <td><span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ${badgeMap[r.badge] || badgeMap.slate}">${r.status}</span></td>
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
