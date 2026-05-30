@extends('layouts.admin')

@section('title', 'Edit Alat')
@section('subtitle', 'Ubah data alat')

@section('content')

    <div class="flex justify-center">

        <div class="w-full max-w-4xl rounded-3xl bg-white p-10 shadow-lg">

            <form action="{{ route('inventory.update', $equipment) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">

                @csrf
                @method('PUT')


                {{-- Nama --}}
                <div>

                    <label class="font-semibold">
                        Nama Alat
                    </label>

                    <input type="text" name="nama" value="{{ old('nama', $equipment->nama) }}"
                        class="mt-2 w-full rounded-xl border p-4" required>

                </div>


                {{-- Deskripsi --}}
                <div>

                    <label class="font-semibold">
                        Deskripsi
                    </label>

                    <textarea name="deskripsi" rows="6" class="mt-2 w-full rounded-xl border p-4">{{ old('deskripsi', $equipment->deskripsi) }}</textarea>

                </div>


                {{-- Stok + Harga --}}
                <div class="grid grid-cols-2 gap-6">

                    <div>

                        <label class="font-semibold">
                            Jumlah Barang
                        </label>

                        <input type="number" name="stok" value="{{ old('stok', $equipment->stok) }}"
                            class="mt-2 w-full rounded-xl border p-4" required>

                    </div>


                    <div>

                        <label class="font-semibold">
                            Harga Harian
                        </label>

                        <input type="number" name="harga_harian"
                            value="{{ old('harga_harian', $equipment->harga_harian) }}"
                            class="mt-2 w-full rounded-xl border p-4" required>

                    </div>

                </div>


                {{-- Foto --}}
                <div>

                    <label class="font-semibold">
                        Foto Baru
                    </label>

                    <input type="file" name="foto" class="mt-2 w-full rounded-xl border p-4">

                </div>


                {{-- Preview --}}
                @if ($equipment->foto)
                    <div>

                        <p class="mb-3 font-semibold">
                            Foto Saat Ini
                        </p>

                        <img src="{{ asset('storage/' . $equipment->foto) }}" class="w-56 rounded-2xl border border-slate-200 p-2 bg-white">

                    </div>
                @endif


                {{-- Tombol --}}
                <div class="flex gap-4">

                    <button type="submit" class="rounded-xl bg-[#073090] px-8 py-3 text-white">
                        Update
                    </button>


                    <a href="{{ route('inventory.index') }}" class="rounded-xl bg-slate-200 px-8 py-3">
                        Batal
                    </a>

                </div>

            </form>

        </div>

    </div>

@endsection
