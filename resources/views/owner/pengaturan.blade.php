@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('subtitle', 'Kelola data owner & manajemen admin')

@section('content')

@if(session('success_profil'))
<div class="mb-6 rounded-2xl bg-green-50 border border-green-200 px-5 py-3 text-sm text-green-700 flex items-center gap-2">
    ✅ {{ session('success_profil') }}
</div>
@endif
@if(session('success_admin'))
<div class="mb-6 rounded-2xl bg-green-50 border border-green-200 px-5 py-3 text-sm text-green-700 flex items-center gap-2">
    ✅ {{ session('success_admin') }}
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SECTION 1: MANAGE DATA OWNER                                  --}}
{{-- ══════════════════════════════════════════════════════════════ --}}

<div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center text-xl">👤</div>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manage Data Owner</h2>
            <p class="text-sm text-slate-500">Perbarui informasi akun owner</p>
        </div>
    </div>

    <form method="POST" action="{{ route('owner.pengaturan.profil') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" value="{{ old('username', $owner->username) }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                           @error('username') border-red-400 @enderror"
                    placeholder="username_owner">
                @error('username')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-400">Username owner dapat diubah bebas tanpa batas.</p>
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $owner->name) }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                           @error('name') border-red-400 @enderror"
                    placeholder="Nama lengkap">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $owner->email) }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                           @error('email') border-red-400 @enderror"
                    placeholder="owner@email.com">
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-400">Digunakan untuk login.</p>
            </div>

            {{-- Nomor --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="08xxxxxxxxxx">
                <p class="mt-1 text-xs text-slate-400">Nomor WA digunakan untuk keperluan login.</p>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                <input type="password" name="password"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                           @error('password') border-red-400 @enderror"
                    placeholder="Kosongkan jika tidak ingin mengubah">
                @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Ulangi password baru">
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                class="rounded-xl bg-[#073090] px-8 py-3 text-sm font-semibold text-white hover:bg-blue-800 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SECTION 2: MANAGE ADMIN                                       --}}
{{-- ══════════════════════════════════════════════════════════════ --}}

<div class="bg-white rounded-3xl shadow-sm p-8" x-data="adminManager()">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-purple-100 flex items-center justify-center text-xl">🛡️</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Manage Role Admin</h2>
                <p class="text-sm text-slate-500">Kelola akun admin yang memiliki akses panel</p>
            </div>
        </div>
        <button @click="showAdd = true"
            class="flex items-center gap-2 rounded-xl bg-[#073090] px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 transition">
            + Tambah Admin
        </button>
    </div>

    {{-- Tabel admin --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-slate-500 text-xs uppercase tracking-wider">
                    <th class="text-left py-3 px-3">Username</th>
                    <th class="text-left py-3 px-3">Email</th>
                    <th class="text-left py-3 px-3">Dibuat</th>
                    <th class="text-right py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr class="border-b hover:bg-slate-50" id="admin-row-{{ $admin->id }}">
                    <td class="py-3 px-3 font-medium text-slate-900">{{ $admin->username }}</td>
                    <td class="py-3 px-3 text-slate-600">{{ $admin->email }}</td>
                    <td class="py-3 px-3 text-slate-400">{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="py-3 px-3 text-right">
                        <button @click="editAdmin({{ $admin->id }}, '{{ $admin->username }}', '{{ $admin->email }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition mr-1">
                            ✏️ Edit
                        </button>
                        <button @click="deleteAdmin({{ $admin->id }}, '{{ $admin->username }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition">
                            🗑️ Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-slate-400">
                        <p class="text-4xl mb-2">🛡️</p>
                        <p>Belum ada admin. Tambah admin pertama.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Modal Tambah Admin ── --}}
    <div x-show="showAdd" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8" @click.outside="showAdd = false">
            <h3 class="text-xl font-bold text-slate-900 mb-6">➕ Tambah Admin Baru</h3>

            <form method="POST" action="{{ route('owner.pengaturan.admin.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="username_admin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Nama lengkap admin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="admin@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Min. 8 karakter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Ulangi password">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAdd = false"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-[#073090] px-4 py-3 text-sm font-semibold text-white hover:bg-blue-800 transition">
                        Tambah Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Edit Admin ── --}}
    <div x-show="showEdit" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8" @click.outside="showEdit = false">
            <h3 class="text-xl font-bold text-slate-900 mb-6">✏️ Edit Admin</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" x-model="editData.username"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="username_admin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" x-model="editData.email"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="admin@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" x-model="editData.password"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                </div>

                <div x-show="editError" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600" x-text="editError"></div>
                <div x-show="editSuccess" class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-600" x-text="editSuccess"></div>

                <div class="flex gap-3 pt-2">
                    <button @click="showEdit = false"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button @click="submitEdit()" :disabled="editLoading"
                        class="flex-1 rounded-xl bg-[#073090] px-4 py-3 text-sm font-semibold text-white hover:bg-blue-800 transition disabled:opacity-60">
                        <span x-text="editLoading ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>[x-cloak]{display:none!important}</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function adminManager() {
    return {
        showAdd: {{ $errors->has('username') || $errors->has('email') || $errors->has('name') ? 'true' : 'false' }},
        showEdit: false,
        editLoading: false,
        editError: '',
        editSuccess: '',
        editData: { id: null, username: '', email: '', password: '' },

        editAdmin(id, username, email) {
            this.editData = { id, username, email, password: '' };
            this.editError = '';
            this.editSuccess = '';
            this.showEdit = true;
        },

        async submitEdit() {
            if (!this.editData.username || !this.editData.email) {
                this.editError = 'Username dan email wajib diisi.';
                return;
            }
            this.editLoading = true;
            this.editError = '';
            this.editSuccess = '';

            try {
                const form = new FormData();
                form.append('_method', 'PUT');
                form.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
                form.append('username', this.editData.username);
                form.append('email', this.editData.email);
                if (this.editData.password) form.append('password', this.editData.password);

                const res  = await fetch(`/owner/pengaturan/admin/${this.editData.id}`, { method: 'POST', body: form });
                const data = await res.json();

                if (data.success) {
                    this.editSuccess = data.message;
                    setTimeout(() => { this.showEdit = false; window.location.reload(); }, 800);
                } else {
                    this.editError = data.message || 'Gagal memperbarui data.';
                }
            } catch (e) {
                this.editError = 'Terjadi kesalahan. Coba lagi.';
            } finally {
                this.editLoading = false;
            }
        },

        async deleteAdmin(id, username) {
            if (!confirm(`Hapus admin "${username}"? Tindakan ini tidak dapat dibatalkan.`)) return;

            try {
                const res  = await fetch(`/owner/pengaturan/admin/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();

                if (data.success) {
                    const row = document.getElementById(`admin-row-${id}`);
                    if (row) row.remove();
                } else {
                    alert(data.error || 'Gagal menghapus admin.');
                }
            } catch (e) {
                alert('Terjadi kesalahan. Coba lagi.');
            }
        },
    };
}
</script>

@endsection
