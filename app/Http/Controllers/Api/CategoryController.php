<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Mendapatkan daftar semua kategori
     */
    public function index(Request $request)
    {
        $query = Category::withCount('gears');

        // Search berdasarkan name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar kategori berhasil diambil',
            'data'    => $categories
        ], 200);
    }

    /**
     * GET /api/categories/{id}
     * Mendapatkan detail kategori spesifik beserta gears nya
     */
    public function show($id)
    {
        $category = Category::with('gears')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail kategori berhasil diambil',
            'data'    => $category
        ], 200);
    }

    /**
     * POST /api/categories
     * Membuat kategori baru (admin only)
     */
    public function store(Request $request)
    {
        // Cek authorization
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized - hanya admin yang dapat membuat kategori'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|unique:categories,name',
            'prefix' => 'required|string|unique:categories,prefix|max:5',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $category = Category::create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil dibuat',
            'data'    => $category
        ], 201);
    }

    /**
     * PUT /api/categories/{id}
     * Update kategori (admin only)
     */
    public function update(Request $request, $id)
    {
        // Cek authorization
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'sometimes|string|unique:categories,name,' . $id,
            'prefix' => 'sometimes|string|unique:categories,prefix,' . $id . '|max:5',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $category->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil diupdate',
            'data'    => $category
        ], 200);
    }

    /**
     * DELETE /api/categories/{id}
     * Hapus kategori (admin only)
     */
    public function destroy($id)
    {
        // Cek authorization
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Cek apakah ada gear yang menggunakan kategori ini
        if ($category->gears()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak dapat menghapus kategori yang memiliki gear'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil dihapus'
        ], 200);
    }

    /**
     * GET /api/categories/{id}/gears
     * Mendapatkan semua gear dalam kategori
     */
    public function gears($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $gears = $category->gears()->latest()->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Gear dalam kategori berhasil diambil',
            'data'    => $gears
        ], 200);
    }
}
