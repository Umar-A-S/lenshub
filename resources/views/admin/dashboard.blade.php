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
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Penyewa & WA</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Jadwal Sewa</th>
                                <th class="p-4 text-[10px] font-bold text-slate-400 uppercase text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($permohonan as $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4">
                                    <p class="font-bold text-slate-800 text-sm">{{ $item->gear->name }}</p>
                                    <p class="text-[10px] text-teal-600 font-bold">{{ $item->booking_code }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-medium text-slate-700">{{ $item->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $item->whatsapp }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="text-xs">
                                        <p class="font-bold text-slate-700">{{ $item->start_date->format('d M Y') }}</p>
                                        <p class="text-slate-400">Jam Ambil: {{ $item->start_time }}</p>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('admin.rentals.aktifkan', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            onclick="sendWA('{{ $item->whatsapp }}', '{{ $item->gear->name }}', '{{ $item->end_date->format('d M Y') }}', '{{ $item->gear->rent_price }}')"
                                            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm shadow-teal-200">
                                            Aktifkan & Kirim WA
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-slate-400 text-sm italic">Belum ada permohonan booking baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

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