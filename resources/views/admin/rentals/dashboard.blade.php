<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ambil Hari Ini</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $stats['ambil_hari_ini'] }} <span class="text-xs font-normal text-slate-400">Unit</span></h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kembali Hari Ini</p>
                    <h3 class="text-2xl font-black text-teal-600">{{ $stats['kembali_hari_ini'] }} <span class="text-xs font-normal text-slate-400">Unit</span></h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terlambat / Overdue</p>
                    <h3 class="text-2xl font-black text-rose-600">{{ $stats['terlambat'] }} <span class="text-xs font-normal text-slate-400">Unit</span></h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pending Booking</p>
                    <h3 class="text-2xl font-black text-amber-500">{{ $stats['total_booking'] }} <span class="text-xs font-normal text-slate-400">Antrean</span></h3>
                </div>
            </div>

            @if($stats['total_booking'] > 0)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-400 text-white rounded-full flex items-center justify-center animate-bounce">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900">Perlu Tindakan Segera!</h4>
                        <p class="text-xs text-amber-700">Ada {{ $stats['total_booking'] }} bookingan yang harus diverifikasi dan diaktifkan.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Daftar Permohonan Sewa (Booked)</h3>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase">Verifikasi Manual</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Unit Alat</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Penyewa</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">WA</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Jadwal Sewa</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Lihat Detail</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($permohonan as $item)
                            <tr class="hover:bg-slate-50/50 transition" x-data="{ openDetail: false }">
                                <td class="p-4">
                                    <p class="font-bold text-slate-800 text-sm">{{ $item->gear->name }}</p>
                                    <p class="text-[10px] text-teal-600 font-bold">{{ $item->booking_code }}</p>
                                </td>
                                <td class="p-4 text-sm">{{ $item->user->name }}</td>
                                <td class="text-[12px] text-teal-600 font-bold">{{ $item->whatsapp }}</td>
                                <td class="p-4 text-sm">{{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}</td>
                                <td class="p-4">
                                    <button @click="openDetail = true" class="text-teal-600 hover:text-teal-800 font-bold text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info"></i> Lihat Detail
                                    </button>
                                </td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('admin.rentals.aktifkan', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-teal-500 text-white px-3 py-1.5 rounded-lg font-bold text-xs">Aktifkan</button>
                                    </form>
                                </td>
                                
                                <!-- Bagian Modal Detail -->

                                <template x-teleport="body">
                                    <div x-show="openDetail"
                                        x-on:keydown.escape.window="openDetail = false"
                                        x-effect="document.body.classList.toggle('overflow-hidden', openDetail)"
                                        class="fixed inset-0 z-[99] overflow-y-auto bg-slate-900/60 backdrop-blur-sm"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0">

                                        {{-- Wrapper buat center + padding --}}
                                        <div class="flex min-h-full items-center justify-center p-4">
                                            <div @click.away="openDetail = false"
                                                class="bg-white w-full max-w-lg rounded-3xl shadow-2xl my-8">

                                                <div class="bg-slate-900 p-5 text-white flex justify-between items-center rounded-t-3xl">
                                                    <h3 class="font-bold uppercase tracking-widest text-sm">Detail Permohonan Sewa</h3>
                                                    <button @click="openDetail = false"><i class="fa-solid fa-xmark"></i></button>
                                                </div>

                                                {{-- Bagian ini yg scroll, bukan body --}}
                                                <div class="p-6 space-y-4 max-h- overflow-y-auto">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Unit Alat</p>
                                                            <p class="text-sm font-bold text-slate-800">{{ $item->gear->name }} ({{ $item->gear->unit_code }})</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Tanggal Pembuatan</p>
                                                            <p class="text-sm font-bold text-slate-800">{{ $item->created_at->format('d M Y, H:i') }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Tujuan Sewa</p>
                                                            <p class="text-sm font-bold text-slate-800">{{ $item->purpose }}</p>
                                                        </div>
                                                    </div>

                                                    <hr class="border-slate-100">

                                                    <div class="space-y-3">
                                                        <div class="flex justify-between">
                                                            <span class="text-xs text-slate-500">Nama Penyewa</span>
                                                            <span class="text-xs font-bold text-slate-800">{{ $item->user->name }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-xs text-slate-500">WhatsApp</span>
                                                            <span class="text-xs font-bold text-teal-600">{{ $item->whatsapp }}</span>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-xs text-slate-500 mb-1">Alamat Domisili</span>
                                                            <span class="text-xs font-bold text-slate-800 bg-slate-50 p-2 rounded-lg">{{ $item->alamat }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-xs text-slate-500">Metode Bayar</span>
                                                            <span class="text-xs font-bold text-slate-800">{{ $item->payment_method }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="bg-teal-50 p-4 rounded-2xl border border-teal-100">
                                                        <p class="text- font-bold text-teal-600 uppercase mb-2">Dokumen Verifikasi (KTP)</p>
                                                        @if($item->foto_ktp)
                                                            <img src="{{ route('admin.view-ktp', $item->id) }}"
                                                                class="w-full h-auto rounded-xl shadow-sm"
                                                                alt="KTP">
                                                        @else
                                                            <div class="text-center p-4 border-2 border-dashed border-slate-200 rounded-xl">
                                                                <p class="text-xs text-slate-400">Foto KTP tidak diunggah</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="p-4 bg-slate-50 flex gap-2 rounded-b-3xl">
                                                    <button @click="openDetail = false" class="flex-1 py-3 text-xs font-bold text-slate-500">Tutup</button>
                                                    <a href="https://wa.me/{{ $item->whatsapp }}" target="_blank" class="flex-1 bg-teal-500 text-white py-3 rounded-xl text-center text-xs font-bold">Chat WA</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </tr>
                            @empty
                            ...
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Area Monitoring Aktif -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-teal-50/30">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-teal-600"></i>
                Monitoring Rental Aktif
            </h3>
            <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-[10px] font-bold uppercase">Sedang Disewa</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Unit & Kode</th>
                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Penyewa</th>
                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Sisa Waktu / Status</th>
                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Estimasi Tagihan</th>
                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($monitoring as $item)
                    @php
                        $isOverdue = now()->greaterThan($item->end_date);
                        $diff = now()->diff($item->end_date);
                    @endphp
                    <tr class="{{ $isOverdue ? 'bg-rose-50/30' : '' }} hover:bg-slate-50/50 transition">
                        <td class="p-4">
                            <p class="font-bold text-slate-800 text-sm">{{ $item->gear->name }}</p>
                            <p class="text-[9px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded inline-block font-mono">{{ $item->booking_code }}</p>
                        </td>
                        <td class="p-4">
                            <p class="text-sm font-medium text-slate-700">{{ $item->user->name }}</p>
                            <a href="https://wa.me/{{ $item->whatsapp }}" target="_blank" class="text-[10px] text-teal-600 font-bold hover:underline">
                                <i class="fa-brands fa-whatsapp"></i> Chat Penyewa
                            </a>
                        </td>
                        <td class="p-4">
                            @if($isOverdue)
                                <div class="flex items-center gap-2 text-rose-600">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    </span>
                                    <p class="text-xs font-black uppercase">Telat {{ $diff->d }} Hari {{ $diff->h }} Jam</p>
                                </div>
                            @else
                                <div class="text-xs">
                                    <p class="text-slate-500">Kembali dalam:</p>
                                    <p class="font-bold text-slate-700">{{ $diff->d }}H {{ $diff->h }}J {{ $diff->i }}M</p>
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="text-xs">
                                <p class="text-slate-400">Sewa: Rp{{ number_format($item->total_price) }}</p>
                                @if($isOverdue)
                                    <p class="text-rose-600 font-bold">Denda: +Rp{{ number_format($item->penalty_details['total']) }}</p>
                                @else
                                    <p class="text-teal-600 font-bold">Lancar</p>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.rentals.selesai', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Apakah barang sudah kembali dengan selamat?')"
                                    class="bg-slate-800 hover:bg-black text-white px-4 py-2 rounded-xl text-xs font-bold transition">
                                    Selesai / Kembali
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-400 text-sm italic">Tidak ada rental yang sedang aktif saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function sendWA(phone, gear, dueDate, penalty) {
            let formattedPhone = phone.replace(/[^0-9]/g, '');
            if(formattedPhone.startsWith('0')) formattedPhone = '62' + formattedPhone.slice(1);

            const message = `Halo Kak, sewa *${gear}* sudah AKTIF ya. \n\nBatas kembali: *${dueDate}*. \nJika terlambat, dikenakan denda sesuai tarif harian ya kak. \n\nMohon dijaga unitnya, terima kasih!`;
            
            const waUrl = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank');
        }
    </script>
</x-app-layout>