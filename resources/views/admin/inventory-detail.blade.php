@extends('layouts.admin')

@section('title', 'Detail Inventory')

@section('content')

    <div class="mx-auto max-w-6xl">

        <div class="mb-8 flex items-center gap-4">

            <a href="{{ route('inventory.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 font-medium text-slate-900 shadow transition-colors hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>

            <h1 class="text-4xl font-bold text-white">
                Detail Inventory
            </h1>

        </div>


        <div class="grid grid-cols-2 gap-8 rounded-3xl border border-slate-200 bg-white p-8">

            {{-- FOTO --}}
            <div class="h-[450px] overflow-hidden rounded-3xl border border-slate-200 bg-slate-100 p-3">

                @if ($equipment->foto)
                    <img src="{{ asset('storage/' . $equipment->foto) }}" class="h-full w-full rounded-2xl object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-gray-400">
                        Gambar
                    </div>
                @endif

            </div>


            {{-- INFO --}}
            <div>

                <h2 class="text-5xl font-bold">
                    {{ $equipment->nama }}
                </h2>

                <p class="mt-3 text-gray-500">
                    {{ $equipment->category->nama }}
                </p>

                <p class="mt-8">
                    {{ $equipment->deskripsi }}
                </p>

                <div class="mt-8 space-y-3">

                    <p>
                        Total :
                        <b>{{ $equipment->stok }}</b>
                    </p>

                    <p>
                        Harga :
                        <b>
                            Rp
                            {{ number_format($equipment->harga_harian, 0, ',', '.') }}
                        </b>
                    </p>

                </div>
            </div>

        </div>

    </div>

@endsection
