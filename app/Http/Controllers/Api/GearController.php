<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gear;
use App\Models\Category;
use App\Models\GearConditionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GearController extends Controller
{
    /**
     * GET /api/gears
     * Mendapatkan daftar semua gear dengan filter optional
     */
    public function index(Request $request)
    {
        $query = Gear::with('category');

        // Filter berdasarkan category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kondisi
        if ($request->has('condition_status')) {
            $query->where('condition_status', $request->condition_status);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $gears = $query->latest()->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar gear berhasil diambil',
            'data'    => $gears
        ], 200);
    }

    /**
     * GET /api/gears/{id}
     * Mendapatkan detail gear spesifik
     */
    public function show($id)
    {
        $gear = Gear::with(['category', 'rentals'])->find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail gear berhasil diambil',
            'data'    => $gear
        ], 200);
    }

    /**
     * POST /api/gears
     * Membuat gear baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'rent_price'  => 'required|numeric|min:0',
            'penalty_fee' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Handle upload foto
        if ($request->hasFile('photo')) {
            $validated['image_path'] = $request->file('photo')->store('gears', 'local');
        }

        // Generate unit code otomatis
        $category = Category::find($request->category_id);
        $validated['unit_code'] = $category->generateNewCode();

        $gear = Gear::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Gear berhasil dibuat',
            'data'    => $gear
        ], 201);
    }

    /**
     * PUT /api/gears/{id}
     * Update gear
     */
    public function update(Request $request, $id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'name'        => 'sometimes|string|max:255',
            'rent_price'  => 'sometimes|numeric|min:0',
            'penalty_fee' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Handle update foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($gear->image_path && Storage::disk('local')->exists($gear->image_path)) {
                Storage::disk('local')->delete($gear->image_path);
            }
            $validated['image_path'] = $request->file('photo')->store('gears', 'local');
        }

        $gear->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Gear berhasil diupdate',
            'data'    => $gear
        ], 200);
    }

    /**
     * DELETE /api/gears/{id}
     * Hapus gear (soft delete)
     */
    public function destroy($id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $gear->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Gear berhasil dihapus'
        ], 200);
    }

    /**
     * PATCH /api/gears/{id}/status
     * Update status gear (available/rented/maintenance)
     */
    public function updateStatus(Request $request, $id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,rented,maintenance'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $gear->update(['status' => $request->status]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status gear berhasil diupdate',
            'data'    => $gear
        ], 200);
    }

    /**
     * PATCH /api/gears/{id}/condition
     * Update kondisi gear dan catat riwayatnya
     */
    public function updateCondition(Request $request, $id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'condition' => 'required|in:baik,rusak,hilang',
            'notes'     => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $oldCondition = $gear->condition_status;
        $newCondition = $request->condition;

        // Update gear
        $gear->condition_status = $newCondition;
        $gear->status = ($newCondition === 'baik') ? 'available' : 'maintenance';
        $gear->save();

        // Catat ke condition log
        GearConditionLog::create([
            'gear_id'      => $gear->id,
            'old_condition' => $oldCondition,
            'new_condition' => $newCondition,
            'notes'        => $request->notes ?? null,
            'changed_by'   => auth()->id()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kondisi gear berhasil diupdate',
            'data'    => $gear
        ], 200);
    }

    /**
     * POST /api/gears/{id}/duplicate
     * Duplikasi gear (tambah stok yang sama)
     */
    public function duplicate($id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $newGear = $gear->replicate();
        $newGear->unit_code = $gear->category->generateNewCode();
        $newGear->status = 'available';
        $newGear->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Gear berhasil diduplikasi',
            'data'    => $newGear
        ], 201);
    }

    /**
     * GET /api/gears/{id}/condition-history
     * Mendapatkan riwayat perubahan kondisi gear
     */
    public function conditionHistory($id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak ditemukan'
            ], 404);
        }

        $history = GearConditionLog::where('gear_id', $id)
            ->with('changedBy')
            ->latest()
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Riwayat kondisi gear berhasil diambil',
            'data'    => $history
        ], 200);
    }
}