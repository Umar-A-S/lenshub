<x-main-layout>
    <div class="space-y-6">
        <h1 class="text-[var(--fs-h1)] font-bold">Dashboard</h1>

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['title' => 'Pendapatan Hari Ini', 'value' => 'Rp 6.000.000', 'icon' => '💰'],
                    ['title' => 'Sewa Aktif', 'value' => '10', 'icon' => '📷'],
                    ['title' => 'Denda Terkumpul', 'value' => 'Rp 500.000', 'icon' => '⚠️'],
                    ['title' => 'Stok Tersedia', 'value' => '35 / 100', 'icon' => '📦'],
                ];
            @endphp

            @foreach($cards as $card)
            <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-5 shadow-sm border border-[var(--border-default)]">
                <p class="text-[var(--fs-small)] text-[var(--text-secondary)] uppercase">{{ $card['title'] }}</p>
                <h3 class="text-xl font-bold mt-2 font-mono-numbers">{{ $card['value'] }}</h3>
            </div>
            @endforeach
        </div>

        {{-- AKTIVITAS TERKINI --}}
        <div class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 border border-[var(--border-default)] shadow-sm">
            <h2 class="text-[var(--fs-h2)] font-bold mb-5">Aktivitas Sewa</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-[var(--fs-body)] text-left">
                    <thead class="text-[var(--text-muted)] border-b border-[var(--border-default)]">
                        <tr>
                            <th class="py-3 font-semibold">Klien</th>
                            <th class="py-3 font-semibold">Alat</th>
                            <th class="py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-default)]">
                        @for ($i = 1; $i <= 5; $i++)
                            <tr class="hover:bg-[#F9FAFB] transition-colors">
                                <td class="py-4 font-medium">Budi Santoso</td>
                                <td class="text-[var(--text-secondary)]">Sony A7 IV</td>
                                <td class="py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-[var(--border-radius-badge)] text-[var(--fs-small)] font-bold uppercase bg-[#DCFCE7] text-[#16A34A]">Aktif</span>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-main-layout>
