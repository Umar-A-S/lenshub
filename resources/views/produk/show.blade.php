@extends('layouts.public')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('produk.index') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 font-medium text-slate-900 shadow hover:bg-slate-100 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
        <h2 class="text-5xl font-bold text-white">Detail</h2>
    </div>
</div>

<div class="grid grid-cols-2 gap-10 rounded-[32px] bg-white/10 p-8 backdrop-blur-md">

    {{-- FOTO --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-3 shadow">
        <div class="h-[420px] bg-slate-100 flex items-center justify-center overflow-hidden rounded-2xl border border-slate-200">
            @if ($equipment->foto)
                <img src="{{ asset('storage/' . $equipment->foto) }}" alt="{{ $equipment->nama }}"
                    class="h-full w-full rounded-2xl object-cover">
            @else
                <div class="text-slate-400 text-lg">foto</div>
            @endif
        </div>
    </div>

    {{-- INFO --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow">
        <h3 class="text-3xl font-bold text-slate-900">{{ $equipment->nama }}</h3>
        <p class="mt-2 text-slate-500">{{ $equipment->category?->nama ?? '-' }}</p>
        <p class="mt-6 text-slate-700 leading-8">{{ $equipment->deskripsi }}</p>

        <div class="mt-8 space-y-4 text-slate-700">
            <p>Stok Tersedia:
                <span class="font-semibold">{{ $stokTersedia }}/{{ $equipment->stok }}</span>
            </p>
            <p>Disewa:
                <span class="font-semibold">{{ $disewaCount ?? 0 }}x</span>
            </p>
            <p>Harga Sewa:
                <span class="font-semibold text-[#073090]">
                    Rp {{ number_format($equipment->harga_harian, 0, ',', '.') }} / hari
                </span>
            </p>
        </div>

        <div class="mt-10 flex gap-4">
            <a href="https://wa.me/6283842510806?text={{ urlencode('Halo, saya ingin bertanya tentang ' . $equipment->nama) }}"
                target="_blank"
                class="rounded-2xl bg-green-100 px-6 py-3 text-green-800 shadow hover:bg-green-200 transition font-medium">
                Tanya Admin
            </a>

            @if($stokTersedia > 0)
                @auth
                    {{-- Sudah login → langsung ke form sewa --}}
                    <a href="{{ route('produk.sewa', $equipment) }}"
                        class="rounded-2xl bg-[#073090] px-6 py-3 text-white shadow hover:bg-blue-800 transition font-medium">
                        Sewa Sekarang
                    </a>
                @else
                    {{-- Belum login → simpan intended URL lalu redirect login --}}
                    <a href="{{ route('login') }}?redirect={{ urlencode(route('produk.sewa', $equipment)) }}"
                        class="rounded-2xl bg-[#073090] px-6 py-3 text-white shadow hover:bg-blue-800 transition font-medium">
                        Sewa Sekarang
                    </a>
                @endauth
            @else
                <button disabled
                    class="rounded-2xl bg-slate-200 px-6 py-3 text-slate-400 shadow cursor-not-allowed font-medium">
                    Stok Habis
                </button>
            @endif
        </div>

        @guest
            <p class="mt-3 text-xs text-slate-400 text-center">* Login diperlukan untuk melakukan pemesanan</p>
        @endguest
    </div>
</div>

@endsection
