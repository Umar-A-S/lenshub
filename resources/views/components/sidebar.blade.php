<aside class="w-[220px] bg-[#1E2A5E] text-white flex flex-col h-screen fixed top-0 left-0">
    <div class="p-6 flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-[#FF6B2B] flex items-center justify-center">
            <span class="text-white font-bold">L</span>
        </div>
        <div>
            <h1 class="font-bold text-lg">LensHub</h1>
            <p class="text-[0.6rem] text-gray-300">Internal System</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-2 space-y-6">
        <div>
            <h3 class="text-[0.6rem] uppercase text-gray-400 mb-2">Utama</h3>
            <x-sidebar-link href="/dashboard" :active="request()->is('dashboard')">Dashboard</x-sidebar-link>
        </div>
        <div>
            <h3 class="text-[0.6rem] uppercase text-gray-400 mb-2">Operasional</h3>
            <div class="space-y-1">
                <x-sidebar-link href="/sewa" :active="request()->is('sewa*')">Manajemen Sewa</x-sidebar-link>
                <x-sidebar-link href="/transaksi" :active="request()->is('transaksi*')">Transaksi</x-sidebar-link>
                <x-sidebar-link href="/inventory" :active="request()->is('inventory*')">Inventory</x-sidebar-link>
            </div>
        </div>
        <div>
            <h3 class="text-[0.6rem] uppercase text-gray-400 mb-2">Pelanggan</h3>
            <x-sidebar-link href="/klien" :active="request()->is('klien*')">Klien Database</x-sidebar-link>
        </div>
        <div>
            <h3 class="text-[0.6rem] uppercase text-gray-400 mb-2">Analitik & Sistem</h3>
            <div class="space-y-1">
                <x-sidebar-link href="/laporan" :active="request()->is('laporan*')">Laporan Keuangan</x-sidebar-link>
                <x-sidebar-link href="/denda" :active="request()->is('denda*')">Denda & Penalti</x-sidebar-link>
            </div>
        </div>
    </nav>

    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-500"></div>
            <div>
                <p class="text-sm font-semibold">Anggeline</p>
                <p class="text-[0.65rem] text-gray-400">Owner – Full Access</p>
            </div>
        </div>
    </div>
</aside>
