@extends('layouts.admin')

@section('title', 'Denda & Penalti')
@section('subtitle', 'Hari ini, ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="space-y-10">

    {{-- CARD RINGKASAN --}}
    <div class="grid grid-cols-4 gap-6">
        <div class="rounded-[30px] bg-white p-7 shadow-sm">
            <p class="text-slate-500">Aktif Terlambat</p>
            <h2 class="mt-4 text-5xl font-bold">{{ $aktifTerlambat }}</h2>
        </div>

        <div class="rounded-[30px] bg-white p-7 shadow-sm">
            <p class="text-slate-500">Denda Berjalan</p>
            <h2 class="mt-4 text-4xl font-bold">Rp {{ number_format($dendaBerjalan, 0, ',', '.') }}</h2>
        </div>

        <div class="rounded-[30px] bg-white p-7 shadow-sm">
            <p class="text-slate-500">Total Denda Bulan Ini</p>
            <h2 class="mt-4 text-4xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h2>
        </div>

        <div class="rounded-[30px] bg-white p-7 shadow-sm">
            <p class="text-slate-500">Total Denda Dilunasi</p>
            <h2 class="mt-4 text-5xl font-bold">{{ $dendaLunas }}</h2>
        </div>
    </div>

    {{-- DENDA AKTIF --}}
    <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-red-600">Denda Aktif Saat Ini</h2>
                <p class="mt-1 text-sm text-slate-500">Baris di bawah ini adalah denda yang statusnya masih <span class="font-semibold">belum lunas</span>.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Penyewa</th>
                        <th class="px-5 py-4 text-left">Alat</th>
                        <th class="px-5 py-4 text-left">Total Denda</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aktif as $fine)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-blue-700">{{ $fine->rental?->kode_sewa ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $fine->rental?->nama_penyewa ?? $fine->rental?->client?->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $fine->rental?->whatsapp ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $fine->rental?->items?->first()?->equipment?->nama ?? '-' }}</td>
                            <td class="px-5 py-4 text-red-600 font-semibold">Rp {{ number_format($fine->total_denda, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Belum Lunas</span>
                            </td>
                            <td class="px-5 py-4">
                                <button type="button"
                                    onclick='openLunasModal({{ $fine->id }}, @json($fine->rental?->nama_penyewa ?? $fine->rental?->client?->nama ?? '-'), {{ $fine->total_denda }})'
                                    class="rounded-full bg-green-100 px-5 py-2 text-green-700 hover:bg-green-200 transition">
                                        Tandai Lunas
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                                <p class="text-4xl mb-3">✅</p>
                                <p>Tidak ada denda aktif</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <div class="mb-6">
            <h2 class="text-3xl font-bold">Riwayat Denda Bulan Ini</h2>
            <p class="mt-1 text-sm text-slate-500">Data ini menampilkan denda yang sudah lunas, lengkap dengan metode bayar dan waktu pelunasan.</p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">Tanggal Bayar</th>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Penyewa</th>
                        <th class="px-5 py-4 text-left">Alat</th>
                        <th class="px-5 py-4 text-left">Total Denda</th>
                        <th class="px-5 py-4 text-left">Metode</th>
                        <th class="px-5 py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $fine)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-5 py-4">{{ $fine->dibayar_pada ? \Carbon\Carbon::parse($fine->dibayar_pada)->format('d M Y H:i') : ($fine->updated_at?->format('d M Y H:i') ?? '-') }}</td>
                            <td class="px-5 py-4 font-semibold text-blue-700">{{ $fine->rental?->kode_sewa ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $fine->rental?->nama_penyewa ?? $fine->rental?->client?->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $fine->rental?->whatsapp ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $fine->rental?->items?->first()?->equipment?->nama ?? '-' }}</td>
                            <td class="px-5 py-4 text-red-600 font-semibold">Rp {{ number_format($fine->total_denda, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 uppercase">{{ $fine->metode_bayar_denda ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Lunas</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                                <p class="text-4xl mb-3">📂</p>
                                <p>Belum ada riwayat denda bulan ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- MODAL LUNASI DENDA --}}
<div id="modalLunasDenda" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl mx-4">
        <h2 class="mb-1 text-2xl font-bold text-slate-900">Konfirmasi Pelunasan</h2>
        <p class="mb-5 text-sm text-slate-500">Pastikan denda sudah diterima sebelum status diubah.</p>

        <div class="mb-5 rounded-2xl bg-green-50 px-5 py-4">
            <p class="text-sm">Penyewa: <span class="font-bold" id="mdNama"></span></p>
            <p class="text-sm mt-1">Total Denda: <span class="font-bold text-red-600">Rp <span id="mdDenda"></span></span></p>
        </div>

        <form id="formLunasDenda" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block mb-1 text-sm font-medium text-slate-700">Metode Pembayaran</label>
                <select name="metode_bayar_denda" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
                    <option value="tunai">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-[#073090] py-3 text-white font-semibold hover:bg-blue-800 transition">
                ✅ Konfirmasi Selesai Transaksi
            </button>
            <button type="button" onclick="closeLunasModal()" class="w-full rounded-2xl border border-slate-200 py-3 text-slate-600 hover:bg-slate-50 transition">
                Batal
            </button>
        </form>
    </div>
</div>

<style>
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}
</style>

<script>
// Disable mouse wheel changing number inputs
document.querySelectorAll('input[type=number]').forEach(el => {
    el.addEventListener('wheel', function(e) { e.preventDefault(); }, { passive: false });
});

function openLunasModal(id, nama, denda) {
    document.getElementById('mdNama').textContent = nama;
    document.getElementById('mdDenda').textContent = new Intl.NumberFormat('id-ID').format(denda);
    document.getElementById('formLunasDenda').action = '/denda/' + id + '/lunas';
    const modal = document.getElementById('modalLunasDenda');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeLunasModal() {
    const modal = document.getElementById('modalLunasDenda');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('formLunasDenda').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    const data = new FormData(form);

    // Disable submit to avoid double submit
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.dataset.origText = submitBtn.textContent; submitBtn.textContent = 'Memproses...'; }

    fetch(form.action, {
        method: 'POST',
        cache: 'no-store',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (res.processed === false) {
                alert('Denda sudah diproses sebelumnya.');
            }
            closeLunasModal();
            window.location.reload();
        }
    })
    .catch(() => alert('Terjadi kesalahan saat melunasi denda.'))
    .finally(() => {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.origText || submitBtn.textContent; }
    });
});
</script>

@endsection
