@extends('layouts.admin')

@section('title', 'Inventory')
@section('subtitle', 'Hari ini, ' . now()->translatedFormat('d F Y'))

@section('content')

<div class="p-6 space-y-6">

    @if (session('success'))
        <div class="rounded-[var(--border-radius-card)] bg-[#DCFCE7] border border-[#BBF7D0] px-6 py-4 text-[#166534] shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter Section --}}
    <form action="{{ route('inventory.index') }}" method="GET" class="bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-6 shadow-sm border border-[var(--border-default)]">
        <div class="flex flex-wrap gap-4 items-end">
            {{-- Search Input --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[var(--fs-small)] font-semibold text-[var(--text-secondary)] mb-2 uppercase">
                    <i class="fas fa-search mr-2"></i>Cari Alat
                </label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Nama atau deskripsi alat..."
                    class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition-all duration-200">
            </div>

            {{-- Category Filter --}}
            <div class="w-full sm:w-48">
                <label class="block text-[var(--fs-small)] font-semibold text-[var(--text-secondary)] mb-2 uppercase">
                    <i class="fas fa-tag mr-2"></i>Kategori
                </label>
                <select name="category" class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition-all duration-200">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="w-full sm:w-48">
                <label class="block text-[var(--fs-small)] font-semibold text-[var(--text-secondary)] mb-2 uppercase">
                    <i class="fas fa-filter mr-2"></i>Status
                </label>
                <select name="status" class="w-full px-4 py-2.5 border border-[var(--border-default)] rounded-[var(--border-radius-btn)] focus:border-[var(--color-primary)] outline-none transition-all duration-200">
                    <option value="">Semua</option>
                    <option value="tersedia" @selected(request('status') === 'tersedia')>
                        Tersedia
                    </option>
                    <option value="tidak tersedia" @selected(request('status') === 'tidak tersedia')>
                        Tidak Tersedia
                    </option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-[var(--bg-sidebar)] hover:bg-[#1e2a5e]/90 text-white font-semibold py-2.5 px-6 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('inventory.index') }}" class="bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[var(--text-secondary)] font-semibold py-2.5 px-6 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-redo"></i>
                </a>
                <a href="{{ route('inventory.create') }}" class="bg-[var(--bg-sidebar)] hover:bg-[#1e2a5e]/90 text-white font-semibold py-2.5 px-6 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
    </form>

    {{-- Equipment Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($equipments as $item)
            <div class="group bg-[var(--bg-card)] rounded-[var(--border-radius-card)] p-0 shadow-sm border border-[var(--border-default)] hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
                {{-- Image Container --}}
                <div class="relative h-40 bg-[#F3F4F6] overflow-hidden flex items-center justify-center">
                    @if ($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                            class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="text-[var(--text-muted)] text-[var(--fs-small)]">Tidak ada gambar</div>
                    @endif
                    
                    {{-- Status Badge --}}
                    <div class="absolute top-2 right-2">
                        @if($item->stok_tersedia > 0)
                            <span class="inline-flex items-center gap-1 bg-[var(--bg-sidebar)] text-white text-[var(--fs-small)] font-bold px-2 py-1 rounded-[var(--border-radius-badge)] shadow-md">
                                <i class="fas fa-check-circle text-[var(--fs-small)]"></i> <span class="hidden sm:inline">Tersedia</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-[#EF4444] text-white text-[var(--fs-small)] font-bold px-2 py-1 rounded-[var(--border-radius-badge)] shadow-md">
                                <i class="fas fa-times-circle text-[var(--fs-small)]"></i> <span class="hidden sm:inline">Habis</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Content Container --}}
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="text-[var(--fs-body)] font-bold text-[var(--text-primary)] line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors">
                        {{ $item->nama }}
                    </h3>
                    <p class="text-[var(--fs-small)] text-[var(--color-primary)] font-semibold mt-1">
                        <i class="fas fa-tag mr-1"></i>{{ $item->category?->nama ?? '-' }}
                    </p>
                    <p class="text-[var(--fs-small)] text-[var(--text-secondary)] line-clamp-1 mb-3">
                        {{ $item->deskripsi ?? 'Tidak ada deskripsi' }}
                    </p>

                    <div class="mb-3 pb-3 border-b border-[var(--border-default)]">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-[var(--fs-small)] font-semibold text-[var(--text-primary)]">
                                <i class="fas fa-cubes mr-1"></i>Stok
                            </p>
                            <span class="text-[var(--fs-small)] font-bold text-[var(--text-secondary)]">
                                {{ $item->stok_tersedia }}/{{ $item->stok }}
                            </span>
                        </div>
                        <div class="h-1.5 rounded-[var(--border-radius-badge)] bg-[#E5E7EB] overflow-hidden">
                            <div class="h-full rounded-[var(--border-radius-badge)] bg-[var(--color-primary)] transition-all duration-500"
                                style="width: {{ $item->stok > 0 ? ($item->stok_tersedia / $item->stok) * 100 : 0 }}%;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="text-[var(--fs-body)] font-bold text-[var(--color-primary)] font-mono-numbers">
                            Rp {{ number_format($item->harga_harian, 0, ',', '.') }}
                        </span>
                        <span class="text-[var(--fs-small)] text-[var(--text-muted)]">/ hari</span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-auto flex gap-2 pt-4">
                        <a href="{{ route('inventory.show', $item) }}"
                            class="flex-1 bg-[#E0E7FF] hover:bg-[#C7D2FE] text-[var(--bg-sidebar)] py-2 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm flex items-center justify-center gap-2 text-sm font-semibold">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('inventory.edit', $item) }}"
                            class="flex-1 bg-[var(--bg-sidebar)] hover:bg-[#1e2a5e]/90 text-white py-2 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm flex items-center justify-center gap-2 text-sm font-semibold">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] py-2 rounded-[var(--border-radius-btn)] transition-all duration-300 shadow-sm flex items-center justify-center gap-2 text-sm font-semibold">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-[var(--border-radius-card)] bg-[var(--bg-card)] p-20 text-center text-[var(--text-muted)]">
                Belum ada data alat
            </div>
        @endforelse
    </div>
</div>
@endsection
