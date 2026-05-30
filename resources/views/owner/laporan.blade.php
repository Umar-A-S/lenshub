@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('subtitle', 'Ringkasan keuangan LensHub')

@section('content')

{{-- FILTER BULAN --}}
<div class="flex items-center gap-4 mb-8">
    <form method="GET" action="{{ route('owner.laporan') }}" class="flex items-center gap-3">
        <select name="bulan" onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
            @foreach ($daftarBulan as $num => $nama)
                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>
        <input type="hidden" name="tahun" value="{{ $tahun }}">
    </form>

    <div class="flex gap-3 ml-auto">
        <a href="{{ route('owner.laporan') }}?bulan={{ $bulan }}&tahun={{ $tahun }}&export=pdf"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-700 text-white text-sm font-medium hover:bg-blue-800 transition">
            📄 Export PDF
        </a>
        <a href="{{ route('owner.laporan') }}?bulan={{ $bulan }}&tahun={{ $tahun }}&export=excel"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
            📊 Export Excel
        </a>
    </div>
</div>

{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-green-100 flex items-center justify-center text-xl mb-3">💰</div>
        <h3 class="text-2xl font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        <p class="text-sm text-slate-500 mt-1">Total Pendapatan</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center text-xl mb-3">📋</div>
        <h3 class="text-2xl font-bold">{{ $totalTransaksi }}</h3>
        <p class="text-sm text-slate-500 mt-1">Total Transaksi</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-yellow-100 flex items-center justify-center text-xl mb-3">👥</div>
        <h3 class="text-2xl font-bold">{{ $klienAktif }}</h3>
        <p class="text-sm text-slate-500 mt-1">Klien Aktif</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-red-100 flex items-center justify-center text-xl mb-3">⚠️</div>
        <h3 class="text-2xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h3>
        <p class="text-sm text-slate-500 mt-1">Total Denda</p>
    </div>
</div>

{{-- CHART PENDAPATAN PER KATEGORI --}}
<div class="bg-white rounded-3xl p-6 shadow-sm mb-8">
    <h3 class="text-xl font-bold mb-6">Pendapatan per Kategori Alat</h3>

    @if($pendapatanPerKategori->isEmpty())
        <div class="py-12 text-center text-slate-400">Belum ada data pendapatan bulan ini</div>
    @else
        @php $maxKat = $pendapatanPerKategori->max('total') ?: 1; @endphp
        <div class="space-y-4">
            @foreach ($pendapatanPerKategori as $kat)
                <div class="flex items-center gap-4">
                    <div class="w-24 text-sm text-right text-slate-600 shrink-0">{{ $kat['nama'] }}</div>
                    <div class="flex-1 bg-slate-100 rounded-full h-8 relative overflow-hidden">
                        <div class="h-full bg-blue-700 rounded-full flex items-center transition-all duration-700"
                             style="width: {{ $maxKat > 0 ? round(($kat['total'] / $maxKat) * 100) : 0 }}%">
                        </div>
                    </div>
                    <div class="w-32 text-sm font-medium text-right shrink-0">
                        Rp {{ number_format($kat['total'], 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- RINGKASAN MINGGUAN --}}
<div class="bg-white rounded-3xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold">Ringkasan Laporan Keuangan — {{ $carbonBulan->translatedFormat('F Y') }}</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-slate-500 text-xs uppercase tracking-wider">
                    <th class="text-left py-3 px-3">Periode</th>
                    <th class="text-right py-3 px-3">Transaksi</th>
                    <th class="text-right py-3 px-3">Pendapatan Sewa</th>
                    <th class="text-right py-3 px-3">Denda</th>
                    <th class="text-right py-3 px-3">Total Gross</th>
                    <th class="text-right py-3 px-3">Biaya Operasional</th>
                    <th class="text-right py-3 px-3">Net Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mingguData as $mg)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-4 px-3 font-medium">{{ $mg['label'] }}</td>
                        <td class="py-4 px-3 text-right">{{ $mg['transaksi'] }}</td>
                        <td class="py-4 px-3 text-right">Rp {{ number_format($mg['pendapatan'], 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right">Rp {{ number_format($mg['denda'], 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right font-medium">Rp {{ number_format($mg['gross'], 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right text-slate-500">Rp {{ number_format($mg['biaya_op'], 0, ',', '.') }}</td>
                        <td class="py-4 px-3 text-right font-bold text-green-600">
                            Rp {{ number_format($mg['net_profit'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-bold text-slate-800 border-t-2">
                    <td class="py-4 px-3">Total Bulan Ini</td>
                    <td class="py-4 px-3 text-right">{{ collect($mingguData)->sum('transaksi') }}</td>
                    <td class="py-4 px-3 text-right">Rp {{ number_format(collect($mingguData)->sum('pendapatan'), 0, ',', '.') }}</td>
                    <td class="py-4 px-3 text-right">Rp {{ number_format(collect($mingguData)->sum('denda'), 0, ',', '.') }}</td>
                    <td class="py-4 px-3 text-right">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
                    <td class="py-4 px-3 text-right text-slate-500">Rp {{ number_format($totalBiayaOp, 0, ',', '.') }}</td>
                    <td class="py-4 px-3 text-right text-green-600">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
