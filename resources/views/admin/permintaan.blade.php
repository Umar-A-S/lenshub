@extends('layouts.admin')

@section('title', 'Permintaan Pesanan')
@section('subtitle', 'Hari ini, ' . now()->translatedFormat('d F Y'))

@section('content')
    <div class="p-8">

        {{-- SEARCH --}}
        <form action="{{ route('permintaan.index') }}" method="GET" class="mb-8 flex items-center gap-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama penyewa..."
                class="h-[58px] flex-1 rounded-2xl bg-white px-8 shadow-sm outline-none">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                class="h-[58px] w-[220px] rounded-2xl bg-white px-5 shadow-sm">
            <button type="submit" class="h-[58px] rounded-2xl bg-[#073090] px-8 text-white">Cari</button>
            <a href="{{ route('permintaan.index') }}"
                class="flex h-[58px] items-center rounded-2xl bg-gray-300 px-8">Reset</a>
        </form>

        {{-- TABEL PERMINTAAN --}}
        <div class="rounded-[32px] bg-white p-8 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-slate-800">PERMINTAAN PESANAN</h3>
                {{-- Badge live + count --}}
                <div class="flex items-center gap-2">
                    <span id="live-badge"
                        class="hidden items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700">
                        <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                        Live
                    </span>
                    <span class="text-sm text-slate-500">
                        <span id="total-pending" class="font-bold text-slate-800">{{ $permintaan->count() }}</span>
                        permintaan
                    </span>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-100">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-4 text-left">Kode</th>
                            <th class="px-5 py-4 text-left">Nama Penyewa</th>
                            <th class="px-5 py-4 text-left">No. WA</th>
                            <th class="px-5 py-4 text-left">Alat</th>
                            <th class="px-5 py-4 text-left">Durasi</th>
                            <th class="px-5 py-4 text-left">Mulai</th>
                            <th class="px-5 py-4 text-left">Logistik & Lokasi</th>
                            <th class="px-5 py-4 text-left">Total</th>
                            <th class="px-5 py-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    {{-- id="tabel-permintaan" — target inject realtime --}}
                    <tbody id="tabel-permintaan">
                        @include('admin.permintaan_rows', ['permintaan' => $permintaan])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI --}}
    <div id="modalKonfirmasi" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="mx-4 max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-8 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-800">Proses Konfirmasi Sewa</h3>
                <button onclick="closeKonfirmasi()"
                    class="text-2xl leading-none text-slate-400 hover:text-slate-600">×</button>
            </div>

            <div class="mb-6 rounded-2xl bg-slate-50 p-4">
                <p class="font-bold text-slate-900" id="konfNama"></p>
                <p class="mt-1 text-sm text-slate-500">Alat: <span id="konfAlat" class="font-medium text-slate-700"></span>
                </p>
                <p class="text-sm text-slate-500">Total: <span class="font-bold text-blue-700">Rp <span
                            id="konfTotal"></span></span></p>
            </div>

            <form id="formKonfirmasi" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Jaminan Fisik yang Ditahan <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-slate-400">(pilih satu atau lebih)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @php
                            $jaminanOptions = [
                                'ktp' => ['label' => 'KTP Asli', 'icon' => ''],
                                'sim' => ['label' => 'SIM', 'icon' => ''],
                                'ktm' => ['label' => 'KTM (Mhs)', 'icon' => ''],
                                'deposit' => ['label' => 'Uang Deposit', 'icon' => ''],
                                'kartu_pelajar' => ['label' => 'Kartu Pelajar', 'icon' => ''],
                                'paspor' => ['label' => 'Paspor', 'icon' => ''],
                                'npwp' => ['label' => 'NPWP', 'icon' => ''],
                                'bpkb' => ['label' => 'BPKB', 'icon' => ''],
                                'stnk' => ['label' => 'STNK', 'icon' => ''],
                                'lainnya' => ['label' => 'Lainnya...', 'icon' => ''],
                            ];
                        @endphp
                        @foreach ($jaminanOptions as $val => $opt)
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 transition hover:bg-slate-50 has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                                <input type="checkbox" name="jaminan_fisik[]" value="{{ $val }}"
                                    class="rounded text-blue-600 focus:ring-blue-400"
                                    {{ $val === 'lainnya' ? 'onchange="toggleLainnya(this)"' : '' }}>
                                <span class="text-sm">{{ $opt['icon'] }} {{ $opt['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div id="lainnyaField" class="mt-2 hidden">
                        <input type="text" name="jaminan_lainnya" placeholder="Sebutkan jaminan lainnya..."
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <p id="jaminanError" class="mt-1 hidden text-xs text-red-500">Pilih minimal satu jaminan.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Metode Pembayaran <span
                            class="text-red-500">*</span></label>
                    <select name="metode_bayar" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">-- Pilih Metode --</option>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Catatan Kondisi Alat</label>
                    <textarea name="catatan_kondisi" rows="3" placeholder="Contoh: Kondisi baik, semua aksesoris lengkap..."
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeKonfirmasi()"
                        class="flex-1 rounded-2xl border border-slate-200 py-3 text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-2xl bg-[#073090] py-3 font-semibold text-white transition hover:bg-blue-800">
                        ✅ Aktifkan Transaksi & Serah Terima
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TOAST NOTIF REALTIME (Admin) --}}
    <div id="admin-toast"
        class="fixed bottom-6 right-6 z-50 hidden max-w-sm rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
        <div class="flex items-start gap-3">
            <span class="text-2xl">🔔</span>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-900">Permintaan Baru Masuk!</p>
                <p class="mt-0.5 text-xs text-slate-500" id="admin-toast-body"></p>
            </div>
            <button onclick="document.getElementById('admin-toast').classList.add('hidden')"
                class="text-lg text-slate-300 hover:text-slate-500">×</button>
        </div>
        <a href="{{ route('permintaan.index') }}" onclick="document.getElementById('admin-toast').classList.add('hidden')"
            class="mt-3 block text-center text-xs font-medium text-blue-600 hover:underline">
            Lihat sekarang →
        </a>
    </div>

    <script>
        let activeRentalId = null;

        function toggleLainnya(cb) {
            document.getElementById('lainnyaField').classList.toggle('hidden', !cb.checked);
        }

        function openKonfirmasi(id, nama, alat, total) {
            activeRentalId = id;
            document.getElementById('konfNama').textContent = nama;
            document.getElementById('konfAlat').textContent = alat;
            document.getElementById('konfTotal').textContent = total;
            document.getElementById('formKonfirmasi').action = '/permintaan/' + id + '/konfirmasi';

            document.querySelectorAll('input[name="jaminan_fisik[]"]').forEach(cb => cb.checked = false);
            document.getElementById('lainnyaField').classList.add('hidden');
            document.getElementById('jaminanError').classList.add('hidden');

            document.getElementById('modalKonfirmasi').classList.remove('hidden');
            document.getElementById('modalKonfirmasi').classList.add('flex');
        }

        function closeKonfirmasi() {
            document.getElementById('modalKonfirmasi').classList.add('hidden');
            document.getElementById('modalKonfirmasi').classList.remove('flex');
        }

        document.getElementById('formKonfirmasi').addEventListener('submit', function(e) {
            e.preventDefault();

            const checked = document.querySelectorAll('input[name="jaminan_fisik[]"]:checked');
            if (checked.length === 0) {
                document.getElementById('jaminanError').classList.remove('hidden');
                return;
            }
            document.getElementById('jaminanError').classList.add('hidden');

            const form = this;
            const data = new FormData(form);

            // Disable submit buttons to prevent double-submit
            const submitButtons = form.querySelectorAll('button[type="submit"], button[data-submit]');
            submitButtons.forEach(b => {
                b.disabled = true;
                b.dataset.origText = b.textContent;
                b.textContent = 'Memproses...';
            });

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        closeKonfirmasi();
                        // Refresh tabel tanpa reload penuh
                        refreshTable();
                    } else {
                        alert('Gagal memproses konfirmasi.');
                    }
                })
                .catch(() => alert('Terjadi kesalahan, coba lagi.'))
                .finally(() => {
                    // Re-enable buttons (only if modal still open)
                    submitButtons.forEach(b => {
                        b.disabled = false;
                        b.textContent = b.dataset.origText || b.textContent;
                    });
                });
        });

        function tolakPermintaan(id) {
            if (!confirm(
                    'Yakin ingin menolak permintaan ini?\nPesanan akan tersimpan di riwayat user dengan status "Ditolak".'))
                return;
            fetch('/permintaan/' + id + '/tolak', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        // Refresh tabel tanpa reload penuh
                        refreshTable();
                    }
                });
        }

        // ─── REALTIME: inject ulang isi tbody tanpa full-page reload ───
        const PARTIAL_URL = "{{ route('permintaan.partial') }}";
        const POLL_URL = "{{ route('poll.admin') }}";
        const POLL_INTERVAL = 10000; // 10 detik

        let lastCount = {{ $permintaan->count() }};
        let lastKey = null;

        async function refreshTable() {
            try {
                const res = await fetch(PARTIAL_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();

                // Inject rows ke tbody
                document.getElementById('tabel-permintaan').innerHTML = data.html;

                // Update counter
                document.getElementById('total-pending').textContent = data.count;

                // Update sidebar badge kalau ada
                const badge = document.getElementById('admin-pending-count');
                if (badge) {
                    badge.textContent = data.count;
                    badge.classList.toggle('hidden', data.count <= 0);
                }

                lastCount = data.count;

                // Tampilkan indikator live sebentar
                const liveBadge = document.getElementById('live-badge');
                liveBadge.classList.remove('hidden');
                liveBadge.classList.add('flex');
                clearTimeout(liveBadge._timer);
                liveBadge._timer = setTimeout(() => {
                    liveBadge.classList.add('hidden');
                    liveBadge.classList.remove('flex');
                }, 2000);

            } catch (_) {
                /* silent */ }
        }

        async function pollAdmin() {
            try {
                const res = await fetch(POLL_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                if (data.pending_count > lastCount) {
                    // Ada permintaan baru → refresh tabel & tampilkan toast
                    refreshTable();
                    if (data.recent_request) {
                        const key = data.recent_request.kode;
                        if (lastKey !== key) {
                            lastKey = key;
                            const body =
                                `#${data.recent_request.kode} dari ${data.recent_request.nama} (${data.recent_request.time})`;
                            document.getElementById('admin-toast-body').textContent = body;
                            document.getElementById('admin-toast').classList.remove('hidden');
                            setTimeout(() => document.getElementById('admin-toast').classList.add('hidden'), 10000);
                        }
                    }
                }
            } catch (_) {
                /* silent */ }
        }

        // Jalankan polling
        setInterval(() => {
            pollAdmin();
            refreshTable(); // refresh tabel setiap interval meski tidak ada perubahan
        }, POLL_INTERVAL);
    </script>
@endsection
