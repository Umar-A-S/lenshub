@extends('layouts.public')

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <div class="rounded-3xl bg-white p-10 shadow text-center">
        <div class="text-6xl mb-5">✅</div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Pesanan Terkirim!</h1>
        <p class="text-slate-500 mb-6">Pesanan Anda sedang menunggu persetujuan admin. Silakan datang ke toko atau tunggu konfirmasi.</p>

        <div class="rounded-2xl bg-blue-50 px-6 py-5 text-left space-y-2 mb-6">
            <p class="text-sm text-slate-500">Kode Sewa</p>
            <p class="text-xl font-bold text-blue-700">{{ $rental->kode_sewa }}</p>
            <hr class="border-blue-100 my-2">
            <p class="text-sm"><span class="text-slate-500">Nama:</span> <span class="font-medium">{{ $rental->nama_penyewa }}</span></p>
            <p class="text-sm"><span class="text-slate-500">Durasi:</span>
                <span class="font-medium">{{ match($rental->durasi) {
                    '12jam'=>'12 Jam','1hari'=>'1 Hari','3hari'=>'3 Hari',
                    '5hari'=>'5 Hari','7hari'=>'7 Hari', default=>$rental->durasi
                } }}</span>
            </p>
            <p class="text-sm"><span class="text-slate-500">Mulai:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($rental->mulai)->format('d M Y H:i') }}</span></p>
            <p class="text-sm"><span class="text-slate-500">Logistik:</span> <span class="font-medium">{{ $rental->logistik === 'cod' ? 'C.O.D' : 'Ambil di Kantor' }}</span></p>
            <p class="text-sm"><span class="text-slate-500">Total:</span> <span class="font-bold text-blue-700">Rp {{ number_format($rental->total, 0, ',', '.') }}</span></p>
            <p class="text-sm"><span class="text-slate-500">Status:</span> <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-700 font-semibold">PENDING</span></p>
        </div>

        <a href="{{ route('home') }}"
            class="block w-full rounded-2xl bg-[#073090] py-4 text-white font-semibold text-lg hover:bg-blue-800 transition">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
