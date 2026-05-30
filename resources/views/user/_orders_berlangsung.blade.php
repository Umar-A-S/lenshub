{{-- resources/views/user/_orders_berlangsung.blade.php --}}
{{-- Partial view: dirender server-side untuk realtime injection via JS --}}

@if($pending->count() > 0)
    <div>
        <h3 class="text-sm font-semibold text-yellow-700 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
            Menunggu Konfirmasi Admin
        </h3>
        <div class="space-y-4">
            @foreach($pending as $p)
                @include('user._order_card', ['p' => $p])
            @endforeach
        </div>
    </div>
@endif

@if($aktif->count() > 0)
    <div>
        <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
            Sedang Disewa
        </h3>
        <div class="space-y-4">
            @foreach($aktif as $p)
                @include('user._order_card', ['p' => $p])
            @endforeach
        </div>
    </div>
@endif

@if($proses->count() > 0)
    <div>
        <h3 class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
            Perlu Dilunasi
        </h3>
        <div class="space-y-4">
            @foreach($proses as $p)
                @include('user._order_card', ['p' => $p])
            @endforeach
        </div>
    </div>
@endif

@if($pending->isEmpty() && $aktif->isEmpty() && $proses->isEmpty())
    <div class="bg-white rounded-3xl p-16 text-center shadow-sm">
        <p class="text-5xl mb-4">✅</p>
        <p class="text-lg font-semibold text-slate-700">Tidak ada pesanan aktif</p>
        <p class="text-slate-400 mt-1 mb-6">Semua pesananmu sudah selesai. Yuk sewa lagi!</p>
        <a href="{{ route('produk.index') }}"
            class="inline-block rounded-2xl bg-[#073090] px-8 py-3 text-white font-semibold hover:bg-blue-800 transition">
            Lihat Produk
        </a>
    </div>
@endif
