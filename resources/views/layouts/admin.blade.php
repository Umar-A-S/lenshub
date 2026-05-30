<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LensHub Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-gradient-to-b from-[#0b3aa9] to-[#b8c8eb]">

    <div class="flex h-screen">

        {{-- SIDEBAR --}}
        <aside class="flex h-screen w-[250px] shrink-0 flex-col bg-[#073090] text-white">

            {{-- LOGO --}}
            <div class="shrink-0 border-b border-white/10 p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500 text-xl">📷</div>
                    <div>
                        <h1 class="text-2xl font-bold">LensHub</h1>
                        <p class="text-sm text-blue-200">Internal System</p>
                    </div>
                </div>
            </div>

            {{-- MENU --}}
            <nav class="hide-scrollbar flex-1 space-y-6 overflow-y-auto px-4 py-6 text-sm">

                {{-- UTAMA --}}
                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Utama</p>
                    <a href="{{ route('dashboard') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                        Dashboard
                    </a>
                </div>

                {{-- OPERASIONAL --}}
                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Operasional</p>

                    <a href="{{ route('sewa') }}"
                        class="flex items-center justify-between rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('sewa*') ? 'bg-white/20' : '' }}">
                        <span>Manajemen Sewa</span>
                    </a>

                    <a href="{{ route('permintaan.index') }}"
                        class="flex items-center justify-between rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('permintaan.*') ? 'bg-white/20' : '' }}">
                        <span>Permintaan Transaksi</span>
                        @php $pendingCount = \App\Models\Rental::where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span id="admin-pending-count" class="pending-badge rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900">
                                {{ $pendingCount }}
                            </span>
                        @else
                            <span id="admin-pending-count" class="pending-badge hidden rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900"></span>
                        @endif
                    </a>

                    <a href="{{ route('transaksi.index') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('transaksi.*') ? 'bg-white/20' : '' }}">
                        Transaksi
                    </a>

                    <a href="{{ route('inventory.index') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('inventory.*') ? 'bg-white/20' : '' }}">
                        Inventory
                    </a>
                </div>

                {{-- ANALITIK --}}
                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Analitik</p>
                    <a href="{{ route('denda.index') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('denda.*') ? 'bg-white/20' : '' }}">
                        Denda & Penalti
                    </a>
                </div>

                {{-- OWNER ONLY: Klien, Laporan, Pengaturan --}}
                @if(auth()->user()?->role === 'owner')
                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Pelanggan</p>
                    <a href="{{ route('owner.klien') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('owner.klien*') ? 'bg-white/20' : '' }}">
                        Klien Database
                    </a>
                </div>

                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Analitik Owner</p>
                    <a href="{{ route('owner.laporan') }}"
                        class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('owner.laporan*') ? 'bg-white/20' : '' }}">
                        Laporan Keuangan
                    </a>
                </div>
                @endif

                {{-- SISTEM --}}
                <div>
                    <p class="mb-3 px-3 text-xs font-semibold uppercase text-blue-200">Sistem</p>

                    @if(auth()->user()?->role === 'owner')
                        <a href="{{ route('owner.pengaturan') }}"
                            class="block rounded-lg px-4 py-2 hover:bg-white/10 {{ request()->routeIs('owner.pengaturan*') ? 'bg-white/20' : '' }}">
                            Pengaturan
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="block rounded-lg px-4 py-2 hover:bg-white/10">Kembali Ke Web</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left rounded-lg px-4 py-2 hover:bg-white/10 text-red-300">
                            Logout
                        </button>
                    </form>
                </div>

            </nav>

            {{-- USER INFO --}}
            <div class="flex shrink-0 items-center gap-3 border-t border-white/10 p-5">
                <div class="h-12 w-12 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center">
                    @if(auth()->user()?->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-full h-full object-cover" alt="">
                    @else
                        <span class="text-gray-500 text-xl">👤</span>
                    @endif
                </div>
                <div>
                    <p class="font-semibold">{{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="text-xs text-blue-200 capitalize">{{ auth()->user()?->role ?? '' }}</p>
                </div>
            </div>

        </aside>

        {{-- CONTENT --}}
        <div class="hide-scrollbar h-screen flex-1 overflow-y-auto">

            {{-- NAVBAR --}}
            <header class="sticky top-0 z-50 rounded-bl-[2rem] border-b border-slate-200 bg-white/85 shadow-sm backdrop-blur-md">
                <div class="flex items-center justify-between px-8 py-6">
                    <div>
                        <h2 class="text-4xl font-extrabold text-slate-800">@yield('title', 'Dashboard')</h2>
                        <p class="mt-1 text-slate-600">@yield('subtitle', '')</p>
                    </div>
                </div>
            </header>

            {{-- PAGE --}}
            <main class="p-8">
                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')

    {{-- Global admin toast (used across pages) --}}
    <div id="admin-global-toast" class="fixed top-6 right-6 z-50 hidden max-w-sm rounded-lg bg-white p-4 shadow-lg">
        <p class="text-sm font-semibold">Permintaan Baru Masuk!</p>
        <p id="admin-global-toast-body" class="mt-1 text-xs text-slate-600"></p>
    </div>

    <script>
        // Global polling for admin pending count and toast notification
        window.__adminPendingCount = {{ $pendingCount ?? 0 }};
        window.__adminLastPendingKey = null;
        const POLL_ADMIN_URL = "{{ route('poll.admin') }}";

        async function globalPollAdmin() {
            try {
                const res = await fetch(POLL_ADMIN_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const data = await res.json();

                const badge = document.getElementById('admin-pending-count');
                if (badge) {
                    if (data.pending_count > 0) {
                        badge.textContent = data.pending_count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // show toast when count increases
                if (data.pending_count > (window.__adminPendingCount || 0)) {
                    if (data.recent_request) {
                        const key = data.recent_request.kode;
                        if (window.__adminLastPendingKey !== key) {
                            window.__adminLastPendingKey = key;
                            const body = `#${data.recent_request.kode} dari ${data.recent_request.nama} (${data.recent_request.time})`;
                            const toast = document.getElementById('admin-global-toast');
                            const toastBody = document.getElementById('admin-global-toast-body');
                            if (toast && toastBody) {
                                toastBody.textContent = body;
                                toast.classList.remove('hidden');
                                setTimeout(() => toast.classList.add('hidden'), 10000);
                            }
                        }
                    }
                }

                window.__adminPendingCount = data.pending_count;
            } catch (e) {
                /* silent */
            }
        }

        // start polling immediately and every 10s
        globalPollAdmin();
        setInterval(globalPollAdmin, 10000);
    </script>

</body>
</html>
