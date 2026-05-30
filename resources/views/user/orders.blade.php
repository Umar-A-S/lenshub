@extends('layouts.user')

@section('content')

<div class="space-y-6" x-data="{ tab: 'berlangsung' }">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Pesanan Saya</h1>
        <p class="text-gray-500 mt-1">Pantau status sewa kamera dan peralatanmu di sini.</p>
    </div>

    {{-- TAB FILTER --}}
    <div class="flex gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-slate-100">
        @php
            $tabs = [
                'berlangsung' => ['label' => 'Sedang Berlangsung', 'count' => $aktif->count() + $pending->count() + $proses->count()],
                'history'     => ['label' => 'Riwayat',            'count' => $history->count()],
            ];
        @endphp
        @foreach($tabs as $key => $t)
            <button
                @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-[#073090] text-white shadow' : 'text-slate-500 hover:bg-slate-50'"
                class="flex-1 rounded-xl px-3 py-2.5 text-sm font-medium transition flex items-center justify-center gap-1.5">
                {{ $t['label'] }}
                {{-- badge count: diupdate realtime oleh JS --}}
                <span id="badge-{{ $key }}"
                    :class="tab === '{{ $key }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                    class="rounded-full px-2 py-0.5 text-xs font-bold {{ $t['count'] > 0 ? '' : 'hidden' }}">{{ $t['count'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- ====== TAB: SEDANG BERLANGSUNG ====== --}}
    <div x-show="tab === 'berlangsung'" x-cloak>
        {{-- id untuk target inject realtime --}}
        <div id="section-berlangsung" class="space-y-6">
            @include('user._orders_berlangsung', ['aktif' => $aktif, 'pending' => $pending, 'proses' => $proses])
        </div>
    </div>

    {{-- ====== TAB: RIWAYAT ====== --}}
    <div x-show="tab === 'history'" x-cloak>
        <div id="section-history" class="space-y-4">
            @include('user._orders_history', ['history' => $history])
        </div>
    </div>

</div>

{{-- TOAST NOTIF REALTIME --}}
<div id="notif-toast"
     class="fixed bottom-6 right-6 z-50 hidden max-w-sm rounded-2xl bg-white border border-slate-200 shadow-xl p-4 transition-all duration-300">
    <div class="flex items-start gap-3">
        <span class="text-2xl" id="notif-icon">🔔</span>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-900 text-sm" id="notif-title">Update Pesanan</p>
            <p class="text-xs text-slate-500 mt-0.5" id="notif-body"></p>
        </div>
        <button onclick="document.getElementById('notif-toast').classList.add('hidden')"
            class="text-slate-300 hover:text-slate-500 text-lg leading-none">×</button>
    </div>
</div>

{{-- Indikator sedang update --}}
<div id="live-indicator"
     class="fixed top-4 right-4 z-40 hidden items-center gap-1.5 bg-green-50 border border-green-200 text-green-700 text-xs font-medium px-3 py-1.5 rounded-full shadow-sm">
    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
    Live
</div>

<style>[x-cloak]{display:none!important}</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
(function () {
    /**
     * ─── REALTIME ORDERS (polling + partial HTML injection) ───
     *
     * Cara kerja:
     * 1. Setiap POLL_INTERVAL ms, fetch /akun/pesanan/partial
     * 2. Server render ulang HTML section 'berlangsung' & 'history'
     * 3. JS inject HTML ke dalam div#section-berlangsung / div#section-history
     * 4. Badge count pada tab diperbarui
     * 5. Kalau ada perubahan status, tampilkan toast notifikasi
     */

    const PARTIAL_URL  = "{{ route('akun.pesanan.partial') }}";
    const POLL_INTERVAL = 10000; // 10 detik

    const statusLabel = {
        aktif:               { text: 'Pesananmu sudah dikonfirmasi & aktif!', icon: '✅' },
        menunggu_pelunasan:  { text: 'Ada denda yang perlu dilunasi.',        icon: '⚠️' },
        selesai:             { text: 'Pesananmu sudah selesai.',              icon: '🎉' },
        ditolak:             { text: 'Pesananmu ditolak oleh admin.',         icon: '❌' },
    };

    let lastBerlangsungCount = {{ $aktif->count() + $pending->count() + $proses->count() }};
    let lastHistoryCount     = {{ $history->count() }};
    let lastChangeKey        = null;

    function showToast(title, body, icon) {
        document.getElementById('notif-icon').textContent  = icon  || '🔔';
        document.getElementById('notif-title').textContent = title;
        document.getElementById('notif-body').textContent  = body;
        const toast = document.getElementById('notif-toast');
        toast.classList.remove('hidden');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.add('hidden'), 10000);
    }

    function updateBadge(tabKey, count) {
        const badge = document.getElementById('badge-' + tabKey);
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function showLiveIndicator() {
        const el = document.getElementById('live-indicator');
        el.classList.remove('hidden');
        el.classList.add('flex');
        clearTimeout(el._timer);
        el._timer = setTimeout(() => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }, 2000);
    }

    async function refreshOrders() {
        try {
            const res = await fetch(PARTIAL_URL, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!res.ok) return;

            const data = await res.json();

            // ── Inject HTML berlangsung ──
            const secBerlangsung = document.getElementById('section-berlangsung');
            if (secBerlangsung && data.html_berlangsung !== undefined) {
                secBerlangsung.innerHTML = data.html_berlangsung;
            }

            // ── Inject HTML history ──
            const secHistory = document.getElementById('section-history');
            if (secHistory && data.html_history !== undefined) {
                secHistory.innerHTML = data.html_history;
            }

            // ── Update badge count ──
            updateBadge('berlangsung', data.berlangsung_count);
            updateBadge('history', data.history_count);

            // ── Toast kalau ada perubahan jumlah ──
            const berlangsungChanged = data.berlangsung_count !== lastBerlangsungCount;
            const historyChanged     = data.history_count     !== lastHistoryCount;

            if (historyChanged && data.history_count > lastHistoryCount) {
                // Kemungkinan ada pesanan baru masuk ke history (selesai/ditolak)
                showToast('Riwayat Diperbarui', 'Ada pesanan yang pindah ke riwayat.', '📦');
            } else if (berlangsungChanged) {
                if (data.berlangsung_count > lastBerlangsungCount) {
                    showToast('Update Pesanan', 'Status pesananmu berubah.', '🔔');
                }
            }

            lastBerlangsungCount = data.berlangsung_count;
            lastHistoryCount     = data.history_count;

            showLiveIndicator();

        } catch (e) {
            // Network error, diam saja
        }
    }

    // ── Poll status untuk toast per kode pesanan ──
    const POLL_STATUS_URL = "{{ route('akun.poll.status') }}";

    async function pollStatus() {
        try {
            const res  = await fetch(POLL_STATUS_URL, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.recent_change) {
                const key = data.recent_change.kode + '_' + data.recent_change.status;
                if (lastChangeKey !== key) {
                    lastChangeKey = key;
                    const s = statusLabel[data.recent_change.status] ?? { text: 'Status pesanan berubah.', icon: '🔔' };
                    showToast(
                        'Update #' + data.recent_change.kode,
                        s.text + ' • ' + data.recent_change.time,
                        s.icon
                    );
                }
            }
        } catch (e) { /* silent */ }
    }

    // Jalankan pertama kali setelah 5 detik (beri waktu halaman render),
    // lalu setiap POLL_INTERVAL ms.
    setTimeout(() => {
        refreshOrders();
        pollStatus();
        setInterval(refreshOrders, POLL_INTERVAL);
        setInterval(pollStatus, POLL_INTERVAL);
    }, 5000);
})();
</script>
@endsection
