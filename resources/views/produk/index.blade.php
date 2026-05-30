@extends('layouts.public')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-5xl font-bold text-white">
            Produk
        </h2>
        <p class="mt-2 text-white/80">
            Pilih alat yang ingin kamu lihat detailnya.
        </p>
    </div>

    <button
        type="button"
        class="rounded-2xl bg-[#073090] px-6 py-3 text-white shadow"
    >
        Tanya Admin
    </button>
</div>

<form
    action="{{ route('produk.index') }}"
    method="GET"
    class="mb-10 flex items-center gap-5"
>
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Cari alat..."
        class="h-[58px] flex-1 rounded-2xl bg-white px-8 outline-none"
    >

    <select
        name="status"
        class="h-[58px] w-[220px] rounded-2xl bg-white px-5"
    >
        <option value="">Semua Status</option>
        <option value="tersedia" @selected(request('status') === 'tersedia')>
            Tersedia
        </option>
        <option value="tidak tersedia" @selected(request('status') === 'tidak tersedia')>
            Tidak Tersedia
        </option>
    </select>

    <select
        name="category"
        class="h-[58px] w-[220px] rounded-2xl bg-white px-5"
    >
        <option value="">Semua Kategori</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(request('category') == $category->id)
            >
                {{ $category->nama }}
            </option>
        @endforeach
    </select>

    <button
        type="submit"
        class="h-[58px] rounded-2xl bg-[#073090] px-8 text-white"
    >
        Cari
    </button>

    <a
        href="{{ route('produk.index') }}"
        class="flex h-[58px] items-center rounded-2xl bg-gray-300 px-8"
    >
        Reset
    </a>
</form>

<div class="grid grid-cols-3 gap-6">
    @forelse ($equipments as $item)
        <a href="{{ route('produk.show', $item) }}" class="block">
            <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-[220px] items-center justify-center overflow-hidden bg-slate-100 p-3">
                    @if ($item->foto)
                        <img
                            src="{{ asset('storage/' . $item->foto) }}"
                            alt="{{ $item->nama }}"
                            class="h-full w-full rounded-2xl object-cover"
                        >
                    @else
                        <div class="text-slate-400">
                            Gambar alat
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="text-2xl font-bold text-slate-900">
                        {{ $item->nama }}
                    </h3>

                    <p class="mt-1 text-slate-500">
                        {{ $item->category?->nama ?? '-' }}
                    </p>

                    <p class="mt-2 text-slate-400">
                        {{ \Illuminate\Support\Str::limit($item->deskripsi, 55) }}
                    </p>

                    <div class="mt-5">
                        <p class="text-slate-500">
                            Stok Tersedia
                        </p>

                        <div class="mt-3 h-[8px] rounded-full bg-gray-200">
                            <div
                                class="h-[8px] rounded-full bg-[#1749D7]"
                                style="
                                    width: {{
                                        $item->stok > 0
                                            ? (($item->stok_tersedia / $item->stok) * 100)
                                            : 0
                                    }}%;
                                "
                            ></div>
                        </div>

                        <div class="mt-2 text-right text-slate-500">
                            {{ $item->stok_tersedia }}/{{ $item->stok }}
                        </div>
                    </div>

                    <div class="mt-5">
                        <span class="text-[26px] font-bold text-[#073090]">
                            Rp {{ number_format($item->harga_harian, 0, ',', '.') }}
                        </span>
                        <span class="text-slate-500">
                            / hari
                        </span>
                    </div>

                    <div class="mt-3 text-slate-600">
                        Disewa :
                        <span class="font-semibold text-[#073090]">
                            {{ $item->disewa_count ?? 0 }}x
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-3 rounded-3xl bg-white p-20 text-center text-slate-500">
            Belum ada data alat
        </div>
    @endforelse
</div>

@endsection