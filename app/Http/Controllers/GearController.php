<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\GearConditionLog;
use Illuminate\Http\Request;

/**
 * GearController
 * Mengelola logika inventaris barang dengan sistem aksi cepat (button-driven).
 * Fitur utama:
 * 1. Update status operasional (available, booked, rented, maintenance) dengan satu klik.
 * 2. Update kondisi fisik (baik, rusak, hilang, maintenance) dengan catatan otomatis ke log riwayat kondisi.
 * 3. Validasi input untuk memastikan status dan kondisi yang dimasukkan sesuai dengan opsi yang tersedia.
 * 4. Integrasi dengan model GearConditionLog untuk mencatat setiap perubahan kondisi secara otomatis, sehingga admin tidak perlu input manual.
 */
class GearController extends Controller
{
    /**
     * Memperbarui status operasional barang (Tersedia, Maintenance, dll).
     */
    public function updateStatus(Gear $gear, $status)
    {
        $validStatuses = ['available', 'booked', 'rented', 'maintenance'];

        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Status tidak valid.');
        }

        $gear->update(['status' => $status]);

        return back()->with('success', "Status unit {$gear->unit_code} berhasil diubah menjadi $status.");
    }

    /**
     * Memperbarui kondisi fisik barang dan mencatatnya ke history log.
     */
    public function updateCondition(Request $request, Gear $gear, $condition)
    {
        $validConditions = ['baik', 'rusak', 'service', 'sensor kotor'];

        if (!in_array($condition, $validConditions)) {
            return back()->with('error', 'Kondisi tidak valid.');
        }

        // Simpan kondisi lama sebelum diupdate untuk kebutuhan log
        $oldCondition = $gear->condition_status;

        // Update kondisi di tabel gears
        $gear->update(['condition_status' => $condition]);

        // Buat catatan log riwayat kondisi secara otomatis
        GearConditionLog::create([
            'gear_id' => $gear->id,
            'condition_before' => $oldCondition,
            'condition_after' => $condition,
            'note' => $request->query('note') ?? "Perubahan kondisi manual oleh admin."
        ]);

        return back()->with('success', "Kondisi unit {$gear->unit_code} diperbarui dan tercatat di riwayat.");
    }
}