<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-b from-[#0b3aa9] to-[#b8c8eb]">

    <header class="sticky top-0 z-50 rounded-b-[2rem] bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-10 py-6">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="flex flex-col">
                <span class="text-2xl font-bold text-slate-900">LensHub</span>
                <span class="text-sm text-slate-500">Photography & Video Gear</span>
            </a>

            {{-- NAV LINKS --}}
            <nav class="flex items-center gap-10 text-slate-900">
                <a href="{{ route('home') }}" class="hover:text-blue-700">Home</a>
                <a href="{{ route('produk.index') }}" class="hover:text-blue-700">Produk</a>
                <a href="{{ route('rules') }}" class="hover:text-blue-700">Rules</a>
            </nav>

            {{-- POJOK KANAN: LOGIN / PROFIL --}}
            <div class="relative" x-data="{ open: false }">

                @auth
                    {{-- Sudah login: tampilkan avatar + dropdown --}}
                    <button @click="open = !open"
                        class="flex items-center gap-2 rounded-full border border-gray-200 px-3 py-2 transition hover:bg-gray-50">
                        {{-- Avatar --}}
                        <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-blue-100">
                            @if (auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                    class="h-full w-full object-cover" alt="">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>

                        {{-- Nama --}}
                        <span class="text-sm font-medium text-slate-700">
                            {{ auth()->user()->username ?? auth()->user()->name }}
                        </span>

                        {{-- Chevron --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transition"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown menu --}}
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 rounded-2xl border border-gray-100 bg-white py-2 text-sm shadow-lg">
                        @if (auth()->user()->role === 'owner')
                            <a href="{{ route('dashboard') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                🏠 Owner Dashboard
                            </a>
                        @endif

                        @if (in_array(auth()->user()->role, ['owner', 'admin']))
                            <a href="{{ route('dashboard') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                ⚙️ Admin Panel
                            </a>
                        @endif

                        @if (auth()->user()->role === 'user')
                            <a href="{{ route('akun.profil') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                👤 Profil Saya
                            </a>
                            <a href="{{ route('akun.pesanan') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                📦 Pesanan
                            </a>
                        @endif

                        <div class="my-1 border-t border-gray-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-red-500 hover:bg-red-50">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Belum login: tampilkan tombol Login --}}
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-2 rounded-full bg-[#073090] px-5 py-2 text-sm font-medium text-white transition hover:bg-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                                clip-rule="evenodd" />
                        </svg>
                        Login
                    </a>
                @endauth

            </div>

        </div>
    </header>

    {{-- Notifikasi akun nonaktif --}}
    @if (session('error_nonaktif'))
        <div id="notif-nonaktif"
            class="animate-fade-in fixed left-1/2 top-6 z-[999] mx-4 flex w-full max-w-md -translate-x-1/2 items-start gap-3 rounded-2xl bg-red-600 px-6 py-4 text-white shadow-2xl">
            <span class="shrink-0 text-2xl">🔒</span>
            <div class="flex-1">
                <p class="text-sm font-semibold leading-snug">{{ session('error_nonaktif') }}</p>
            </div>
            <button onclick="document.getElementById('notif-nonaktif').remove()"
                class="shrink-0 text-lg leading-none text-white/70 hover:text-white">✕</button>
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-10 py-10">
        @yield('content')
    </main>

    {{-- Alpine.js untuk dropdown (jika belum ada di app.js) --}}
    @if (!app()->environment('production'))
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

</body>

</html>
