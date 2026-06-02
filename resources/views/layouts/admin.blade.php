<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LensHub Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Sidebar Responsif */
        .sidebar-overlay {
            @apply fixed inset-0 bg-black/50 hidden z-30 lg:hidden transition-opacity duration-300;
        }
        
        .sidebar-overlay.active {
            @apply block;
        }
        
        @media (max-width: 1024px) {
            aside.sidebar {
                @apply fixed left-0 top-0 h-screen w-60 transform transition-transform duration-300 ease-in-out -translate-x-full z-40 !flex;
            }
            
            aside.sidebar.active {
                @apply translate-x-0;
            }
            
            .main-content {
                @apply w-full;
            }
        }
        
        @media (min-width: 1025px) {
            aside {
                @apply relative translate-x-0;
            }
            
            .main-content {
                @apply flex-1;
            }
        }
        
        /* Smooth transitions for buttons */
        .btn-transition {
            @apply transition-all duration-200 ease-in-out;
        }
        
        /* Focus states */
        .btn-modern:focus {
            @apply ring-2 ring-offset-2 ring-blue-400;
        }
        
        /* Icon styling */
        .icon-lg {
            @apply text-lg;
        }
        
        /* Header background */
        header {
            @apply bg-gradient-to-r from-white via-blue-50 to-white;
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-gradient-to-br from-[#0b3aa9] to-[#b8c8eb]">

    {{-- Sidebar Overlay untuk Mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="flex h-screen">

        {{-- SIDEBAR --}}
        <aside class="sidebar hidden lg:flex h-screen w-60 shrink-0 flex-col bg-[var(--bg-sidebar)] text-white shadow-xl">

            {{-- LOGO --}}
            <div class="shrink-0 border-b border-white/10 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 shadow-md">
                        <i class="fas fa-camera text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight font-sans">LensHub</h1>
                        <p class="text-xs text-blue-100 font-sans">Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- MENU --}}
            <nav class="hide-scrollbar flex-1 space-y-4 overflow-y-auto px-3 py-4 text-sm font-sans">

                {{-- UTAMA --}}
                <div>
                    <p class="mb-3 px-2 text-[10px] font-bold uppercase text-blue-200 tracking-wider font-sans">Utama</p>
                    <a href="{{ route('dashboard') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-gauge-high w-4 mr-2"></i>Dashboard
                    </a>
                </div>

                {{-- OPERASIONAL --}}
                <div>
                    <p class="mb-3 px-2 text-[10px] font-bold uppercase text-blue-200 tracking-wider font-sans">Operasional</p>

                    <a href="{{ route('sewa') }}"
                        class="flex items-center justify-between btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('sewa*') ? 'bg-white/20' : '' }}">
                        <span><i class="fas fa-handshake w-4 mr-2"></i>Manajemen Sewa</span>
                    </a>

                    <a href="{{ route('permintaan.index') }}"
                        class="flex items-center justify-between btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('permintaan.*') ? 'bg-white/20' : '' }}">
                        <span><i class="fas fa-clipboard-list w-4 mr-2"></i>Permintaan</span>
                        @php $pendingCount = \App\Models\Rental::where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span id="admin-pending-count" class="pending-badge rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white animate-pulse">
                                {{ $pendingCount }}
                            </span>
                        @else
                            <span id="admin-pending-count" class="pending-badge hidden rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white"></span>
                        @endif
                    </a>

                    <a href="{{ route('transaksi.index') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('transaksi.*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-credit-card w-4 mr-2"></i>Transaksi
                    </a>

                    <a href="{{ route('inventory.index') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('inventory.*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-boxes-stacked w-4 mr-2"></i>Inventory
                    </a>
                </div>

                {{-- ANALITIK --}}
                <div>
                    <p class="mb-3 px-2 text-[10px] font-bold uppercase text-blue-200 tracking-wider">Analitik</p>
                    <a href="{{ route('denda.index') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('denda.*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-receipt w-4 mr-2"></i>Denda & Penalti
                    </a>
                </div>

                {{-- OWNER ONLY --}}
                @if(auth()->user()?->role === 'owner')
                <div>
                    <p class="mb-3 px-2 text-[10px] font-bold uppercase text-blue-200 tracking-wider">Owner</p>
                    <a href="{{ route('owner.klien') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('owner.klien*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-users w-4 mr-2"></i>Klien
                    </a>
                    <a href="{{ route('owner.laporan') }}"
                        class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('owner.laporan*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-chart-line w-4 mr-2"></i>Laporan
                    </a>
                </div>
                @endif

                {{-- SISTEM --}}
                <div>
                    <p class="mb-3 px-2 text-[10px] font-bold uppercase text-blue-200 tracking-wider">Sistem</p>

                    @if(auth()->user()?->role === 'owner')
                        <a href="{{ route('owner.pengaturan') }}"
                            class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25 {{ request()->routeIs('owner.pengaturan*') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-sliders w-4 mr-2"></i>Pengaturan
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="block btn-transition rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-white/15 active:bg-white/25">
                        <i class="fas fa-arrow-left w-4 mr-2"></i>Kembali Ke Web
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block btn-transition w-full text-left rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-red-600/20 active:bg-red-600/30 text-red-200">
                            <i class="fas fa-sign-out-alt w-4 mr-2"></i>Logout
                        </button>
                    </form>
                </div>

            </nav>

            {{-- USER INFO --}}
            <div class="flex shrink-0 items-center gap-3 border-t border-white/10 p-4">
                <div class="h-10 w-10 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-md">
                    @if(auth()->user()?->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-full h-full object-cover" alt="">
                    @else
                        <i class="fas fa-user text-gray-500"></i>
                    @endif
                </div>
                <div class="overflow-hidden">
                    <p class="font-semibold text-sm truncate">{{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="text-[11px] text-blue-200 capitalize truncate">{{ auth()->user()?->role ?? '' }}</p>
                </div>
            </div>

        </aside>

        {{-- CONTENT --}}
        <div class="main-content hide-scrollbar flex flex-col h-screen lg:overflow-y-auto flex-1 w-full">

            {{-- NAVBAR --}}
            <header class="sticky top-0 z-40 shadow-sm bg-[var(--bg-sidebar)] text-white lg:relative shadow-xl min-h-16 justify-between">
                <div class="flex items-center justify-between px-4 py-3 sm:px-6 sm:py-4">
                    {{-- Hamburger Button (Mobile Only) --}}
                    <button id="sidebarToggle" class="lg:hidden btn-modern text-white hover:bg-white/10 p-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex-1 lg:flex flex-col">
                        <h2 class="text-xl sm:text-2xl uppercase font-bold text-slate-800 text-white">@yield('title', 'Dashboard')</h2>
                        <p class="hidden sm:block text-sm text-slate-500 text-white-200">@yield('subtitle', '')</p>
                    </div>
                    
                </div>
            </header>

            {{-- PAGE --}}
            <main class="flex-1 w-full h-full overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')

    {{-- Global admin toast --}}
    <div id="admin-global-toast" class="fixed top-4 right-4 z-50 hidden max-w-xs rounded-md bg-white p-4 shadow-xl border border-slate-100">
        <div class="flex items-start gap-3">
            <i class="fas fa-bell text-blue-500 mt-1"></i>
            <div>
                <p class="text-xs font-semibold text-slate-800">Permintaan Baru!</p>
                <p id="admin-global-toast-body" class="mt-1 text-[12px] text-slate-600"></p>
            </div>
        </div>
    </div>

    <script>
        // Sidebar Toggle untuk Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('aside.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebar.classList.toggle('hidden');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebar.classList.add('hidden');
            overlay.classList.remove('active');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar when a link is clicked
        document.querySelectorAll('aside a, aside form button').forEach(element => {
            element.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });

        // Global polling script
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

                if (data.pending_count > (window.__adminPendingCount || 0)) {
                    if (data.recent_request) {
                        const key = data.recent_request.kode;
                        if (window.__adminLastPendingKey !== key) {
                            window.__adminLastPendingKey = key;
                            const body = `#${data.recent_request.kode} dari ${data.recent_request.nama}`;
                            const toast = document.getElementById('admin-global-toast');
                            const toastBody = document.getElementById('admin-global-toast-body');
                            if (toast && toastBody) {
                                toastBody.textContent = body;
                                toast.classList.remove('hidden');
    
                                setTimeout(() => toast.classList.add('hidden'), 5000);
                            }
                        }
                    }
                }

                window.__adminPendingCount = data.pending_count;
            } catch (e) {
                /* silent */
            }
        }

        globalPollAdmin();
        setInterval(globalPollAdmin, 10000);
    </script>

</body>
</html>
