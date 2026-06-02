@extends('layouts.admin')

@section('title', 'Manajemen Sewa')
@section('subtitle', 'Monitoring Real-Time · ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="p-8">

    {{-- SEARCH --}}
    <form action="{{ route('sewa') }}" method="GET" class="bg-[#1E2A5E] rounded-[var(--border-radius-card)] p-6 shadow-xs mb-8">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-white mb-2 uppercase">Cari Sewa</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Kode, klien, alat..."
                    class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition">
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-white mb-2 uppercase">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition">
                    <option value="">Semua</option>
                    <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
                    <option value="menunggu_pelunasan" @selected(request('status')==='menunggu_pelunasan')>Pelunasan</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-white mb-2 uppercase">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-[var(--bg-sidebar)] hover:bg-[#1e2a5e]/90 text-white font-semibold py-2.5 px-6 rounded-[var(--border-radius-btn)] transition shadow-sm flex items-center justify-center border border-[var(--border-default)]">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('sewa') }}" class="bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[var(--bg-sidebar)] font-semibold py-2.5 px-6 rounded-[var(--border-radius-btn)] transition shadow-sm flex items-center justify-center">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </div>
    </form>

    {{-- INDIKATOR --}}
    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-1">
        @php
            $indikator = [
                ['label' => 'Sewa Aktif', 'value' => $sewaAktif, 'text' => 'text-[#2563EB]'],
                ['label' => 'Hampir Jatuh Tempo', 'value' => $hampirJatuhTempo, 'text' => 'text-[#CA8A04]'],
                ['label' => 'Terlambat', 'value' => $terlambat, 'text' => 'text-[#DC2626]'],
                ['label' => 'Selesai Bulan Ini', 'value' => $selesaiBulanIni, 'text' => 'text-[#16A34A]'],
            ];
        @endphp
        @foreach($indikator as $item)
        <div class="bg-[var(--bg-card)] rounded-[8px] p-6 shadow-sm border border-[var(--border-default)] hover:shadow-md transition-all duration-300">
            <h3 class="text-2xl font-extrabold {{ $item['text'] }} font-mono-numbers">{{ $item['value'] }}</h3>
            <p class="text-xs font-400 text-[var(--text-primary)] tracking-wider mt-1">{{ $item['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- COUNTDOWN CARDS --}}
    @if($sewaCards->count())
    <div class="mb-8 grid grid-cols-3 gap-6">
        @foreach ($sewaCards as $sewa)
            @php
                $detik  = $sewa->sisa_detik;
                $lewat  = $sewa->sudah_lewat;
                $border = $lewat ? 'border-[var(--color-accent-red)]' : ($detik < 86400 ? 'border-[var(--color-accent-yellow)]' : 'border-[var(--border-default)]');
                $badge  = $lewat ? 'bg-[#FEE2E2] text-[#DC2626]' : ($detik < 86400 ? 'bg-[#FEF9C3] text-[#CA8A04]' : 'bg-[#EFF6FF] text-[#2563EB]');
                $timer  = $lewat ? 'text-[var(--color-accent-red)]' : ($detik < 86400 ? 'text-[var(--color-accent-yellow)]' : 'text-[var(--color-primary)]');
            @endphp
            <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-3 shadow-sm border-2 {{ $border }} hover:shadow-md transition-all duration-300 min-w-80">
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-[var(--text-primary)]">{{ $sewa->nama_penyewa ?? $sewa->client?->nama ?? '-' }}</h3>
                        <p class="text-xs text-[var(--text-secondary)]">{{ $sewa->alat_nama ?: '-' }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ $sewa->kode_sewa }}</p>
                    </div>
                    <span class="rounded-[var(--border-radius-badge)] {{ $badge }} px-3 py-1 text-xs font-medium uppercase tracking-wider">
                        {{ $lewat ? 'TERLAMBAT' : ($detik < 86400 ? 'HAMPIR HABIS' : 'AMAN') }}
                    </span>
                </div>

                <div class="my-4 text-3xl font-bold {{ $timer }} font-mono-numbers"
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
                        WhatsApp
                    </a>
                    <button
                        onclick='openPengembalian({{ $sewa->id }}, @json($sewa->nama_penyewa ?? $sewa->client?->nama), @json($sewa->alat_nama), @json($sewa->jatuh_tempo->copy()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i:sP')))'
                        class="flex-1 rounded-xl bg-[#073090] py-2 text-center text-xs font-medium text-white hover:bg-blue-800 transition">
                        Pengembalian
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- TABEL SEMUA AKTIF --}}
    <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 shadow-sm border border-[var(--border-default)] overflow-x">
        <h3 class="mb-6 text-s font-bold text-[var(--text-primary)]">Semua Sewa Aktif & Menunggu Pelunasan</h3>

        <div class="overflow-y-hidden">
            <table class="w-full text-xs">
                <thead class="bg-[#F9FAFB] text-[var(--text-muted)] text-xs tracking-wider text-center">
                    <tr>
                        <th class="px-5 py-4">ID Transaksi</th>
                        <th class="px-5 py-4">Nama & WA</th>
                        <th class="px-5 py-4">Alat</th>
                        <th class="px-5 py-4">Jaminan</th>
                        <th class="px-5 py-4">Sisa Waktu</th>
                        <th class="px-5 py-4">Denda</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-default)]">
                    @forelse ($rentals as $rental)
                        <tr class="hover:bg-[#F9FAFB]">
                            <td class="px-5 py-4 font-semibold text-[var(--color-primary)]">{{ $rental->kode_sewa }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-[var(--text-primary)]">{{ $rental->nama_penyewa ?? '-' }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $rental->whatsapp }}</p>
                            </td>
                            <td class="px-5 py-4 text-[var(--text-secondary)]">{{ $rental->alat_nama ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-[var(--border-radius-badge)] bg-[var(--border-default)] px-2 py-1 text-xs uppercase text-[var(--text-secondary)]">
                                    {{ $rental->jaminan_fisik ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="{{ $rental->sudah_lewat ? 'text-[var(--color-accent-red)] font-bold' : ($rental->sisa_detik < 86400 ? 'text-[var(--color-accent-yellow)] font-semibold' : 'text-[var(--color-primary)]') }}">
                                    {{ $rental->sisa_waktu }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[var(--text-primary)] font-mono-numbers">
                                {{ $rental->total_denda > 0 ? 'Rp ' . number_format($rental->total_denda, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($rental->status === 'aktif')
                                    <span class="rounded-[var(--border-radius-badge)] bg-[#EFF6FF] px-3 py-1 text-xs text-[#2563EB]">Aktif</span>
                                @else
                                    <span class="rounded-[var(--border-radius-badge)] bg-[#FEF9C3] px-3 py-1 text-xs text-[#CA8A04] font-medium">Menunggu Pelunasan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex flex-row gap-2 flex-wrap">
                                    <a href="{{ route('sewa.wa-reminder', $rental) }}" target="_blank"
                                        class="rounded-[var(--border-radius-btn)] bg-[#DCFCE7] px-3 py-2 text-xs text-[#16A34A] hover:bg-[#BBF7D0]">
                                        WhatsApp
                                    </a>
                                    @if($rental->status === 'aktif')
                                        <button
                                            onclick='openPengembalian({{ $rental->id }}, @json($rental->nama_penyewa), @json($rental->alat_nama), @json($rental->jatuh_tempo->copy()->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i:sP')))'
                                            class="rounded-[var(--border-radius-btn)] bg-[var(--color-primary)] px-3 py-2 text-xs text-white hover:bg-[var(--color-primary-dark)]">
                                            Pengembalian
                                        </button>
                                    @else
                                        <button
                                            onclick='openLunasDenda({{ $rental->id }}, @json($rental->nama_penyewa), {{ $rental->total_denda }})'
                                            class="rounded-[var(--border-radius-btn)] bg-[var(--color-accent-yellow)] px-3 py-2 text-xs text-white hover:bg-[#D97706]">
                                            Lunasi Denda
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-[var(--text-muted)]">
                                <p class="text-4xl mb-3"></p>
                                <p>Tidak ada sewa aktif saat ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
