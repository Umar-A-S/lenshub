@extends('layouts.public')

@section('content')
    <div class="space-y-10">

        {{-- HERO --}}
        <section class="grid grid-cols-2 items-center gap-10">
            <div>
                <h1 class="text-6xl font-bold leading-tight text-white">
                    Sewa Kamera
                    <br>
                    Profesional,
                    <br>
                    Kapan Saja
                </h1>

                <p class="mt-8 max-w-xl text-lg leading-8 text-white/90">
                    Platform penyewaan kamera dan perlengkapan fotografi dengan monitoring realtime,
                    notifikasi otomatis, serta sistem pengelolaan inventori.
                </p>

                <a href="{{ route('produk.index') }}"
                    class="mt-10 inline-flex rounded-2xl bg-white px-8 py-4 font-semibold text-slate-900 shadow">
                    Lihat Produk
                </a>
            </div>

            <div class="rounded-[2rem] bg-white/15 p-8 shadow-lg backdrop-blur-md">

                <h2 class="mb-8 text-2xl font-bold text-white">
                    Live Dashboard
                </h2>


                <div class="grid grid-cols-2 gap-6">

                    <div class="rounded-3xl bg-white/10 p-6 text-white">
                        <h3 class="text-5xl font-bold">
                            {{ $sewaAktif }}
                        </h3>

                        <p class="mt-3 text-white/80">
                            Sewa Aktif
                        </p>
                    </div>


                    <div class="rounded-3xl bg-white/10 p-6 text-white">
                        <h3 class="text-5xl font-bold">
                            {{ $barangDisewa }}
                        </h3>

                        <p class="mt-3 text-white/80">
                            Total Barang Disewa
                        </p>
                    </div>

                </div>


                <div class="mt-12">

                    <h3 class="mb-5 text-xl font-semibold text-white">
                        Utilisasi Stok
                    </h3>


                    <div class="h-5 overflow-hidden rounded-full bg-white/30">

                        <div class="h-full rounded-full bg-[#073090] transition-all duration-500"
                            style="
                    width:
                    {{ $persenUtilisasi }}%
                ">
                        </div>

                    </div>


                    <p class="mt-4 text-lg font-medium text-white">
                        {{ $stokTersedia }}/{{ $totalStok }}
                        Tersedia
                    </p>

                </div>

            </div>
    </div>
    </section>

    {{-- SEARCH --}}
    <section>
        <form action="{{ route('produk.index') }}" method="GET" class="flex gap-4">
            <input type="text" name="search" placeholder="What you wanna search..."
                class="h-[60px] mt-5 flex-1 rounded-full bg-white px-8 shadow outline-none">

            <button type="submit" class="h-[60px] mt-5 rounded-full bg-[#073090] px-10 text-white shadow">
                Search
            </button>
        </form>
    </section>

    {{-- TOP 10 --}}
    <section class="space-y-6">
        <div class="mt-5 flex items-center justify-between">
            <h2 class="text-3xl font-bold text-white">
                10 Alat Paling Sering Disewa
            </h2>
        </div>

        <div class="grid grid-cols-5 gap-5">
            @forelse ($topAlat as $item)
                <a href="{{ route('produk.show', $item) }}" class="block">
                    <div class="rounded-[1.5rem] bg-white/20 p-4 text-white shadow backdrop-blur-md">
                        <div class="flex h-28 items-center justify-center overflow-hidden rounded-2xl bg-white/20">
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                    class="h-full w-full object-cover">
                            @else
                                <span class="text-white/70">Foto</span>
                            @endif
                        </div>

                        <h3 class="mt-4 font-semibold">
                            {{ $item->nama }}
                        </h3>

                        <p class="text-sm text-white/80">
                            Disewa {{ $item->total_disewa ?? 0 }}x
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-5 rounded-3xl bg-white/20 p-8 text-center text-white">
                    Belum ada data alat
                </div>
            @endforelse
        </div>
    </section>

    </div>
@endsection
