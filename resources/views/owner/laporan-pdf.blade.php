<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan – {{ $carbonBulan->translatedFormat('F Y') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; padding: 32px; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #073090; padding-bottom: 12px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #073090; }
        .header p  { font-size: 11px; color: #64748b; margin-top: 2px; }
        .meta      { font-size: 11px; color: #64748b; text-align: right; }
        .section-title { font-size: 13px; font-weight: 700; color: #073090; margin: 20px 0 8px; border-left: 3px solid #073090; padding-left: 8px; }
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .card  { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; }
        .card-val { font-size: 16px; font-weight: 700; color: #073090; }
        .card-lbl { font-size: 10px; color: #64748b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #f8fafc; text-align: left; padding: 7px 8px; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        td.num { text-align: right; }
        th.num { text-align: right; }
        tfoot tr td { font-weight: 700; background: #f8fafc; border-top: 2px solid #e2e8f0; }
        .footer { margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 10px; color: #94a3b8; text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#073090;color:#fff;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;border-radius:0 0 12px 12px;">
    <span style="font-weight:700;font-size:14px;">📄 Preview Laporan PDF</span>
    <button onclick="window.print()" style="background:#fff;color:#073090;border:none;padding:8px 20px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;">🖨️ Print / Simpan PDF</button>
</div>

<div class="header">
    <div>
        <h1>📷 LensHub — Laporan Keuangan</h1>
        <p>Periode: {{ $carbonBulan->translatedFormat('F Y') }}</p>
    </div>
    <div class="meta">
        <p>Diekspor: {{ now()->format('d M Y, H:i') }}</p>
    </div>
</div>

<div class="section-title">Ringkasan Bulan</div>
<div class="cards">
    <div class="card">
        <div class="card-val">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        <div class="card-lbl">Total Pendapatan</div>
    </div>
    <div class="card">
        <div class="card-val">{{ $totalTransaksi }}</div>
        <div class="card-lbl">Total Transaksi</div>
    </div>
    <div class="card">
        <div class="card-val">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        <div class="card-lbl">Total Denda (Lunas)</div>
    </div>
    <div class="card">
        <div class="card-val">{{ $klienAktif }}</div>
        <div class="card-lbl">Klien Aktif</div>
    </div>
</div>

<div class="section-title">Ringkasan Per Minggu</div>
<table>
    <thead>
        <tr>
            <th>Periode</th>
            <th class="num">Transaksi</th>
            <th class="num">Pendapatan Sewa</th>
            <th class="num">Denda</th>
            <th class="num">Total Gross</th>
            <th class="num">Biaya Op (15%)</th>
            <th class="num">Net Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($mingguData as $mg)
        <tr>
            <td>{{ $mg['label'] }}</td>
            <td class="num">{{ $mg['transaksi'] }}</td>
            <td class="num">Rp {{ number_format($mg['pendapatan'], 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format($mg['denda'], 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format($mg['gross'], 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format($mg['biaya_op'], 0, ',', '.') }}</td>
            <td class="num" style="color:#16a34a;font-weight:600;">Rp {{ number_format($mg['net_profit'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total Bulan Ini</td>
            <td class="num">{{ collect($mingguData)->sum('transaksi') }}</td>
            <td class="num">Rp {{ number_format(collect($mingguData)->sum('pendapatan'), 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format(collect($mingguData)->sum('denda'), 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
            <td class="num">Rp {{ number_format($totalBiayaOp, 0, ',', '.') }}</td>
            <td class="num" style="color:#16a34a;">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

@if($pendapatanPerKategori->isNotEmpty())
<div class="section-title" style="margin-top:24px;">Pendapatan per Kategori</div>
<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th class="num">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pendapatanPerKategori as $kat)
        <tr>
            <td>{{ $kat['nama'] }}</td>
            <td class="num">Rp {{ number_format($kat['total'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    Laporan ini digenerate otomatis oleh sistem LensHub. © {{ now()->year }} LensHub Photography & Video Gear.
</div>

</body>
</html>
