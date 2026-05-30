@extends('layouts.admin')

@section('title', 'Tambah Alat')
@section('subtitle', 'Input data alat baru')

@section('content')
    <div class="bg-white rounded-3xl p-8 max-w-3xl">
        <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-2 font-medium">Kategori</label>
                <select name="category_id" class="w-full border rounded-xl px-4 py-3">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-2 font-medium">Nama Alat</label>
                <input type="text" name="nama" class="w-full border rounded-xl px-4 py-3">
                @error('nama') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-2 font-medium">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="w-full border rounded-xl px-4 py-3"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 font-medium">Stok</label>
                    <input type="number" name="stok" class="w-full border rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block mb-2 font-medium">Harga Harian</label>
                    <input type="number" name="harga_harian" class="w-full border rounded-xl px-4 py-3">
                </div>
            </div>

            <div>
                <label class="block mb-2 font-medium">Status</label>
                <select name="status" class="w-full border rounded-xl px-4 py-3">
                    <option value="tersedia">Tersedia</option>
                    <option value="disewa">Disewa</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 font-medium">Foto</label>
                <input type="file" name="foto" class="w-full border rounded-xl px-4 py-3 bg-white">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#073090] text-white px-6 py-3 rounded-xl">
                    Simpan
                </button>
                <a href="{{ route('inventory.index') }}" class="bg-slate-200 px-6 py-3 rounded-xl">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection