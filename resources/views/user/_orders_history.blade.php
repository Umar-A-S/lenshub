{{-- resources/views/user/_orders_history.blade.php --}}
{{-- Partial view: dirender server-side untuk realtime injection via JS --}}

@forelse($history as $p)
    @include('user._order_card', ['p' => $p])
@empty
    <div class="bg-white rounded-3xl p-16 text-center shadow-sm">
        <p class="text-5xl mb-4">📦</p>
        <p class="text-lg font-semibold text-slate-700">Belum ada riwayat</p>
        <p class="text-slate-400 mt-1 mb-6">Pesanan yang sudah selesai atau ditolak akan muncul di sini.</p>
        <a href="{{ route('produk.index') }}"
            class="inline-block rounded-2xl bg-[#073090] px-8 py-3 text-white font-semibold hover:bg-blue-800 transition">
            Mulai Sewa
        </a>
    </div>
@endforelse
