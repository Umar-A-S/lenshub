@extends('layouts.admin')

@section('title', 'Inventory')
@section('subtitle', 'Hari ini, ' . now()->translatedFormat('d F Y'))

@section('content')

    <div class="p-10">

        @if (session('success'))
            <div class="mb-8 rounded-2xl bg-green-100 px-6 py-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('inventory.index') }}" method="GET" class="mb-10 flex items-center gap-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari alat..."
                class="h-[58px] flex-1 rounded-2xl bg-white px-8 outline-none">

            <select name="status" class="h-[58px] w-[220px] rounded-2xl bg-white px-5">
                <option value="">Semua Status</option>
                <option value="tersedia" @selected(request('status') === 'tersedia')>
                    Tersedia
                </option>
                <option value="tidak tersedia" @selected(request('status') === 'tidak tersedia')>
                    Tidak Tersedia
                </option>
            </select>

            <select name="category" class="h-[58px] w-[220px] rounded-2xl bg-white px-5">
                <option value="">Semua Kategori</option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                        {{ $category->nama }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="h-[58px] rounded-2xl bg-[#073090] px-8 text-white">
                Cari
            </button>

            <a href="{{ route('inventory.index') }}" class="flex h-[58px] items-center rounded-2xl bg-gray-300 px-8">
                Reset
            </a>

            <a href="{{ route('inventory.create') }}"
                class="flex h-[58px] items-center rounded-2xl bg-[#073090] px-5 text-white">
                + Tambah Alat
            </a>
        </form>

        <div class="grid grid-cols-3 gap-6">
            @forelse ($equipments as $item)
                <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow">
                    <div class="flex h-[220px] items-center justify-center overflow-hidden bg-slate-100 p-3">
                        @if ($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                class="h-full w-full rounded-2xl object-cover">
                        @else
                            <div class="text-slate-400">
                                Gambar alat
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <h2 class="text-[22px] font-bold">
                            {{ $item->nama }}
                        </h2>

                        <p class="mt-1 text-gray-500">
                            {{ $item->category?->nama ?? '-' }}
                        </p>

                        <p class="mt-2 text-gray-400">
                            {{ \Illuminate\Support\Str::limit($item->deskripsi, 35) }}
                        </p>

                        <div class="mt-5">
                            <p class="text-gray-500">
                                Stok Tersedia
                            </p>

                            <div class="mt-3 h-[8px] rounded-full bg-gray-200">
                                <div class="h-[8px] rounded-full bg-[#1749D7]"
                                    style="
                                    width: {{ $item->stok > 0 ? ($item->stok_tersedia / $item->stok) * 100 : 0 }}%;
                                ">
                                </div>
                            </div>

                            <div class="mt-2 text-right text-gray-500">
                                {{ $item->stok_tersedia }}/{{ $item->stok }}
                            </div>
                        </div>

                        <div class="mt-5">
                            <span class="text-[26px] font-bold text-[#073090]">
                                Rp {{ number_format($item->harga_harian, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-500">
                                / hari
                            </span>
                        </div>

                        <div class="mt-3 text-gray-600">
                            Disewa :
                            <span class="font-semibold text-[#073090]">
                                {{ $item->disewa_count ?? 0 }}x
                            </span>
                        </div>

                        <div class="mt-5 flex gap-3">
                            <a href="{{ route('inventory.show', $item) }}"
                                class="flex-1 rounded-xl bg-blue-100 py-2 text-center">
                                Detail
                            </a>

                            <a href="{{ route('inventory.edit', $item) }}"
                                class="flex-1 rounded-xl bg-yellow-100 py-2 text-center">
                                Edit
                            </a>

                            <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')

                                <button type="submit" onclick="return confirm('Hapus alat?')"
                                    class="w-full rounded-xl bg-red-100 py-2">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 rounded-3xl bg-white p-20 text-center text-gray-500">
                    Belum ada data alat
                </div>
            @endforelse
        </div>

    </div>

@endsection
