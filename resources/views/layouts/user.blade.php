<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensHub Member</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen overflow-hidden">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="sticky top-0 h-screen w-[260px] shrink-0 bg-white border-r border-gray-200 flex flex-col overflow-hidden">

            {{-- PROFIL USER --}}
            <div class="p-6 border-b border-gray-200 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                            class="w-full h-full object-cover" alt="Foto Profil">
                    @else
                        <span class="text-gray-400 text-2xl">👤</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 truncate">
                        {{ auth()->user()->username ?? auth()->user()->name }}
                    </p>
                    <a href="{{ route('akun.profil') }}" class="text-sm text-gray-400 hover:text-blue-700 transition">
                        Ubah Profil
                    </a>
                </div>
            </div>

            {{-- MENU --}}
            <nav class="hide-scrollbar flex-1 space-y-1 overflow-y-auto p-4">
                <p class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Akun Saya
                </p>

                <a href="{{ route('akun.profil') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm
                        {{ request()->routeIs('akun.profil') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span>👤</span> Profil
                </a>

                <a href="{{ route('akun.pesanan') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm
                        {{ request()->routeIs('akun.pesanan') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="flex items-center gap-3"><span>📦</span> Pesanan</span>
                    @php
                        $pendingUser = \App\Models\Rental::where('user_id', auth()->id())
                            ->whereIn('status', ['pending','aktif','menunggu_pelunasan'])
                            ->count();
                    @endphp
                    @if($pendingUser > 0)
                        <span id="user-pending-count" class="rounded-full bg-blue-600 text-white text-xs px-2 py-0.5 font-bold">
                            {{ $pendingUser }}
                        </span>
                    @else
                        <span id="user-pending-count" class="hidden rounded-full bg-blue-600 text-white text-xs px-2 py-0.5 font-bold"></span>
                    @endif
                </a>

                <a href="{{ route('akun.notifikasi') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm
                        {{ request()->routeIs('akun.notifikasi') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span>🔔</span> Notifikasi
                </a>
            </nav>

            {{-- BAGIAN BAWAH SIDEBAR --}}
            <div class="p-4 border-t border-gray-100 space-y-1">

                {{-- KEMBALI KE WEB --}}
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition">
                    <span>🌐</span> Kembali ke Web
                </a>

                {{-- LOGOUT --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-500 hover:bg-red-50 transition">
                        <span>🚪</span> Logout
                    </button>
                </form>
            </div>

        </aside>

        {{-- KONTEN --}}
        <main class="hide-scrollbar flex-1 min-h-0 overflow-y-auto p-8">
            @yield('content')
        </main>

    </div>


<script>
(function () {
    if (window.__userSidebarPollingStarted) return;
    window.__userSidebarPollingStarted = true;

    const URL = "{{ route('akun.poll.status') }}";
    const INTERVAL = 5000;

    async function refreshUserBadge() {
        try {
            const res = await fetch(URL, {
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const badge = document.getElementById('user-pending-count');
            if (!badge) return;

            const count = Number(data.pending ?? 0) + Number(data.aktif ?? 0) + Number(data.menunggu ?? 0);
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.textContent = '';
                badge.classList.add('hidden');
            }
        } catch (e) {
            // silent
        }
    }

    refreshUserBadge();
    setInterval(refreshUserBadge, INTERVAL);
})();
</script>

</body>
</html>
