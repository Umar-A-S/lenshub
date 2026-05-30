@extends('layouts.public')

@section('content')
    <div class="mx-auto mb-6 max-w-4xl">
        <a href="{{ route('produk.show', $equipment) }}"
            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 font-medium text-slate-900 shadow transition hover:bg-slate-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Buat Pesanan
        </a>
    </div>

    <div class="mx-auto max-w-4xl">
        {{-- FOTO --}}
        <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow">

            <div class="overflow-hidden rounded-2xl border border-slate-200 p-2">

                @if (!empty($equipment->foto) && file_exists(public_path('storage/' . $equipment->foto)))
                    <img src="{{ asset('storage/' . $equipment->foto) }}" alt="{{ $equipment->nama }}"
                        class="h-[360px] w-full rounded-2xl bg-slate-50 object-contain">
                @else
                    <div class="flex h-[360px] items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                        foto

                    </div>
                @endif

            </div>

        </div>

        {{-- FORM --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow" x-data="sewaForm({{ $equipment->harga_harian }})">

            <h2 class="mb-1 text-2xl font-bold text-slate-900">{{ $equipment->nama }}</h2>
            <p class="mb-1 text-sm text-slate-400">{{ $equipment->category?->nama }}</p>
            <p class="mb-5 text-sm font-semibold text-blue-700">
                Rp {{ number_format($equipment->harga_harian, 0, ',', '.') }} / hari
            </p>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 px-5 py-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('produk.sewa.store', $equipment) }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nama produk (readonly) --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nama Produk <span class="text-red-500">*</span></label> </span>
                    <input type="text" value="{{ $equipment->nama }}" readonly
                        class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-500">
                </div>

                {{-- Nama penyewa - prefill dari user login --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Nama Lengkap Penyewa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_penyewa" value="{{ old('nama_penyewa', auth()->user()->name) }}"
                        required placeholder="Masukkan nama lengkap"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                {{-- No WA - prefill dari user login --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        No. WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', auth()->user()->phone ?? '') }}"
                        required placeholder="08xxxxxxxxxx"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                {{-- Tanggal & Jam --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Pinjam <span
                                class="text-red-500">*</span></label>
                        <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                            required min="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Jam Pinjam <span class="text-red-500">*</span>
                        </label>

                        <input id="jam_mulai" type="text" name="jam_mulai" value="{{ old('jam_mulai', '00:00') }}"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-lg font-medium shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                {{-- Durasi --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Durasi Sewa</label>
                    <p class="mb-2 text-xs text-slate-400">Harga berbeda-beda tergantung durasi</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['12jam' => '12 Jam', '1hari' => '1 Hari', '3hari' => '3 Hari', '5hari' => '5 Hari', '7hari' => '7 Hari'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="durasi" value="{{ $val }}" x-model="durasi"
                                    class="sr-only" {{ old('durasi', '1hari') === $val ? 'checked' : '' }}>
                                <span
                                    :class="durasi === '{{ $val }}' ? 'bg-[#073090] text-white' :
                                        'bg-slate-100 text-slate-700'"
                                    class="block rounded-xl px-4 py-2 text-sm font-medium transition">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Logistik --}}
                <div x-data="{ logistik: '{{ old('logistik', 'ambil') }}' }">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Alamat
                        <span class="ml-2 inline-flex overflow-hidden rounded-full border border-slate-200 text-xs">
                            <button type="button" @click="logistik='ambil'"
                                :class="logistik === 'ambil' ? 'bg-[#073090] text-white' : 'bg-white text-slate-600'"
                                class="px-4 py-1 transition">Filter Ambil</button>
                            <button type="button" @click="logistik='cod'"
                                :class="logistik === 'cod' ? 'bg-[#073090] text-white' : 'bg-white text-slate-600'"
                                class="px-4 py-1 transition">Filter COD</button>
                        </span>
                    </label>
                    <input type="hidden" name="logistik" :value="logistik">

                    <template x-if="logistik === 'ambil'">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                            Jl. Prof. Dr. Hamka, Tambakaji, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50151 (Sebelah gerbang
                            kampus 1 UIN WALISONGO)
                            <input type="hidden" name="alamat_pengiriman" value="Kantor Pusat LensHub">
                        </div>
                    </template>

                    <template x-if="logistik === 'cod'">
                        <textarea name="alamat_pengiriman" placeholder="Masukkan alamat lengkap pengiriman..." required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                            rows="3">{{ old('alamat_pengiriman') }}</textarea>
                    </template>
                </div>

                {{-- Total harga --}}
                <div class="flex items-center justify-between rounded-2xl bg-blue-50 px-6 py-4">
                    <span class="font-medium text-slate-600">Total Harga</span>
                    <span class="text-2xl font-bold text-[#073090]">
                        Rp <span x-text="formatRupiah(hitungHarga())"></span>
                    </span>
                </div>

                <p class="text-center text-xs text-slate-400">
                    Dengan memesan, Anda menyetujui
                    <a href="{{ route('rules') }}" target="_blank" class="text-blue-600 underline">Syarat & Ketentuan</a> yang berlaku.
                </p>

                <button type="submit"
                    class="w-full rounded-2xl bg-[#073090] py-4 text-lg font-semibold text-white transition hover:bg-blue-800">
                    Pesan Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
        function sewaForm(hargaHarian) {
            return {
                durasi: '{{ old('durasi', '1hari') }}',
                hargaHarian: hargaHarian,
                hitungHarga() {
                    const map = {
                        '12jam': hargaHarian * 0.80,
                        '1hari': hargaHarian,
                        '3hari': hargaHarian * 3 * 0.90,
                        '5hari': hargaHarian * 5 * 0.85,
                        '7hari': hargaHarian * 7 * 0.83,
                    };
                    return map[this.durasi] ?? hargaHarian;
                },
                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(val));
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* popup */
        .flatpickr-calendar {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08) !important;
            padding: 8px !important;
        }

        /* container */
        .flatpickr-time {
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }

        /* angka */
        .flatpickr-time input {

            height: 42px !important;
            width: 58px !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            text-align: center !important;
            border-radius: 10px !important;
            line-height: 42px !important;
        }

        /* titik dua */
        .flatpickr-time .flatpickr-time-separator {
            font-size: 18px !important;
            font-weight: 600 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
        }

        /* panah */
        .flatpickr-time .arrowUp,
        .flatpickr-time .arrowDown {
            width: 18px !important;
            height: 18px !important;
            right: 6px !important;
            opacity: .55;
        }

        .flatpickr-time .arrowUp:hover,
        .flatpickr-time .arrowDown:hover {
            opacity: 1;
        }

        .flatpickr-am-pm {
            display: none !important;
        }
    </style>

    <script>
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        const jamMulaiPicker = flatpickr("#jam_mulai", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        function syncMinTime() {
            if (!tanggalMulaiInput) return;

            const today = new Date();
            const selectedDate = tanggalMulaiInput.value;
            const todayStr = [
                today.getFullYear(),
                String(today.getMonth() + 1).padStart(2, '0'),
                String(today.getDate()).padStart(2, '0')
            ].join('-');

            if (selectedDate === todayStr) {
                const hh = String(today.getHours()).padStart(2, '0');
                const mm = String(today.getMinutes()).padStart(2, '0');
                const minTime = `${hh}:${mm}`;

                jamMulaiPicker.set('minTime', minTime);

                if (jamMulaiPicker.input.value && jamMulaiPicker.input.value < minTime) {
                    jamMulaiPicker.setDate(minTime, false, 'H:i');
                }
            } else {
                jamMulaiPicker.set('minTime', null);
            }
        }

        tanggalMulaiInput?.addEventListener('change', syncMinTime);
        syncMinTime();

        const formSewa = document.querySelector('form');
        formSewa?.addEventListener('submit', function(e) {
            const tanggal = tanggalMulaiInput?.value;
            const jam = jamMulaiPicker.input.value;
            if (!tanggal || !jam) return;

            const selected = new Date(`${tanggal}T${jam}:00`);
            const now = new Date();
            if (selected < now) {
                e.preventDefault();
                alert('Waktu pinjam tidak boleh sudah terlewati.');
            }
        });
    </script>
@endsection
