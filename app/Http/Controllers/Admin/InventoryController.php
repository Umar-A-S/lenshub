<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'rentalItems.rental'])->latest();

        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->search));

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('LOWER(deskripsi) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereHas('category', function ($cat) use ($keyword) {
                        $cat->whereRaw('LOWER(nama) LIKE ?', ['%' . $keyword . '%']);
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $equipments = $query->get()->map(function ($item) {
            $disewaCount = $item->rentalItems
                ->filter(function ($rentalItem) {
                    return in_array($rentalItem->rental?->status, ['aktif', 'terlambat']);
                })
                ->sum('qty');

            $stokTersedia = max($item->stok - $disewaCount, 0);

            $item->disewa_count = $disewaCount;
            $item->stok_tersedia = $stokTersedia;
            $item->status_stok = $stokTersedia > 0 ? 'tersedia' : 'tidak tersedia';

            return $item;
        });

        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $equipments = $equipments->where('status_stok', 'tersedia')->values();
            } elseif ($request->status === 'tidak tersedia') {
                $equipments = $equipments->where('status_stok', 'tidak tersedia')->values();
            }
        }

        $categories = Category::orderBy('nama', 'asc')->get();

        return view('admin.inventory', compact('equipments', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama', 'asc')->get();

        return view('admin.inventory-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'nama'         => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'stok'         => 'required|integer|min:0',
            'harga_harian' => 'required|numeric|min:0',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('equipments', 'public');
        }

        Equipment::create($data);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit(Equipment $equipment)
    {
        $categories = Category::orderBy('nama', 'asc')->get();

        return view('admin.inventory-edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'nama'         => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'stok'         => 'required|integer|min:0',
            'harga_harian' => 'required|numeric|min:0',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($equipment->foto) {
                Storage::disk('public')->delete($equipment->foto);
            }

            $data['foto'] = $request->file('foto')->store('equipments', 'public');
        }

        $equipment->update($data);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->foto) {
            Storage::disk('public')->delete($equipment->foto);
        }

        $equipment->ForceDelete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Alat berhasil dihapus.');
    }
}