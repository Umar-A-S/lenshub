<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        $data = $this->buildData((int) $bulan, (int) $tahun);

        // ─── Handle Export ────────────────────────────────────────
        if ($request->filled('export')) {
            return match ($request->input('export')) {
                'pdf'   => $this->exportPdf($data, $bulan, $tahun),
                'excel' => $this->exportExcel($data, $bulan, $tahun),
                default => view('owner.laporan', $data),
            };
        }

        return view('owner.laporan', $data);
    }

    // ─── Build shared data ────────────────────────────────────────

    private function buildData(int $bulan, int $tahun): array
    {
        $carbonBulan = Carbon::createFromDate($tahun, $bulan, 1);

        $totalPendapatan = Rental::where('status', 'selesai')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->sum('total');

        $totalTransaksi = Rental::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->whereNotIn('status', ['pending', 'ditolak'])
            ->count();

        $klienAktif = User::where('role', 'user')
            ->where('status', 'aktif')
            ->count();

        $totalDenda = Fine::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'lunas')
            ->sum('total_denda');

        $pendapatanPerKategori = Category::with(['equipments.rentalItems' => function ($q) use ($bulan, $tahun) {
            $q->whereHas('rental', function ($r) use ($bulan, $tahun) {
                $r->where('status', 'selesai')
                  ->whereMonth('updated_at', $bulan)
                  ->whereYear('updated_at', $tahun);
            });
        }])->get()->map(function ($cat) {
            $total = $cat->equipments->flatMap->rentalItems->sum('harga');
            return ['nama' => $cat->nama, 'total' => $total];
        })->sortByDesc('total')->values();

        $mingguData   = [];
        $startOfMonth = $carbonBulan->copy()->startOfMonth();
        $endOfMonth   = $carbonBulan->copy()->endOfMonth();

        $weekNum = 1;
        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $weekEnd = $current->copy()->endOfWeek(Carbon::SUNDAY);
            if ($weekEnd->gt($endOfMonth)) $weekEnd = $endOfMonth->copy();

            $pendapatan = Rental::where('status', 'selesai')
                ->whereBetween('updated_at', [$current, $weekEnd])
                ->sum('total');

            $denda = Fine::where('status', 'lunas')
                ->whereBetween('updated_at', [$current, $weekEnd])
                ->sum('total_denda');

            $transaksi = Rental::whereBetween('created_at', [$current, $weekEnd])
                ->whereNotIn('status', ['pending', 'ditolak'])
                ->count();

            $gross     = $pendapatan + $denda;
            $biayaOp   = $gross * 0.15;
            $netProfit = $gross - $biayaOp;

            $mingguData[] = [
                'label'      => "Minggu {$weekNum} ({$current->format('d')}-{$weekEnd->format('d M')})",
                'transaksi'  => $transaksi,
                'pendapatan' => $pendapatan,
                'denda'      => $denda,
                'gross'      => $gross,
                'biaya_op'   => $biayaOp,
                'net_profit' => $netProfit,
            ];

            $current = $weekEnd->copy()->addDay()->startOfDay();
            $weekNum++;
        }

        $totalGross   = collect($mingguData)->sum('gross');
        $totalBiayaOp = collect($mingguData)->sum('biaya_op');
        $totalNet     = collect($mingguData)->sum('net_profit');

        $daftarBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $daftarBulan[$m] = Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F Y');
        }

        return compact(
            'totalPendapatan', 'totalTransaksi', 'klienAktif', 'totalDenda',
            'pendapatanPerKategori', 'mingguData',
            'totalGross', 'totalBiayaOp', 'totalNet',
            'bulan', 'tahun', 'carbonBulan', 'daftarBulan'
        );
    }

    // ─── Export Excel (CSV) ───────────────────────────────────────

    private function exportExcel(array $data, int $bulan, int $tahun): \Illuminate\Http\Response
    {
        $carbonBulan = $data['carbonBulan'];
        $label       = $carbonBulan->translatedFormat('F_Y');
        $filename    = "laporan_keuangan_{$label}.csv";

        $rows   = [];
        $rows[] = ['Laporan Keuangan LensHub — ' . $carbonBulan->translatedFormat('F Y')];
        $rows[] = ['Diekspor pada', now()->format('d M Y H:i')];
        $rows[] = [];
        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Pendapatan', 'Rp ' . number_format($data['totalPendapatan'], 0, ',', '.')];
        $rows[] = ['Total Transaksi', $data['totalTransaksi']];
        $rows[] = ['Total Denda (Lunas)', 'Rp ' . number_format($data['totalDenda'], 0, ',', '.')];
        $rows[] = ['Klien Aktif', $data['klienAktif']];
        $rows[] = ['Grand Total Gross', 'Rp ' . number_format($data['totalGross'], 0, ',', '.')];
        $rows[] = ['Est. Biaya Operasional (15%)', 'Rp ' . number_format($data['totalBiayaOp'], 0, ',', '.')];
        $rows[] = ['Net Profit', 'Rp ' . number_format($data['totalNet'], 0, ',', '.')];
        $rows[] = [];
        $rows[] = ['RINGKASAN PER MINGGU'];
        $rows[] = ['Periode', 'Transaksi', 'Pendapatan Sewa', 'Denda', 'Total Gross', 'Biaya Op (15%)', 'Net Profit'];

        foreach ($data['mingguData'] as $mg) {
            $rows[] = [
                $mg['label'],
                $mg['transaksi'],
                'Rp ' . number_format($mg['pendapatan'], 0, ',', '.'),
                'Rp ' . number_format($mg['denda'], 0, ',', '.'),
                'Rp ' . number_format($mg['gross'], 0, ',', '.'),
                'Rp ' . number_format($mg['biaya_op'], 0, ',', '.'),
                'Rp ' . number_format($mg['net_profit'], 0, ',', '.'),
            ];
        }

        $rows[] = [];
        $rows[] = ['PENDAPATAN PER KATEGORI'];
        $rows[] = ['Kategori', 'Total Pendapatan'];
        foreach ($data['pendapatanPerKategori'] as $kat) {
            $rows[] = [$kat['nama'], 'Rp ' . number_format($kat['total'], 0, ',', '.')];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(';', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ])->withHeaders(['Content-Type' => 'text/csv; charset=UTF-8'])
          ->withCookie(cookie()->forever('laporan_exported', '1'));
    }

    // ─── Export PDF (print view) ──────────────────────────────────

    private function exportPdf(array $data, int $bulan, int $tahun): \Illuminate\Http\Response
    {
        $html = view('owner.laporan-pdf', $data)->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
