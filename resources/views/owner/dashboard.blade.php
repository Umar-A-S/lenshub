@extends('layouts.admin')

@section('title', 'Owner Dashboard')
@section('subtitle', 'Kontrol penuh sistem LensHub')

@section('content')

<div class="grid grid-cols-4 gap-6 mb-10">

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Total Pendapatan</p>
        <h3 class="text-3xl font-bold mt-3">Rp 42,000,000</h3>
        <p class="text-xs text-green-600 mt-2">Bulan ini</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Total Pengguna</p>
        <h3 class="text-3xl font-bold mt-3">128</h3>
        <p class="text-xs text-blue-600 mt-2">+12 bulan ini</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Total Admin</p>
        <h3 class="text-3xl font-bold mt-3">3</h3>
        <p class="text-xs text-slate-400 mt-2">Aktif</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-slate-500">Total Equipment</p>
        <h3 class="text-3xl font-bold mt-3">100</h3>
        <p class="text-xs text-slate-400 mt-2">Di semua kategori</p>
    </div>

</div>

<div class="bg-white rounded-3xl p-8 shadow-sm">
    <h2 class="text-xl font-bold text-slate-800 mb-4">Akses Owner</h2>
    <p class="text-slate-500">Halaman ini hanya dapat diakses oleh <span class="font-semibold text-blue-700">Owner</span>. Di sini Anda bisa mengelola seluruh sistem, laporan keuangan, dan manajemen akun admin.</p>
</div>

@endsection
