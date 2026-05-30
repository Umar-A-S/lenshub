<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    public function index()
    {
        $owner  = auth()->user();
        $admins = User::where('role', 'admin')->latest()->get();

        return view('owner.pengaturan', compact('owner', 'admins'));
    }

    // ─── Update profil Owner ─────────────────────────────────────────

    public function updateProfil(Request $request)
    {
        /** @var \App\Models\User $owner */
        $owner = auth()->user();

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($owner->id)],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($owner->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $update = [
            'username' => $data['username'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? $owner->phone,
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $owner->update($update);

        return back()->with('success_profil', 'Data profil owner berhasil diperbarui.');
    }

    // ─── Tambah Admin ────────────────────────────────────────────────

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'username' => $data['username'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        return back()->with('success_admin', 'Admin baru berhasil ditambahkan.');
    }

    // ─── Edit Admin ──────────────────────────────────────────────────

    public function updateAdmin(Request $request, User $user)
    {
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $update = [
            'username' => $data['username'],
            'email'    => $data['email'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return response()->json(['success' => true, 'message' => 'Data admin berhasil diperbarui.']);
    }

    // ─── Hapus Admin ─────────────────────────────────────────────────

    public function destroyAdmin(User $user)
    {
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Admin berhasil dihapus.']);
    }
}
