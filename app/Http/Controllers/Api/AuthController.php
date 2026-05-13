<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Register user baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => 'required|in:admin,owner,user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'User berhasil terdaftar',
                'data'    => [
                    'user'  => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/auth/login
     * Login dan dapatkan API token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Hapus token lama jika ada
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'user'  => $user,
                'token' => $token
            ]
        ], 200);
    }

    /**
     * POST /api/auth/logout
     * Logout dan revoke token
     */
    public function logout(Request $request)
    {
        // Revoke token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * POST /api/auth/logout-all
     * Logout dari semua device dengan menghapus semua token
     */
    public function logoutAll(Request $request)
    {
        // Revoke semua token
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout dari semua device berhasil'
        ], 200);
    }

    /**
     * GET /api/auth/me
     * Mendapatkan data user yang login
     */
    public function me(Request $request)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Data user berhasil diambil',
            'data'    => $request->user()
        ], 200);
    }

    /**
     * POST /api/auth/refresh-token
     * Refresh API token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        // Hapus token lama
        $user->currentAccessToken()->delete();

        // Buat token baru
        $newToken = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Token berhasil diperbarui',
            'data'    => [
                'token' => $newToken
            ]
        ], 200);
    }

    /**
     * POST /api/auth/verify-token
     * Verify jika token masih valid
     */
    public function verifyToken(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Token valid',
                'data'    => $request->user()
            ], 200);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Token tidak valid'
        ], 401);
    }
}
