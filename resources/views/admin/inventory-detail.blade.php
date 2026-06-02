@extends('layouts.admin')

@section('title', 'Detail Alat')
@section('subtitle', $equipment->nama)

@section('content')

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Back Button --}}
        <a href="{{ route('inventory.index') }}"
            class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Inventory</span>
        </a>

        {{-- Main Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                {{-- Image Section --}}
                <div class="bg-slate-100 p-6 sm:p-8 flex items-center justify-center min-h-96">
                    @if ($equipment->foto)
                        <img src="{{ asset('storage/' . $equipment->foto) }}" alt="{{ $equipment->nama }}"
                            class="w-full h-full object-cover rounded-lg">
                    @else
                        <div class="text-center text-slate-400">
                            <i class="fas fa-image text-6xl mb-4 block"></i>
                            <p>Tidak ada gambar</p>
                        </div>
                    @endif
                </div>

                {{-- Info Section --}}
                <div class="p-6 sm:p-8 flex flex-col justify-between">

                    {{-- Title & Category --}}
                    <div>
                        <div class="mb-4">
                            <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-2">
                                {{ $equipment->nama }}
                            </h1>
                            <div class="flex items-center gap-2 text-blue-600 font-semibold">
                                <i class="fas fa-tag"></i>
                                <span>{{ $equipment->category?->nama ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="my-6 pb-6 border-b border-slate-200">
                            <p class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-left text-blue-600"></i>Deskripsi
                            </p>
                            <p class="text-slate-600 leading-relaxed">
                                {{ $equipment->deskripsi ?? 'Tidak ada deskripsi' }}
                            </p>
                        </div>

                        {{-- Details Grid --}}
                        <div class="space-y-4">

                            {{-- Total Stock --}}
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1 flex items-center gap-2">
                                    <i class="fas fa-boxes"></i>Total Stok
                                </p>
                                <p class="text-2xl font-bold text-blue-900">
                                    {{ $equipment->stok }} Unit
                                </p>
                            </div>

                            {{-- Available Stock --}}
                            <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide mb-1 flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>Stok Tersedia
                                </p>
                                <p class="text-2xl font-bold text-emerald-900">
                                    {{ $equipment->stok_tersedia }} Unit
                                </p>
                                <div class="mt-3 h-2 rounded-full bg-emerald-200 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full"
                                        style="width: {{ $equipment->stok > 0 ? ($equipment->stok_tersedia / $equipment->stok) * 100 : 0 }}%;"></div>
                                </div>
                            </div>

                            {{-- Daily Price --}}
                            <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
                                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1 flex items-center gap-2">
                                    <i class="fas fa-money-bill"></i>Harga per Hari
                                </p>
                                <p class="text-2xl font-bold text-amber-900">
                                    Rp {{ number_format($equipment->harga_harian, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Rental Count --}}
                            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1 flex items-center gap-2">
                                    <i class="fas fa-history"></i>Sudah Disewa
                                </p>
                                <p class="text-2xl font-bold text-purple-900">
                                    {{ $equipment->disewa_count ?? 0 }} kali
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-slate-200">
                        <a href="{{ route('inventory.edit', $equipment) }}"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i>
                            <span>Edit Alat</span>
                        </a>
                        <form action="{{ route('inventory.destroy', $equipment) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                <span>Hapus Alat</span>
                            </button>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection
