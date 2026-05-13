<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * GET /api/users
     * Mendapatkan daftar semua user (admin only)
     */
    public function index(Request $request)
    {
        // Cek authorization
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized - hanya admin yang dapat mengakses'
            ], 403);
        }

        $query = User::query();

        // Filter berdasarkan role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Search berdasarkan name atau email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar user berhasil diambil',
            'data'    => $users
        ], 200);
    }

    /**
     * GET /api/users/{id}
     * Mendapatkan detail user spesifik
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Cek authorization: user hanya bisa lihat data diri sendiri, admin bisa lihat semua
        if (auth()->user()->id !== $user->id && auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail user berhasil diambil',
            'data'    => $user
        ], 200);
    }

    /**
     * GET /api/users/me
     * Mendapatkan data user yang login
     */
    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak login'
            ], 401);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data user berhasil diambil',
            'data'    => $user
        ], 200);
    }

    /**
     * PUT /api/users/{id}
     * Update data user
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Cek authorization: user hanya bisa update data diri sendiri, admin bisa update semua
        if (auth()->user()->id !== $user->id && auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'role'  => 'sometimes|in:admin,owner,user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Hanya admin yang bisa mengubah role
        if ($request->has('role') && auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengubah role'
            ], 403);
        }

        $user->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'User berhasil diupdate',
            'data'    => $user
        ], 200);
    }

    /**
     * PATCH /api/users/{id}/password
     * Update password user
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Cek authorization
        if (auth()->user()->id !== $user->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Password saat ini tidak sesuai'
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diubah'
        ], 200);
    }

    /**
     * DELETE /api/users/{id}
     * Hapus user
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Hanya admin yang bisa delete user
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized - hanya admin yang dapat menghapus user'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    /**
     * GET /api/users/role/{role}
     * Mendapatkan user berdasarkan role
     */
    public function byRole($role)
    {
        // Validasi role
        if (!in_array($role, ['admin', 'owner', 'user'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Role tidak valid'
            ], 400);
        }

        $users = User::where('role', $role)->get();

        return response()->json([
            'status'  => 'success',
            'message' => "User dengan role $role berhasil diambil",
            'data'    => $users
        ], 200);
    }
}
