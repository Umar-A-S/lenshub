<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WA via Fonnte.
     */
    public static function kirim(string $nomor, string $pesan): bool
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        $token = config('services.fonnte.token');
        if (empty($token)) {
            Log::warning('[WhatsApp] Token Fonnte belum dikonfigurasi.');
            return false;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'  => $nomor,
                    'message' => $pesan,
                ]);

            if ($response->successful() && ($response->json('status') ?? false)) {
                return true;
            }

            Log::warning('[WhatsApp] Gagal kirim.', [
                'nomor'    => $nomor,
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception: ' . $e->getMessage());
            return false;
        }
    }

    // ---------------------------------------------------------------
    // KIRIM OTP VERIFIKASI NOMOR WA
    // ---------------------------------------------------------------

    /**
     * Kirim kode OTP verifikasi nomor WhatsApp.
     */
    public static function kirimOTP(string $nomor, string $kode): bool
    {
        $pesan = "🔐 *Verifikasi WhatsApp - LensHub*\n\n"
               . "Kode OTP Anda:\n\n"
               . "*{$kode}*\n\n"
               . "Kode berlaku selama *5 menit*.\n"
               . "Jangan bagikan kode ini kepada siapapun.\n\n"
               . "_LensHub Internal System_ 📷";

        return self::kirim($nomor, $pesan);
    }

    // ---------------------------------------------------------------
    // KIRIM OTP RESET PASSWORD
    // ---------------------------------------------------------------

    /**
     * Kirim kode OTP untuk reset password.
     */
    public static function kirimOtpReset(string $nomor, string $kode): bool
    {
        $pesan = "🔒 *Reset Password - LensHub*\n\n"
               . "Kami menerima permintaan untuk mereset password akun Anda.\n"
               . "Kode OTP Reset Anda:\n\n"
               . "*{$kode}*\n\n"
               . "Kode berlaku selama *5 menit*.\n"
               . "Jika Anda tidak merasa melakukan permintaan ini, abaikan pesan ini.\n\n"
               . "_LensHub Internal System_ 📷";

        return self::kirim($nomor, $pesan);
    }

    // ---------------------------------------------------------------
    // KONFIRMASI PESANAN (admin konfirmasi → aktif)
    // ---------------------------------------------------------------

    public static function kirimKonfirmasiPesanan(\App\Models\Rental $rental): bool
    {
        $alat       = $rental->items->pluck('equipment.nama')->join(', ');
        $mulai      = $rental->mulai?->format('d M Y H:i') ?? '-';
        $jatuhTempo = $rental->jatuh_tempo?->format('d M Y H:i') ?? '-';
        $logistik   = $rental->logistik === 'cod' ? 'Antar ke Alamat' : 'Ambil Sendiri';
        $metode     = strtoupper($rental->metode_bayar ?? '-');
        $total      = 'Rp ' . number_format($rental->total, 0, ',', '.');

        $pesan = "*Pesanan Dikonfirmasi - LensHub*\n\n"
               . "Halo *{$rental->nama_penyewa}*,\n"
               . "Pesanan sewa Anda telah *dikonfirmasi* dan sekarang aktif!\n\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . "*Detail Pesanan*\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . "Kode Sewa  : *{$rental->kode_sewa}*\n"
               . "Alat       : *{$alat}*\n"
               . "Mulai      : *{$mulai}*\n"
               . "Jatuh Tempo: *{$jatuhTempo}*\n"
               . "Logistik   : *{$logistik}*\n"
               . "Pembayaran : *{$metode}*\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . "Total      : *{$total}*\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n\n"
               . "Mohon kembalikan alat tepat waktu ya! 🙏\n\n"
               . "_LensHub Internal System_ 📷";

        return self::kirim($rental->whatsapp, $pesan);
    }

    // ---------------------------------------------------------------
    // PESANAN SELESAI (dikembalikan, denda sudah lunas/tidak ada)
    // ---------------------------------------------------------------

    public static function kirimPesananSelesai(\App\Models\Rental $rental): bool
    {
        $alat       = $rental->items->pluck('equipment.nama')->join(', ');
        $mulai      = $rental->mulai?->format('d M Y H:i') ?? '-';
        $jatuhTempo = $rental->jatuh_tempo?->format('d M Y H:i') ?? '-';
        $selesaiAt  = $rental->dikembalikan_at
            ? \Carbon\Carbon::parse($rental->dikembalikan_at)->format('d M Y H:i')
            : now()->format('d M Y H:i');

        $logistik = $rental->logistik === 'cod' ? 'Antar ke Alamat' : 'Ambil Sendiri';
        $metode   = strtoupper($rental->metode_bayar ?? '-');
        $total    = 'Rp ' . number_format($rental->total, 0, ',', '.');

        // ── Rincian denda detail ──────────────────────────────────
        $dendaInfo = '';
        if ($rental->denda && $rental->denda > 0) {
            $fine       = $rental->fine;
            $totalDenda = 'Rp ' . number_format($rental->denda, 0, ',', '.');

            $rincianBaris = '';
            if ($fine?->terlambat && $fine->telat_jam > 0) {
                $tarifFmt  = 'Rp ' . number_format($fine->tarif_per_jam, 0, ',', '.');
                $subtotal  = 'Rp ' . number_format($fine->telat_jam * $fine->tarif_per_jam, 0, ',', '.');
                $rincianBaris .= "Terlambat {$fine->telat_jam} jam × {$tarifFmt} = {$subtotal}\n";
            }
            if ($fine?->biaya_kerusakan && $fine->biaya_kerusakan > 0) {
                $biayaFmt  = 'Rp ' . number_format($fine->biaya_kerusakan, 0, ',', '.');
                $keterangan = $fine->deskripsi_kerusakan ? " ({$fine->deskripsi_kerusakan})" : '';
                $rincianBaris .= "  Kerusakan{$keterangan}: {$biayaFmt}\n";
            }

            $statusDenda = $rental->status_denda === 'lunas' ? '✅ Lunas' : '⏳ Belum Lunas';
            $dendaInfo   = "*Rincian Denda*\n"
                         . $rincianBaris
                         . "*Total Denda  : {$totalDenda}* ({$statusDenda})\n";
        }

        $grandTotal = $rental->denda > 0
            ? 'Rp ' . number_format($rental->total + $rental->denda, 0, ',', '.')
            : null;

        $totalBaris = "Total Sewa  : *{$total}*\n";
        if ($grandTotal) {
            $dendaFmt    = 'Rp ' . number_format($rental->denda, 0, ',', '.');
            $totalBaris .= "Denda       : *{$dendaFmt}*\n"
                        . "Grand Total : *{$grandTotal}*\n";
        }

        $pesan = "*Pesanan Selesai - LensHub*\n\n"
               . "Halo *{$rental->nama_penyewa}*,\n"
               . "Terima kasih! Pesanan sewa Anda telah selesai.\n\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . "*Rekap Pesanan*\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . "Kode Sewa   : *{$rental->kode_sewa}*\n"
               . "Alat        : *{$alat}*\n"
               . "Mulai       : *{$mulai}*\n"
               . "Jatuh Tempo : *{$jatuhTempo}*\n"
               . "Dikembalikan: *{$selesaiAt}*\n"
               . "Logistik    : *{$logistik}*\n"
               . "Pembayaran  : *{$metode}*\n"
               . "━━━━━━━━━━━━━━━━━━━━━\n"
               . $totalBaris
               . ($dendaInfo ? "━━━━━━━━━━━━━━━━━━━━━\n" . $dendaInfo : '')
               . "━━━━━━━━━━━━━━━━━━━━━\n\n"
               . "Semoga alat bermanfaat untuk karya Anda 📸\n"
               . "Sampai jumpa di pesanan berikutnya!\n\n"
               . "_LensHub Internal System_ 🙏";

        return self::kirim($rental->whatsapp, $pesan);
    }
}