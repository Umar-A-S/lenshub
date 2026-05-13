<x-app-layout>
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-lg pb-10" x-data="{ 
        filter: 'Semua', 
        selectedGearId: '',
        pricePerDay: 0,
        duration: 1,
        startDate: '',
        customDuration: '',
        isCustom: false,
        agreed: false,
        bookedDates: [], 
        
        get currentDuration() { 
            let d = this.isCustom ? parseInt(this.customDuration) : parseInt(this.duration);
            return isNaN(d) ? 0 : d;
        },
        get totalPrice() { return this.pricePerDay * this.currentDuration },

        // Logika Cek Bentrok di Frontend
        isConflict() {
            if (!this.startDate || !this.selectedGearId || this.currentDuration <= 0) return false;
            
            let startReq = new Date(this.startDate);
            let endReq = new Date(this.startDate);
            endReq.setDate(startReq.getDate() + this.currentDuration);

            return this.bookedDates.some(range => {
                let bStart = new Date(range.start);
                let bEnd = new Date(range.end);
                // Overlap: (StartA < EndB) AND (EndA > StartB)
                return (startReq < bEnd && endReq > bStart);
            });
        },

        updateBookedDates(rentals) {
            this.bookedDates = rentals.map(r => ({
                start: r.start_date,
                end: r.end_date
            }));
        }
    }">
        <div class="bg-slate-900 text-white p-6 rounded-b-[30px] text-center">
            <h1 class="text-2xl font-bold italic tracking-wider">EquipRent</h1>
            <p class="text-[10px] text-teal-400 uppercase font-bold tracking-[0.2em]">Form Penyewaan Alat</p>
        </div>

        <form action="{{ route('rentals.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-6">
            @csrf
            
            <input type="hidden" name="gear_id" :value="selectedGearId">
            <input type="hidden" name="total_price" :value="totalPrice">
            <input type="hidden" name="duration" :value="currentDuration">

            <div class="space-y-4">
                <div class="relative">
                    <input type="text" value="{{ auth()->user()->name }}" class="w-full p-3 border-2 border-slate-200 rounded-xl bg-slate-50 text-slate-500 text-sm outline-none" readonly>
                    <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600 uppercase">Nama Penyewa</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm focus:border-teal-500 outline-none">
                        <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600 uppercase">Email</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="whatsapp" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm focus:border-teal-500 outline-none" placeholder="08xxx">
                        <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600 uppercase">WhatsApp</span>
                    </div>
                </div>

                <div class="relative">
                    <textarea name="alamat" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm focus:border-teal-500 outline-none" rows="2"></textarea>
                    <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600 uppercase">Alamat Domisili</span>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Pilih Alat</h3>
                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2">
                    <template x-for="cat in ['Semua', 'Kamera', 'Lensa', 'Drone', 'Lainnya']">
                        <button type="button" @click="filter = cat" 
                            :class="filter === cat ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-500'"
                            class="px-5 py-1.5 rounded-full text-[11px] font-bold transition whitespace-nowrap" x-text="cat"></button>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    @foreach($gears as $gear)
                    <div x-show="filter === 'Semua' || '{{ $gear->category->name }}' === filter" 
                        @click="selectedGearId = '{{ $gear->id }}'; pricePerDay = {{ $gear->rent_price }}; updateBookedDates({{ $gear->rentals->toJson() }})"
                        :class="selectedGearId == '{{ $gear->id }}' ? 'border-teal-500 bg-teal-50 ring-2 ring-teal-500' : 'border-slate-200 bg-white'"
                        class="p-3 border-2 rounded-2xl cursor-pointer transition shadow-sm">
                        <div class="w-full h-20 bg-slate-100 rounded-xl mb-2 flex items-center justify-center italic text-slate-400 text-[10px]">Foto Alat</div>
                        <p class="text-[11px] font-bold text-slate-800 truncate">{{ $gear->name }}</p>
                        <p class="text-[10px] text-teal-600 font-bold">Rp {{ number_format($gear->rent_price) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <input type="date" x-model="startDate" name="start_date" required 
                            :class="isConflict() ? 'border-red-500 bg-red-50' : 'border-slate-200'"
                            class="w-full p-3 border-2 rounded-xl text-sm outline-none">
                        <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600">TGL MULAI</span>
                    </div>
                    <div class="relative">
                        <input type="time" name="start_time" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm outline-none">
                        <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600">JAM AMBIL</span>
                    </div>
                </div>

                <template x-if="isConflict()">
                    <div class="p-3 bg-red-100 text-red-700 rounded-xl text-[10px] font-bold flex items-center gap-2 animate-pulse">
                        ⚠️ Alat sudah dibooking pada tanggal tersebut. Silakan pilih jadwal lain.
                    </div>
                </template>

                <h3 class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Durasi (Hari)</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="n in [1, 2, 3, 4, 5, 7, 10]">
                        <button type="button" @click="duration = n; isCustom = false" 
                            :class="duration === n && !isCustom ? 'bg-teal-600 text-white' : 'bg-white text-slate-500 border-slate-200'"
                            class="w-10 h-10 flex items-center justify-center border-2 rounded-xl text-sm font-bold transition" x-text="n"></button>
                    </template>
                    <button type="button" @click="isCustom = true; duration = 0"
                        :class="isCustom ? 'bg-teal-600 text-white' : 'bg-white text-slate-500 border-slate-200'"
                        class="px-4 h-10 flex items-center justify-center border-2 rounded-xl text-sm font-bold transition">Lainnya</button>
                </div>
                
                <div x-show="isCustom" x-transition class="mt-2">
                    <input type="number" x-model="customDuration" placeholder="Masukkan jumlah hari..." class="w-full p-3 border-2 border-teal-500 rounded-xl text-sm outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="relative">
                    <select name="purpose" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm outline-none appearance-none">
                        <option value="Hobi">Hobi / Hunting</option>
                        <option value="Project">Project Komersial</option>
                        <option value="Wedding">Wedding / Event</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600">TUJUAN SEWA</span>
                </div>
                <div class="relative">
                    <select name="payment_method" required class="w-full p-3 border-2 border-slate-200 rounded-xl text-sm outline-none appearance-none">
                        <option value="QRIS">QRIS / E-Wallet</option>
                        <option value="Transfer">Transfer Bank</option>
                        <option value="Cash">Tunai (Bayar di Toko)</option>
                    </select>
                    <span class="absolute -top-2.5 left-3 bg-white px-1 text-[11px] font-bold text-teal-600">PEMBAYARAN</span>
                </div>
            </div>

            <div class="py-2">
                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Dokumen Verifikasi</h3>
                <label class="block border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center bg-slate-50 hover:bg-slate-100 cursor-pointer transition">
                    <i class="fa-solid fa-id-card text-2xl text-slate-400 mb-2"></i>
                    <p class="text-[10px] font-bold text-slate-600">Upload Foto Wajah + KTP</p>
                    <input type="file" name="foto_ktp" class="hidden" accept="image/*" required>
                </label>
            </div>

            <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl">
                <h4 class="text-[11px] font-bold text-amber-800 uppercase mb-1">Catatan Penting:</h4>
                <ul class="text-[10px] text-amber-700 space-y-1 list-disc ml-3">
                    <li>Keterlambatan > 2 jam dikenakan denda sesuai tarif item.</li>
                    <li>Kerusakan/Kehilangan alat wajib diganti sesuai nilai barang.</li>
                </ul>
                <label class="mt-3 flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="agreed" class="rounded text-teal-600 focus:ring-teal-500">
                    <span class="text-[10px] font-bold text-slate-700">Saya setuju dengan peraturan yang berlaku.</span>
                </label>
            </div>

            <div class="sticky bottom-4">
                <div class="bg-slate-900 p-4 rounded-2xl shadow-2xl flex justify-between items-center border-t border-slate-800">
                    <div>
                        <p class="text-[9px] text-slate-400 uppercase font-bold">Total Estimasi</p>
                        <p class="text-lg font-bold text-teal-400" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></p>
                    </div>
                    <button type="submit" :disabled="!agreed || !selectedGearId || isConflict()" 
                        :class="agreed && selectedGearId && !isConflict() ? 'bg-teal-500' : 'bg-slate-700 cursor-not-allowed'"
                        class="px-6 py-2.5 rounded-xl text-white font-bold text-xs transition active:scale-95">
                        Sewa Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>