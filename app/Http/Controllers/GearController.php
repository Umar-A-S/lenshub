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

    /**
     * Menyimpan unit barang baru ke database.
     * Logika unit_code otomatis dijalankan di Model Gear (booted method).
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'rent_price'  => 'required|numeric',
            'penalty_fee' => 'required|numeric',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        // Logika upload foto jika admin menyertakan gambar
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('gears', 'public');
        }

        Gear::create($data);

        return redirect()->route('gears.index')->with('success', 'Unit baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui informasi dasar unit barang.
     */
    public function update(Request $request, Gear $gear)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'rent_price'  => 'required|numeric',
            'penalty_fee' => 'required|numeric',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('gears', 'public');
        }

        $gear->update($data);

        return redirect()->route('gears.index')->with('success', 'Informasi unit berhasil diperbarui.');
    }

    /**
     * Menghapus unit barang secara halus (Soft Delete).
     * Data tetap ada di database tapi tidak muncul di query reguler.
     */
    public function destroy(Gear $gear)
    {
        $gear->delete(); // Ini otomatis memicu SoftDeletes trait

        return back()->with('success', "Unit {$gear->unit_code} berhasil dipindahkan ke tempat sampah.");
    }

    /**
     * Fitur duplikasi cepat: membuat unit baru berdasarkan data unit yang sudah ada.
     * Berguna saat admin menambah stok barang yang sama (misal: tambah 5 unit lensa).
     */
    public function duplicate(Gear $gear)
    {
        $newUnit = $gear->replicate(); // Salin semua data kecuali ID dan timestamps
        $newUnit->unit_code = null; // Set null agar memicu pembuatan kode otomatis baru (CAM_002, dst)
        $newUnit->status = 'available'; // Reset status ke tersedia
        $newUnit->save();

        return back()->with('success', "Berhasil menggandakan unit {$gear->name}. Kode unit baru: {$newUnit->unit_code}");
    }
}