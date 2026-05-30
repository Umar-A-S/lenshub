@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl py-4">
    <div class="rounded-3xl bg-white p-10 shadow">
        <div class="flex items-center gap-4 mb-2">
            <div class="h-12 w-12 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">📋</div>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Rules & Syarat Ketentuan</h1>
                <p class="text-slate-500 text-sm mt-0.5">Perjanjian antara pengguna dan sistem LensHub</p>
            </div>
        </div>

        <div class="mt-2 mb-8 h-1 w-16 rounded-full bg-[#073090]"></div>

        {{-- Bab 1 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">1</span>
                Ketentuan Umum
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pengguna wajib berusia minimal 17 tahun dan memiliki identitas resmi (KTP/SIM/KTM/Paspor) yang masih berlaku.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Satu akun hanya boleh digunakan oleh satu orang dan tidak boleh dipinjamkan kepada pihak lain.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> LensHub berhak menonaktifkan atau menghapus akun yang terbukti melanggar ketentuan tanpa pemberitahuan sebelumnya.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pengguna wajib memastikan data profil (nama, nomor WhatsApp, dll.) selalu akurat dan terkini.</li>
            </ul>
        </div>

        {{-- Bab 2 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">2</span>
                Persyaratan & Proses Penyewaan
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pengguna wajib menyerahkan jaminan fisik (KTP, SIM, KTM, Paspor, atau dokumen lain yang disepakati) saat pengambilan alat.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pembayaran biaya sewa dilakukan di awal sebelum alat diserahkan kepada penyewa.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pesanan dianggap aktif setelah admin LensHub melakukan konfirmasi resmi.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Alat hanya boleh digunakan untuk keperluan fotografi/videografi yang legal dan tidak melanggar hukum.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Penyewa dilarang meminjamkan, menyewakan kembali, atau mengalihkan alat kepada pihak ketiga.</li>
            </ul>
        </div>

        {{-- Bab 3 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">3</span>
                Pengembalian Alat
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Alat wajib dikembalikan tepat waktu sesuai tanggal dan jam jatuh tempo yang tertera di pesanan.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Alat wajib dikembalikan dalam kondisi bersih, lengkap, dan berfungsi dengan baik sebagaimana saat dipinjam.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Semua aksesori bawaan alat wajib dikembalikan secara lengkap.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Jaminan fisik hanya akan dikembalikan setelah semua kewajiban (termasuk denda, jika ada) diselesaikan.</li>
            </ul>
        </div>

        {{-- Bab 4 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">4</span>
                Denda & Kerusakan
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Keterlambatan pengembalian dikenakan denda per jam sesuai tarif yang telah disepakati saat konfirmasi.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Penyewa bertanggung jawab penuh atas kerusakan, kehilangan, atau cacat pada alat selama masa sewa.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Biaya perbaikan atau penggantian alat dibebankan kepada penyewa sesuai estimasi dari pihak LensHub.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Denda wajib dilunasi sebelum jaminan fisik dikembalikan kepada penyewa.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Transaksi dinyatakan selesai setelah semua denda terlunasi.</li>
            </ul>
        </div>

        {{-- Bab 5 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">5</span>
                Pembatalan Pesanan
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pembatalan pesanan hanya dapat dilakukan selama status masih "Menunggu Konfirmasi" (pending).</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pesanan yang sudah dikonfirmasi admin tidak dapat dibatalkan secara sepihak oleh pengguna.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Untuk pembatalan darurat pada pesanan aktif, pengguna wajib menghubungi admin LensHub secara langsung.</li>
            </ul>
        </div>

        {{-- Bab 6 --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-[#073090] mb-3 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-[#073090] text-white text-sm flex items-center justify-center font-bold shrink-0">6</span>
                Privasi & Keamanan Data
            </h2>
            <ul class="space-y-2 text-slate-700 text-sm pl-4">
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Data pribadi pengguna disimpan secara aman dan tidak akan dibagikan kepada pihak ketiga tanpa persetujuan.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Nomor WhatsApp digunakan khusus untuk pengiriman notifikasi transaksi sewa dari LensHub.</li>
                <li class="flex gap-2"><span class="text-[#073090] mt-0.5">•</span> Pengguna bertanggung jawab menjaga kerahasiaan kata sandi akun masing-masing.</li>
            </ul>
        </div>

        <div class="mt-10 rounded-2xl bg-blue-50 border border-blue-200 px-6 py-4 text-center text-sm text-[#073090]">
            Dengan melakukan pemesanan di LensHub, Anda dianggap telah membaca, memahami, dan menyetujui seluruh rules di atas.
        </div>
    </div>
</div>
@endsection
