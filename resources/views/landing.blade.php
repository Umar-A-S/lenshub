@extends('layouts.public')

@section('content')
    <div class="space-y-16 md:space-y-32">

        {{-- 1. DRAMATIC HERO SECTION --}}
        <section class="relative overflow-hidden">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                <div class="z-10 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 border border-white/20 mb-6 backdrop-blur-md">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-widest text-blue-100">Gear Terbaru Telah Tersedia</span>
                    </div>

                    <h1 class="text-5xl font-black leading-[1] text-white md:text-7xl xl:text-8xl">
                        Capture the <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-white">Extraordinary.</span>
                    </h1>

                    <p class="mx-auto mt-8 max-w-lg text-lg leading-relaxed text-blue-100/70 md:mx-0 md:text-xl">
                        Sewa kamera dan perlengkapan sinema kelas dunia tanpa beban biaya kepemilikan. Siap untuk proyek besar Anda selanjutnya?
                    </p>

                    <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center lg:justify-start">
                        <a href="{{ route('produk.index') }}"
                            class="group relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-2xl bg-white px-10 py-5 font-black text-[#073090] shadow-[0_0_40px_rgba(255,255,255,0.3)] transition-all hover:scale-105 active:scale-95 sm:w-auto">
                            <span>Jelajahi Katalog</span>
                        </a>
                        <a href="#featured" class="px-8 py-4 font-bold text-white hover:text-blue-200 transition-colors">
                            Lihat Rekomendasi
                        </a>
                    </div>

                    {{-- TRUST SIGNALS --}}
                    <div class="mt-12 flex flex-wrap justify-center lg:justify-start gap-8 opacity-60">
                         <div class="text-center lg:text-left">
                            <p class="text-2xl font-black text-white">{{ $sewaAktif }}+</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">Rentals Today</p>
                         </div>
                         <div class="text-center lg:text-left">
                            <p class="text-2xl font-black text-white">{{ $totalStok }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">Units Available</p>
                         </div>
                         <div class="text-center lg:text-left">
                            <p class="text-2xl font-black text-white">24/7</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">Support Ready</p>
                         </div>
                    </div>
                </div>

                <div class="relative lg:h-[600px] flex items-center justify-center">
                    {{-- DECORATIVE ELEMENT: FLOATING GEAR PREVIEW --}}
                    <div class="relative w-full max-w-md aspect-square md:max-w-xl">
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/30 to-white/10 rounded-[3rem] rotate-6 blur-2xl"></div>
                        <div class="absolute inset-0 bg-white/10 backdrop-blur-3xl rounded-[3.5rem] border border-white/20 -rotate-3 transition-transform hover:rotate-0 duration-700">
                             {{-- Placeholder for a hero product image --}}
                             <div class="flex h-full w-full items-center justify-center p-12">
                                <div class="text-center text-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-32 w-32 md:h-48 md:w-48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                                        <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="mt-4 font-bold text-sm tracking-widest uppercase">Premium Gear Selection</p>
                                </div>
                             </div>

                             {{-- Floating Badge --}}
                             <div class="absolute -bottom-6 -left-6 md:-left-12 bg-white rounded-3xl p-6 shadow-2xl animate-bounce-slow">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-900 font-black">Asuransi All-Risk</p>
                                        <p class="text-slate-500 text-xs">Sewa dengan tenang.</p>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. PREMIUM SEARCH --}}
        <section class="max-w-5xl mx-auto w-full px-4">
            <div class="bg-white/5 backdrop-blur-xl rounded-[2.5rem] p-4 border border-white/10 shadow-2xl">
                <form action="{{ route('produk.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative group">
                        <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" placeholder="Cari gear impianmu (misal: Sony A7IV, RS3 Pro...)"
                            class="w-full h-16 md:h-20 pl-16 pr-6 rounded-3xl bg-white/10 border-none focus:ring-4 focus:ring-blue-500/50 text-white placeholder:text-white/30 text-lg transition-all">
                    </div>

                    <button type="submit" class="h-16 md:h-20 px-12 rounded-3xl bg-white text-[#073090] font-black text-lg hover:bg-blue-50 transition-all active:scale-95 shadow-xl">
                        Temukan
                    </button>
                </form>
            </div>
        </section>

        {{-- 3. FEATURED COLLECTION --}}
        <section id="featured" class="space-y-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-4">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-black text-white md:text-5xl lg:text-6xl">
                        Koleksi <span class="text-blue-200 italic">Paling Dicari.</span>
                    </h2>
                    <p class="mt-4 text-lg text-white/50 leading-relaxed">
                        Inilah gear yang paling sering menemani para kreator profesional mewujudkan karya terbaik mereka.
                    </p>
                </div>
                <a href="{{ route('produk.index') }}" class="group flex items-center gap-3 text-lg font-bold text-white hover:text-blue-300 transition-all">
                    Lihat Semua Gear
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 border border-white/20 group-hover:bg-white group-hover:text-blue-900 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 px-4">
                @forelse ($topAlat as $item)
                    <div class="group relative flex flex-col overflow-hidden rounded-[2.5rem] bg-white/5 border border-white/10 transition-all hover:bg-white/10 hover:border-white/20 hover:-translate-y-2">
                        {{-- Product Image Container --}}
                        <div class="relative aspect-[1/1] overflow-hidden m-3 rounded-[2rem]">
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="flex h-full items-center justify-center bg-white/5 text-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg">Popular</span>
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="flex flex-1 flex-col p-6 pt-2">
                            <h3 class="text-xl font-bold text-white line-clamp-1 group-hover:text-blue-200 transition-colors">
                                {{ $item->nama }}
                            </h3>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">Status</p>
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-green-400"></span>
                                        <span class="text-sm font-semibold text-white/80">Available</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">Total Sewa</p>
                                    <p class="text-sm font-bold text-white">{{ $item->total_disewa ?? 0 }}x</p>
                                </div>
                            </div>

                            <a href="{{ route('produk.show', $item) }}" 
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-white/10 py-4 text-sm font-black text-white border border-white/10 transition-all hover:bg-white hover:text-[#073090] hover:border-transparent">
                                Detail Gear
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                         <p class="text-2xl font-bold text-white/20">Katalog sedang diperbarui.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 4. WHY LENS HUB? --}}
        <section class="bg-white/5 rounded-[3rem] border border-white/10 p-12 md:p-24 overflow-hidden relative">
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 h-[500px] w-[500px] bg-blue-500/10 blur-[120px] rounded-full"></div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <h2 class="text-4xl md:text-6xl font-black text-white leading-tight">
                        Mengapa Harus <br>
                        <span class="text-blue-300">LensHub?</span>
                    </h2>
                    <p class="mt-8 text-lg text-white/60 leading-relaxed">
                        Kami bukan sekadar tempat sewa. Kami adalah partner kreatif yang memastikan peralatan Anda selalu siap, terawat, dan terjamin keselamatannya.
                    </p>
                    
                    <div class="mt-12 space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="h-14 w-14 shrink-0 rounded-2xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-300 font-bold text-xl italic">01</div>
                            <div>
                                <h4 class="text-xl font-bold text-white">Maintenance Berkala</h4>
                                <p class="text-white/50 mt-2">Setiap gear melewati QC ketat dan pembersihan sensor setiap kali kembali ke gudang.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="h-14 w-14 shrink-0 rounded-2xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-300 font-bold text-xl italic">02</div>
                            <div>
                                <h4 class="text-xl font-bold text-white">Booking Sistem Realtime</h4>
                                <p class="text-white/50 mt-2">Cek ketersediaan barang secara langsung tanpa perlu bertanya ke admin. Klik, Bayar, Ambil.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4 pt-12">
                        <div class="aspect-[4/5] rounded-[2.5rem] bg-white/10 border border-white/20 flex items-center justify-center p-8">
                             <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-4 text-white font-bold">Fast Process</p>
                             </div>
                        </div>
                        <div class="aspect-square rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-blue-400 p-8 flex items-end">
                             <p class="text-white font-black text-2xl leading-tight">Harga <br> Kompetitif</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="aspect-square rounded-[2.5rem] bg-white text-[#073090] p-8 flex flex-col justify-between">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                             </svg>
                             <p class="font-black text-2xl leading-tight">Terpercaya</p>
                        </div>
                        <div class="aspect-[4/5] rounded-[2.5rem] bg-white/10 border border-white/20 flex items-center justify-center p-8 text-center">
                             <div>
                                <p class="text-4xl font-black text-white">5★</p>
                                <p class="mt-2 text-white/50 text-xs uppercase font-bold tracking-widest">Customer Rating</p>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. CALL TO ACTION --}}
        <section class="text-center py-20 px-4">
            <h2 class="text-4xl md:text-6xl font-black text-white leading-tight">
                Siap Melahirkan <br> Karya <span class="text-blue-300">Masterpiece?</span>
            </h2>
            <p class="mt-8 text-lg text-white/50 max-w-2xl mx-auto">
                Jangan biarkan keterbatasan alat menghalangi imajinasi Anda. Mulai sewa sekarang dan buktikan kualitasnya.
            </p>
            <div class="mt-12">
                 <a href="{{ route('produk.index') }}"
                    class="inline-flex items-center gap-4 rounded-3xl bg-white px-12 py-6 font-black text-[#073090] shadow-2xl transition-all hover:scale-110 active:scale-95 text-xl">
                    Sewa Gear Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </section>

    </div>

    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 4s ease-in-out infinite;
        }
    </style>
@endsection
