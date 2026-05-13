<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EquipRent - Form Sewa Mobile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Style for floating labels to ensure they stay visible */
        .floating-label-group { position: relative; margin-top: 10px; }
        .floating-label {
            position: absolute;
            top: -10px;
            left: 12px;
            background: white;
            padding: 0 4px;
            font-size: 11px;
            font-weight: 700;
            color: #0d9488;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800" x-data="{ 
    filter: 'Semua', 
    duration: 1, 
    customDuration: '',
    selectedId: null,
    products: [
        { id: 1, name: 'Sony Alpha 7 IV', category: 'Kamera', price: '350k', img: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=200' },
        { id: 2, name: 'Sony 24-70mm GM', category: 'Lensa', price: '200k', img: 'https://images.unsplash.com/photo-1617113931037-7724392f69f2?w=200' },
        { id: 3, name: 'DJI Mavic 3 Pro', category: 'Drone', price: '500k', img: 'https://images.unsplash.com/photo-1508614589041-895b88991e3e?w=200' },
        { id: 4, name: 'Manfrotto Tripod', category: 'Lainnya', price: '75k', img: 'https://images.unsplash.com/photo-1581591524425-c7e0978865fc?w=200' }
    ]
}">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-lg pb-10">
        <!-- Header -->
        <div class="bg-slate-900 text-white p-6 rounded-b-[30px] text-center">
            <h1 class="text-2xl font-bold italic tracking-wider">EquipRent</h1>
            <p class="text-[10px] text-teal-400 uppercase font-bold tracking-[0.2em]">Professional Equipment Rental</p>
        </div>

        <div class="p-5 space-y-6">
            <!-- Data Diri with Fixed Labels -->
            <div class="floating-label-group">
                <span class="floating-label">Nama Lengkap</span>
                <input type="text" class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition" placeholder="Masukkan nama...">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="floating-label-group">
                    <span class="floating-label">WhatsApp</span>
                    <input type="tel" class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition">
                </div>
                <div class="floating-label-group">
                    <span class="floating-label">Instagram</span>
                    <input type="text" class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition" placeholder="@">
                </div>
            </div>

            <div class="floating-label-group">
                <span class="floating-label">Email</span>
                <input type="email" class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition">
            </div>

            <div class="floating-label-group">
                <span class="floating-label">Alamat Domisili</span>
                <textarea class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition" rows="2" placeholder="Tulis alamat lengkap..."></textarea>
            </div>

            <!-- ID Verification -->
            <div class="py-2">
                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Verifikasi Keamanan</h3>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50 hover:bg-slate-100 cursor-pointer transition">
                    <i class="fa-solid fa-camera-retro text-3xl text-slate-400 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-600">Foto Wajah + KTP</p>
                    <input type="file" class="hidden">
                </div>
                <div class="mt-3 p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                    <p class="text-[10px] leading-relaxed text-amber-800">
                        <strong>CATATAN:</strong> *Pastikan foto jelas dan KTP terlihat jelas. Kami membutuhkan ini untuk mencocokkan data dengan kartu tanda penduduk.
                    </p>
                </div>
            </div>

            <!-- Product Category Filter -->
            <div class="py-2">
                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Katalog Alat</h3>
                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2">
                    <template x-for="cat in ['Semua', 'Kamera', 'Lensa', 'Drone', 'Lainnya']">
                        <button @click="filter = cat" 
                            :class="filter === cat ? 'bg-teal-600 text-white border-teal-600' : 'bg-slate-100 text-slate-500 border-transparent'"
                            class="px-5 py-1.5 rounded-full text-[11px] font-bold border-2 transition whitespace-nowrap shadow-sm" 
                            x-text="cat"></button>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <template x-for="product in products" :key="product.id">
                        <div x-show="filter === 'Semua' || product.category === filter" 
                            @click="selectedId = product.id"
                            :class="selectedId === product.id ? 'border-teal-500 bg-teal-50 ring-1 ring-teal-500' : 'border-slate-200 bg-white'"
                            class="p-3 border-2 rounded-2xl cursor-pointer transition transform active:scale-95 shadow-sm">
                            <img :src="product.img" class="w-full h-20 object-cover rounded-xl mb-2">
                            <p class="text-[11px] font-bold text-slate-800 leading-tight" x-text="product.name"></p>
                            <p class="text-[10px] text-teal-600 font-bold mt-1" x-text="'Rp ' + product.price + '/hari'"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Duration & Time -->
            <div class="py-2">
                <div class="floating-label-group mb-6">
                    <span class="floating-label">Mulai Sewa</span>
                    <input type="datetime-local" class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition bg-white">
                </div>

                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Durasi Sewa (Hari)</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="n in [1,2,3,4,5,7,10]">
                        <button @click="duration = n" 
                            :class="duration === n ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-500 border-slate-200'"
                            class="w-10 h-10 flex items-center justify-center border-2 rounded-xl text-sm font-bold transition" x-text="n"></button>
                    </template>
                    <button @click="duration = 'more'" 
                        :class="duration === 'more' ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-500 border-slate-200'"
                        class="px-4 h-10 border-2 rounded-xl text-xs font-bold transition">Lainnya</button>
                </div>
                
                <div x-show="duration === 'more'" class="mt-4 transition-all duration-300">
                    <div class="floating-label-group">
                        <span class="floating-label">Input Manual Hari</span>
                        <input type="number" x-model="customDuration" placeholder="Contoh: 15" class="w-full p-3 border-2 border-teal-300 rounded-xl outline-none text-sm shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Purpose & Payment -->
            <div class="py-2">
                <div class="floating-label-group mb-6">
                    <span class="floating-label">Tujuan Sewa</span>
                    <select class="w-full p-3 border-2 border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm transition bg-white">
                        <option>Project Film/Video</option>
                        <option>Wisata / Liburan</option>
                        <option>Wedding Documentation</option>
                        <option>Kebutuhan Konten Sosmed</option>
                    </select>
                </div>

                <h3 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Metode Pembayaran</h3>
                <div class="grid grid-cols-2 gap-3">
                    <button class="flex items-center gap-3 p-3 border-2 border-teal-500 bg-teal-50 rounded-xl text-left transition">
                        <i class="fa-solid fa-qrcode text-teal-600"></i>
                        <span class="text-xs font-bold">QRIS / E-Wallet</span>
                    </button>
                    <button class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl text-left transition grayscale hover:grayscale-0">
                        <i class="fa-solid fa-building-columns text-slate-500"></i>
                        <span class="text-xs font-bold text-slate-600">Transfer Bank</span>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6">
                <button class="w-full bg-slate-900 py-4 rounded-2xl text-white font-bold text-md shadow-xl shadow-slate-200 active:scale-[0.98] transition">
                    Konfirmasi Sewa Sekarang
                </button>
            </div>
        </div>
    </div>
</body>
</html>