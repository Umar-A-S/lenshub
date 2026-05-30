@php
    $statusConfig = match($p->status) {
        'pending'            => ['label'=>'Menunggu Konfirmasi','color'=>'bg-yellow-100 text-yellow-700','icon'=>'⏳'],
        'aktif'              => ['label'=>'Sedang Disewa',      'color'=>'bg-blue-100 text-blue-700',   'icon'=>'✅'],
        'menunggu_pelunasan' => ['label'=>'Perlu Lunasi Denda', 'color'=>'bg-orange-100 text-orange-700','icon'=>'⚠️'],
        'selesai'            => ['label'=>'Selesai',            'color'=>'bg-green-100 text-green-700', 'icon'=>'🎉'],
        'ditolak'            => ['label'=>'Ditolak',            'color'=>'bg-red-100 text-red-700',     'icon'=>'❌'],
        default              => ['label'=>ucfirst($p->status),  'color'=>'bg-slate-100 text-slate-600', 'icon'=>'📦'],
    };
@endphp

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

    {{-- STATUS BAR --}}
    <div class="flex items-center justify-between px-6 py-3 border-b border-slate-100 bg-slate-50">
        <span class="text-xs text-slate-400 font-mono">{{ $p->kode_sewa }}</span>
        <span class="rounded-full {{ $statusConfig['color'] }} px-3 py-1 text-xs font-semibold">
            {{ $statusConfig['icon'] }} {{ $statusConfig['label'] }}
        </span>
    </div>

    {{-- ISI KARTU --}}
    <div class="p-5 flex gap-5">

        {{-- FOTO ALAT --}}
        <div class="w-24 h-24 rounded-2xl bg-slate-100 overflow-hidden shrink-0 flex items-center justify-center">
            @if($p->alat_foto)
                <img src="{{ asset('storage/' . $p->alat_foto) }}" class="w-full h-full object-cover" alt="">
            @else
                <span class="text-3xl">📷</span>
            @endif
        </div>

        {{-- INFO --}}
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-slate-900 text-lg truncate">{{ $p->alat_nama ?: '-' }}</h3>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ match($p->durasi ?? '') {
                    '12jam'=>'12 Jam','1hari'=>'1 Hari','3hari'=>'3 Hari',
                    '5hari'=>'5 Hari','7hari'=>'7 Hari', default=>$p->durasi??'-'
                } }} ·
                {{ $p->logistik === 'cod' ? 'C.O.D' : 'Ambil di Kantor' }}
            </p>

            <div class="mt-2 flex flex-wrap gap-4 text-sm">
                <div>
                    <span class="text-slate-400 text-xs">Mulai Sewa</span>
                    <p class="font-medium text-slate-700">
                        {{ $p->mulai->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">Jatuh Tempo</span>
                    <p class="font-medium text-slate-700">
                        {{ $p->jatuh_tempo->format('d M Y, H:i') }}
                    </p>
                </div>
                @if(in_array($p->status, ['aktif','menunggu_pelunasan']) && isset($p->sisa_waktu))
                    <div>
                        <span class="text-slate-400 text-xs">Sisa Waktu</span>
                        <p class="font-bold {{ $p->waktu_warna === 'red' ? 'text-red-600' : ($p->waktu_warna === 'yellow' ? 'text-yellow-600' : 'text-blue-700') }}">
                            {{ $p->sisa_waktu }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- HARGA --}}
        <div class="text-right shrink-0">
            <p class="text-xs text-slate-400">Total Sewa</p>
            <p class="text-xl font-bold text-[#073090]">Rp {{ number_format($p->total, 0, ',', '.') }}</p>
            @if($p->denda > 0)
                <p class="text-xs text-red-500 mt-1">+ Denda Rp {{ number_format($p->denda, 0, ',', '.') }}</p>
            @endif
            <p class="text-xs mt-1 uppercase font-medium {{ $p->metode_bayar ? 'text-green-600' : 'text-slate-400' }}">
                {{ $p->metode_bayar ?? 'Belum dibayar' }}
            </p>
        </div>
    </div>

    {{-- TRACKING PROGRESS --}}
    @php
        $steps = [
            ['label'=>'Pesanan Masuk'],
            ['label'=>'Dikonfirmasi'],
            ['label'=>'Sedang Disewa'],
            ['label'=>'Selesai'],
        ];
        $activeIdx = match($p->status) {
            'pending'            => 0,
            'aktif'              => 2,
            'menunggu_pelunasan' => 2,
            'selesai'            => 3,
            'ditolak'            => -1,
            default              => 0,
        };
    @endphp

    @if($p->status !== 'ditolak')
        <div class="px-6 pb-5">
            <div class="flex items-center gap-0">
                @foreach($steps as $i => $step)
                    <div class="flex flex-1 items-center {{ $i < count($steps)-1 ? '' : 'flex-none' }}">
                        <div class="flex flex-col items-center">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $i <= $activeIdx ? 'bg-[#073090] text-white' : 'bg-slate-200 text-slate-400' }}">
                                @if($i < $activeIdx) ✓ @else {{ $i + 1 }} @endif
                            </div>
                            <p class="text-xs mt-1 text-center {{ $i <= $activeIdx ? 'text-[#073090] font-medium' : 'text-slate-400' }}" style="width:60px">
                                {{ $step['label'] }}
                            </p>
                        </div>
                        @if($i < count($steps)-1)
                            <div class="flex-1 h-0.5 mx-1 mb-4 {{ $i < $activeIdx ? 'bg-[#073090]' : 'bg-slate-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- STATUS KHUSUS --}}
    @if($p->status === 'pending')
        <div class="mx-5 mb-5 rounded-2xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-700">
            ⏳ Pesanan sedang menunggu konfirmasi admin. Silakan datang ke toko atau tunggu dihubungi via WhatsApp.
        </div>
    @elseif($p->status === 'menunggu_pelunasan')
        <div class="mx-5 mb-5 rounded-2xl bg-orange-50 border border-orange-200 px-4 py-3 text-sm text-orange-700">
            ⚠️ Ada denda yang belum dilunasi sebesar <strong>Rp {{ number_format($p->denda, 0, ',', '.') }}</strong>.
            Silakan segera datang ke toko untuk melunasi dan mengambil jaminan.
        </div>
    @elseif($p->status === 'selesai')
        <div class="mx-5 mb-5 rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            🎉 Transaksi selesai. Terima kasih telah menyewa!
        </div>
        @if($p->denda > 0)
        <div class="mx-5 mb-5 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Rincian Denda</p>
            @if($p->fine?->terlambat && $p->fine->telat_jam > 0)
            <p>Keterlambatan: {{ $p->fine->telat_jam }} jam × Rp {{ number_format($p->fine->tarif_per_jam, 0, ',', '.') }}
               = <strong>Rp {{ number_format($p->fine->telat_jam * $p->fine->tarif_per_jam, 0, ',', '.') }}</strong></p>
            @endif
            @if($p->fine?->biaya_kerusakan > 0)
            <p>Kerusakan{{ $p->fine->deskripsi_kerusakan ? ' ('.$p->fine->deskripsi_kerusakan.')' : '' }}:
               <strong>Rp {{ number_format($p->fine->biaya_kerusakan, 0, ',', '.') }}</strong></p>
            @endif
            <p class="mt-1 font-bold">Total Denda: Rp {{ number_format($p->denda, 0, ',', '.') }}
                <span class="font-normal text-xs">({{ $p->status_denda === 'lunas' ? '✅ Lunas' : '⏳ Belum Lunas' }})</span>
            </p>
        </div>
        @endif
    @elseif($p->status === 'ditolak')
        <div class="mx-5 mb-5 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            ❌ Pesanan ini ditolak oleh admin. Silakan hubungi kami untuk informasi lebih lanjut.
        </div>
    @endif

</div>