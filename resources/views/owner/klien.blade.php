@extends('layouts.admin')

@section('title', 'Klien Database')
@section('subtitle', 'Data seluruh pengguna terdaftar')

@section('content')

{{-- SUMMARY CARDS (2 saja) --}}
<div class="grid grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-sm flex items-center gap-4">
        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">👥</div>
        <div>
            <p class="text-sm text-slate-500">Total Klien</p>
            <h3 class="text-3xl font-bold">{{ $totalKlien }}</h3>
        </div>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-sm flex items-center gap-4">
        <div class="h-14 w-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">✅</div>
        <div>
            <p class="text-sm text-slate-500">Aktif Bulan Ini</p>
            <h3 class="text-3xl font-bold">{{ $aktifBulanIni }}</h3>
        </div>
    </div>
</div>

{{-- FILTER + SEARCH --}}
<div class="bg-white rounded-3xl p-6 shadow-sm mb-6">
    <form method="GET" action="{{ route('owner.klien') }}" class="flex gap-3 items-center">
        <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, email, atau nomor HP..."
                   class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
        </div>
        <select name="status"
                class="px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="px-6 py-3 bg-blue-700 text-white rounded-2xl text-sm font-medium hover:bg-blue-800 transition">
            Cari
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('owner.klien') }}" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-2xl text-sm hover:bg-slate-200 transition">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-3xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold">Database Klien</h3>
        <span class="text-sm text-slate-500">{{ $users->count() }} pengguna ditemukan</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-slate-500 border-b">
                <tr>
                    <th class="text-left py-3 px-2">ID</th>
                    <th class="text-left py-3 px-2">Pengguna</th>
                    <th class="text-left py-3 px-2">No. HP</th>
                    <th class="text-left py-3 px-2">Email</th>
                    <th class="text-left py-3 px-2">Terdaftar</th>
                    <th class="text-left py-3 px-2">Status</th>
                    <th class="text-left py-3 px-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b hover:bg-slate-50 transition">
                        <td class="py-4 px-2 text-slate-400 font-mono text-xs">#{{ $user->id }}</td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden text-sm font-bold text-blue-700 shrink-0">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/'.$user->photo) }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->username ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2 text-slate-600">{{ $user->phone ?? '-' }}</td>
                        <td class="py-4 px-2 text-slate-600">{{ $user->email }}</td>
                        <td class="py-4 px-2 text-slate-500">{{ $user->created_at?->format('d M Y') }}</td>
                        <td class="py-4 px-2">
                            @if(($user->status ?? 'aktif') === 'aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex gap-2">
                                {{-- Toggle Aktif/Nonaktif --}}
                                <button onclick="toggleStatus({{ $user->id }}, '{{ $user->status ?? 'aktif' }}')"
                                        id="btn-toggle-{{ $user->id }}"
                                        class="px-3 py-1.5 rounded-xl text-xs font-medium transition
                                               {{ ($user->status ?? 'aktif') === 'aktif'
                                                  ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                                  : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                        title="{{ ($user->status ?? 'aktif') === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    {{ ($user->status ?? 'aktif') === 'aktif' ? '🔒 Nonaktifkan' : '🔓 Aktifkan' }}
                                </button>

                                {{-- Delete --}}
                                <button onclick="hapusUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        class="px-3 py-1.5 rounded-xl text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition"
                                        title="Hapus pengguna">
                                    🗑️ Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="text-4xl mb-3">👥</div>
                            <p>Belum ada data klien</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- TOAST --}}
<div id="toast"
     class="fixed bottom-6 right-6 z-50 hidden px-6 py-3 rounded-2xl shadow-xl text-white text-sm font-medium transition-all duration-300">
</div>

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `fixed bottom-6 right-6 z-50 px-6 py-3 rounded-2xl shadow-xl text-white text-sm font-medium transition-all duration-300
        ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    t.style.opacity = '1';
    setTimeout(() => { t.className += ' opacity-0'; setTimeout(() => t.classList.add('hidden'), 400); }, 3000);
}

async function toggleStatus(userId, currentStatus) {
    const btn = document.getElementById(`btn-toggle-${userId}`);
    btn.disabled = true;
    btn.textContent = '⏳';

    try {
        const res = await fetch(`/owner/klien/${userId}/toggle-status`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            // Reload halaman untuk update tampilan
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('Gagal mengubah status', 'error');
            btn.disabled = false;
        }
    } catch(e) {
        showToast('Terjadi kesalahan', 'error');
        btn.disabled = false;
    }
}

async function hapusUser(userId, nama) {
    if (!confirm(`Yakin ingin menghapus pengguna "${nama}"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;

    try {
        const res = await fetch(`/owner/klien/${userId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.error ?? 'Gagal menghapus', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan', 'error');
    }
}
</script>
@endpush

@endsection
