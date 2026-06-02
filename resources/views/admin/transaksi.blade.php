@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('subtitle', 'Hari ini, ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="p-8">

    <form action="{{ route('transaksi.index') }}" method="GET" class="mb-8 flex items-center gap-5">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari transaksi, klien..."
            class="h-[58px] flex-1 rounded-2xl bg-white px-8 outline-none shadow-sm">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
            class="h-[58px] w-[220px] rounded-2xl bg-white px-5 shadow-sm">
        <button type="submit" class="h-[58px] rounded-2xl bg-[#073090] px-8 text-white">Cari</button>
        <a href="{{ route('transaksi.index') }}" class="flex h-[58px] items-center rounded-2xl bg-gray-300 px-8">Reset</a>
    </form>

    <div class="mb-8 grid grid-cols-4 gap-6">
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Total Transaksi Selesai</p>
            <h3 class="mt-3 text-2xl font-bold">{{ $transactions->count() }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Total Pendapatan</p>
            <h3 class="mt-3 text-xl font-bold text-green-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Sedang Aktif</p>
            <h3 class="mt-3 text-2xl font-bold text-blue-700">{{ $aktif }}</h3>
        </div>
        <div class="rounded-[28px] bg-white p-6 shadow-sm">
            <p class="text-slate-500">Total Denda Terkumpul</p>
            <h3 class="mt-3 text-xl font-bold text-red-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-2xl font-bold">Riwayat Transaksi (History)</h3>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Nama Penyewa</th>
                        <th class="px-5 py-4 text-left">Alat</th>
                        <th class="px-5 py-4 text-left">Durasi</th>
                        <th class="px-5 py-4 text-left">Logistik</th>
                        <th class="px-5 py-4 text-left">Total Sewa</th>
                        <th class="px-5 py-4 text-left">Metode Bayar</th>
                        <th class="px-5 py-4 text-left">Denda</th>
                        <th class="px-5 py-4 text-left">Dikembalikan</th>
                        <th class="px-5 py-4 text-left">Label</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-blue-700">{{ $trx->kode_sewa }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $trx->nama_penyewa ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $trx->whatsapp }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $trx->alat_nama ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-blue-50 px-2 py-1 text-xs text-blue-700">
                                    {{ match($trx->durasi) {
                                        '12jam'=>'12 Jam','1hari'=>'1 Hari','3hari'=>'3 Hari',
                                        '5hari'=>'5 Hari','7hari'=>'7 Hari', default=>$trx->durasi??'-'
                                    } }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                {{ $trx->logistik === 'cod' ? 'C.O.D' : 'Ambil' }}
                            </td>
                            <td class="px-5 py-4 font-semibold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 uppercase">{{ $trx->metode_bayar ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @if($trx->total_denda > 0)
                                    <span class="text-red-600 font-semibold">Rp {{ number_format($trx->total_denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-xs">
                                {{ $trx->dikembalikan_at ? \Carbon\Carbon::parse($trx->dikembalikan_at)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $label = $trx->label ?? 'Selesai';
                                    $color = str_contains($label, 'Denda') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
                                @endphp
                                <span class="rounded-full {{ $color }} px-3 py-1 text-xs font-medium">{{ $label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center text-slate-400">
                                <p class="text-4xl mb-3"></p>
                                <p>Belum ada riwayat transaksi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
