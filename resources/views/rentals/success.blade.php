<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-slate-50 pb-10">
        <div id="invoice-card" class="bg-white p-6 shadow-sm border-b-2 border-dashed border-slate-200">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check text-3xl"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-800">Booking Berhasil!</h1>
                <p class="text-xs text-slate-500">Tunjukkan struk ini saat pengambilan alat</p>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <div class="flex justify-between">
                    <span class="text-[11px] text-slate-400 uppercase font-bold">Kode Booking</span>
                    <span class="text-[11px] text-slate-800 font-bold">{{ $rental->booking_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[11px] text-slate-400 uppercase font-bold">Nama Alat</span>
                    <span class="text-[11px] text-slate-800 font-bold">{{ $rental->gear->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[11px] text-slate-400 uppercase font-bold">Tgl Ambil</span>
                    <span class="text-[11px] text-slate-800 font-bold">{{ $rental->start_date->format('d M Y') }} ({{ $rental->start_time }})</span>
                </div>
                <div class="flex justify-between border-t border-dashed pt-4">
                    <span class="text-sm text-slate-800 font-bold">Total Bayar</span>
                    <span class="text-sm text-teal-600 font-extrabold">Rp {{ number_format($rental->total_price) }}</span>
                </div>
            </div>

            <div class="mt-8 text-center border-t border-slate-100 pt-4">
                <p class="text-[9px] text-slate-400">Terima kasih telah menggunakan **EquipRent**</p>
                <p class="text-[9px] text-slate-400 italic">Jl. Ngaliyan No. 12, Semarang</p>
            </div>
        </div>

        <div class="p-6 space-y-3">
            <button onclick="downloadInvoice()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-lg">
                <i class="fa-solid fa-download"></i> Simpan Sebagai Foto
            </button>
            <a href="{{ route('dashboard') }}" class="w-full block text-center bg-white border-2 border-slate-200 text-slate-600 py-3 rounded-xl font-bold text-sm">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        function downloadInvoice() {
            const element = document.getElementById('invoice-card');
            
            // Pengaturan agar gambar jernih
            html2canvas(element, {
                scale: 3, // Meningkatkan resolusi gambar
                backgroundColor: "#ffffff",
                logging: false,
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'EquipRent-Booking-{{ $rental->booking_code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</x-app-layout>