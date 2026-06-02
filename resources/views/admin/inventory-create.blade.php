@extends('layouts.admin')

@section('title', 'Tambah Alat')
@section('subtitle', 'Input data alat baru ke inventory')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sm:p-8">
            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <i class="fas fa-plus-circle text-blue-600"></i>
                    Form Tambah Alat
                </h2>
                <p class="text-slate-600 text-sm mt-2">Isi semua data dengan lengkap dan benar</p>
            </div>

            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Kategori --}}
                <div>
                    <label class="block mb-2 font-semibold text-slate-700 text-sm">
                        <i class="fas fa-tag mr-2 text-blue-600"></i>Kategori
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 @error('category_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') 
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- Nama Alat --}}
                <div>
                    <label class="block mb-2 font-semibold text-slate-700 text-sm">
                        <i class="fas fa-cube mr-2 text-blue-600"></i>Nama Alat
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Kamera Canon EOS R5"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 @error('nama') border-red-500 @enderror">
                    @error('nama') 
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block mb-2 font-semibold text-slate-700 text-sm">
                        <i class="fas fa-align-left mr-2 text-blue-600"></i>Deskripsi
                    </label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan spesifikasi dan fitur dari alat ini..." 
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') 
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- Stok & Harga (2 kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 font-semibold text-slate-700 text-sm">
                            <i class="fas fa-boxes mr-2 text-blue-600"></i>Total Stok
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stok" value="{{ old('stok') }}" placeholder="Jumlah alat" min="1"
                            class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 @error('stok') border-red-500 @enderror">
                        @error('stok') 
                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>{{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold text-slate-700 text-sm">
                            <i class="fas fa-money-bill mr-2 text-blue-600"></i>Harga Per Hari (Rp)
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="harga_harian" value="{{ old('harga_harian') }}" placeholder="Contoh: 500000" min="0"
                            class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 @error('harga_harian') border-red-500 @enderror">
                        @error('harga_harian') 
                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>{{ $message }}
                            </p> 
                        @enderror
                    </div>
                </div>

                {{-- Foto --}}
                <div>
                    <label class="block mb-2 font-semibold text-slate-700 text-sm">
                        <i class="fas fa-image mr-2 text-blue-600"></i>Foto Alat
                    </label>
                    <div class="relative">
                        <input type="file" name="foto" accept="image/*" id="fotoInput"
                            class="w-full h-full border-2 border-dashed border-slate-300 rounded-lg px-4 py-8 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 cursor-pointer hover:border-blue-400 @error('foto') border-red-500 @enderror">
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 mb-1 mt-4"></i>
                            <p class="text-sm text-slate-600 mt-6">Klik atau drag foto di sini</p>
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                        </div>
                    </div>
                    @error('foto') 
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Alat</span>
                    </button>
                    <a href="{{ route('inventory.index') }}" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection