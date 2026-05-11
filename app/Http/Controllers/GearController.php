<?php

/* Catatan untuk Pengembang:
Controller Gear ini adalah pusat pengelolaan alat di gudang. Berikut penjelasan fungsinya:
    1.  Index: 
        Menampilkan semua daftar barang yang ada. Di bagian View, fungsi ini bisa menerima filter kategori jika kamu mengirimkan parameter category_id melalui link atau dropdown pencarian.

    2.  Store: 
        Untuk add gear baru ke database. Fungsi ini secara otomatis membuatkan kode unik (seperti CAM_001) dan mengelola unggahan foto; pastikan form di View memiliki atribut enctype="multipart/form-data".

    3.  updateStatus: 
        Fitur aksi cepat untuk mengubah status operasional barang (contoh: dari Available ke Maintenance). Di View, kamu cukup membuat tombol yang mengarah ke URL route dengan menyertakan parameter status yang diinginkan.

    4.  updateCondition: 
        Memperbarui kondisi fisik barang (seperti 'baik' atau 'rusak') sekaligus mencatat riwayat perubahannya ke tabel GearConditionLog. Admin bisa menyertakan catatan tambahan melalui query string note di URL atau input form.

    5.  duplicate: 
        Mempermudah penambahan stok untuk barang yang tipenya sama. Fungsi ini akan menyalin data barang lama ke baris baru tetapi tetap memberikan nomor urut (unit_code) yang baru agar tetap unik.

    6.  destroy: 
        Menghapus data menggunakan sistem Soft Delete. Barang tidak akan hilang permanen dari database, hanya tidak muncul di daftar aktif, sehingga riwayat transaksi lama tetap aman dan tidak error.

    Pemanggilan di View: 
        Gunakan elemen <form> dengan @method('POST') atau @method('DELETE') untuk aksi yang mengubah data (store, destroy, duplicate) demi keamanan, sedangkan untuk update status/kondisi bisa menggunakan link <a> atau tombol yang diarahkan ke route spesifik.
*/


namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Category;
use App\Models\GearConditionLog;
use Illuminate\Http\Request;

class GearController extends Controller
{
    // Menampilkan daftar alat
    public function index(Request $request)
    {
        $categories = Category::all();
        
        // Pakai with('category') supaya loading data lebih cepat (Eager Loading)
        $gears = Gear::with('category')
            ->when($request->category_id, function($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->latest()
            ->get();

        return view('admin.gears.index', compact('gears', 'categories'));
    }

    // Simpan alat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string',
            'rent_price'  => 'required|numeric',
            'penalty_fee' => 'required|numeric',
            'photo'       => 'nullable|image|max:2048',
        ]);

        // Cari kategori untuk generate kode unik
        $category = Category::findOrFail($request->category_id);
        $validated['unit_code'] = $category->generateNewCode();

        // Urus foto jika ada
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('gears', 'public');
        }

        Gear::create($validated);

        return redirect()->route('gears.index')->with('success', 'Barang baru berhasil ditambah.');
    }

    // Update status (Tersedia/Disewa/Maintenance) - Tombol Cepat
    public function updateStatus(Gear $gear, $status)
    {
        $gear->update(['status' => $status]);

        return back()->with('success', "Status {$gear->unit_code} sekarang: $status.");
    }

    // Update kondisi fisik + Otomatis catat ke riwayat
    public function updateCondition(Request $request, Gear $gear, $condition)
    {
        // Simpan riwayat perubahan sebelum diupdate
        GearConditionLog::create([
            'gear_id'          => $gear->id,
            'condition_before' => $gear->condition_status,
            'condition_after'  => $condition,
            'note'             => $request->note ?? "Update manual admin"
        ]);

        $gear->update(['condition_status' => $condition]);

        return back()->with('success', "Kondisi {$gear->unit_code} berhasil diperbarui.");
    }

    // Duplikasi barang (Tambah stok barang yang sama dengan cepat)
    public function duplicate(Gear $gear)
    {
        $newGear = $gear->replicate();
        
        // Generate kode baru supaya tidak bentrok
        $newGear->unit_code = $gear->category->generateNewCode();
        $newGear->status    = 'available';
        $newGear->save();

        return back()->with('success', "Barang berhasil digandakan dengan kode: {$newGear->unit_code}");
    }

    // Hapus barang (Soft Delete)
    public function destroy(Gear $gear)
    {
        $gear->delete();
        return back()->with('success', 'Barang berhasil dihapus.');
    }
}