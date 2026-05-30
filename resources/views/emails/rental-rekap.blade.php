<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Sewa – {{ $rental->kode_sewa }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #111827; color: #fff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p  { margin: 4px 0 0; font-size: 13px; color: #9ca3af; }
        .body { padding: 28px 32px; }
        .kode { display: inline-block; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 14px; font-weight: 700; font-size: 15px; margin-bottom: 20px; }
        h2 { font-size: 15px; color: #374151; margin: 24px 0 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        table td { padding: 7px 4px; vertical-align: top; }
        table td:first-child { color: #6b7280; width: 45%; }
        table td:last-child { color: #111827; font-weight: 500; }
        .item-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 6px; }
        .item-table th { background: #f9fafb; color: #374151; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .item-table td { padding: 9px 10px; border-bottom: 1px solid #f3f4f6; color: #374151; }
        .total-row td { font-weight: 700; font-size: 15px; color: #111827; padding-top: 12px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-selesai   { background: #dcfce7; color: #15803d; }
        .badge-aktif     { background: #dbeafe; color: #1d4ed8; }
        .badge-pending   { background: #fef9c3; color: #854d0e; }
        .denda-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 14px 16px; margin-top: 10px; }
        .denda-box p { margin: 0 0 4px; font-size: 13px; color: #b91c1c; }
        .footer { background: #f9fafb; padding: 18px 32px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <h1>📷 LensHub</h1>
        <p>Rekap Transaksi Sewa Alat Fotografi</p>
    </div>

    <!-- Body -->
    <div class="body">

        <p style="font-size:15px;color:#374151;">Halo <strong>{{ $rental->nama_penyewa }}</strong>,</p>
        <p style="font-size:14px;color:#6b7280;margin-top:4px;">Berikut rekap lengkap transaksi sewa Anda di LensHub.</p>

        <span class="kode">{{ $rental->kode_sewa }}</span>

        <!-- Info Pesanan -->
        <h2>📋 Informasi Pesanan</h2>
        <table>
            <tr>
                <td>Nama Penyewa</td>
                <td>{{ $rental->nama_penyewa }}</td>
            </tr>
            <tr>
                <td>Nomor WhatsApp</td>
                <td>{{ $rental->whatsapp }}</td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td>{{ \Carbon\Carbon::parse($rental->mulai)->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</td>
            </tr>
            <tr>
                <td>Jatuh Tempo</td>
                <td>{{ \Carbon\Carbon::parse($rental->jatuh_tempo)->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td>{{ $rental->durasi }}</td>
            </tr>
            <tr>
                <td>Logistik</td>
                <td>{{ $rental->logistik === 'cod' ? 'COD / Antar Jemput' : 'Ambil Sendiri' }}</td>
            </tr>
            @if($rental->alamat_pengiriman)
            <tr>
                <td>Alamat Pengiriman</td>
                <td>{{ $rental->alamat_pengiriman }}</td>
            </tr>
            @endif
            <tr>
                <td>Jaminan Fisik</td>
                <td>{{ $rental->jaminan_fisik ? strtoupper(str_replace(',', ', ', $rental->jaminan_fisik)) : '-' }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td>{{ $rental->metode_bayar ? strtoupper($rental->metode_bayar) : '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    @php
                        $badgeClass = match($rental->status) {
                            'selesai'  => 'badge-selesai',
                            'aktif'    => 'badge-aktif',
                            default    => 'badge-pending',
                        };
                        $label = match($rental->status) {
                            'selesai'            => 'Selesai',
                            'aktif'              => 'Aktif',
                            'menunggu_pelunasan' => 'Menunggu Pelunasan',
                            'pending'            => 'Pending',
                            'ditolak'            => 'Ditolak',
                            default              => ucfirst($rental->status),
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                </td>
            </tr>
            @if($rental->dikembalikan_at)
            <tr>
                <td>Dikembalikan</td>
                <td>{{ \Carbon\Carbon::parse($rental->dikembalikan_at)->locale('id')->isoFormat('D MMMM YYYY HH:mm') }}</td>
            </tr>
            @endif
            @if($rental->catatan_kondisi)
            <tr>
                <td>Catatan Kondisi</td>
                <td>{{ $rental->catatan_kondisi }}</td>
            </tr>
            @endif
        </table>

        <!-- Alat Disewa -->
        <h2>🎥 Alat yang Disewa</h2>
        <table class="item-table">
            <thead>
                <tr>
                    <th>Nama Alat</th>
                    <th style="text-align:right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rental->items as $item)
                <tr>
                    <td>{{ $item->equipment?->nama ?? '-' }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Denda -->
        @if($rental->denda && $rental->denda > 0)
        <div class="denda-box">
            <p><strong>⚠️ Denda</strong></p>
            @if($rental->fine?->terlambat)
            <p>Keterlambatan {{ $rental->fine->telat_jam }} jam × Rp {{ number_format($rental->fine->tarif_per_jam, 0, ',', '.') }}
               = Rp {{ number_format($rental->fine->telat_jam * $rental->fine->tarif_per_jam, 0, ',', '.') }}</p>
            @endif
            @if($rental->fine?->biaya_kerusakan)
            <p>Kerusakan: Rp {{ number_format($rental->fine->biaya_kerusakan, 0, ',', '.') }}
               @if($rental->fine->deskripsi_kerusakan) – {{ $rental->fine->deskripsi_kerusakan }} @endif
            </p>
            @endif
            <p style="font-weight:700;font-size:14px;margin-top:6px;">
                Total Denda: Rp {{ number_format($rental->denda, 0, ',', '.') }}
                <span style="font-weight:400;color:#6b7280;">({{ $rental->status_denda === 'lunas' ? 'Lunas' : 'Belum Lunas' }})</span>
            </p>
        </div>
        @endif

        <!-- Total -->
        <table style="margin-top:20px;">
            <tr class="total-row">
                <td>Total Sewa</td>
                <td>Rp {{ number_format($rental->total, 0, ',', '.') }}</td>
            </tr>
            @if($rental->denda && $rental->denda > 0)
            <tr class="total-row">
                <td>Total Denda</td>
                <td>Rp {{ number_format($rental->denda, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="color:#dc2626;">
                <td>Grand Total</td>
                <td>Rp {{ number_format($rental->total + $rental->denda, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <p style="margin-top:24px;font-size:13px;color:#6b7280;">
            Terima kasih telah menggunakan layanan <strong>LensHub</strong>. Semoga peralatan kami membantu karya Anda! 🎬
        </p>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem LensHub. Jangan membalas email ini.</p>
        <p style="margin-top:4px;">Butuh bantuan? Hubungi kami via WhatsApp.</p>
    </div>

</div>
</body>
</html>
