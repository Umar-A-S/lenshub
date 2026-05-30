{{-- resources/views/admin/permintaan_rows.blade.php --}}
{{-- Partial view: hanya <tr> rows — diinject realtime ke <tbody id="tabel-permintaan"> --}}

@forelse ($permintaan as $p)
    <tr class="border-t border-slate-100 hover:bg-slate-50">
        <td class="px-5 py-4 font-medium text-blue-700">{{ $p->kode_sewa }}</td>
        <td class="px-5 py-4 font-medium">{{ $p->nama_penyewa }}</td>
        <td class="px-5 py-4 text-slate-500">{{ $p->whatsapp }}</td>
        <td class="px-5 py-4">{{ $p->alat_nama ?: '-' }}</td>
        <td class="px-5 py-4">
            <span class="rounded-full bg-blue-50 px-2 py-1 text-xs text-blue-700">
                {{ match($p->durasi) {
                    '12jam' => '12 Jam', '1hari' => '1 Hari',
                    '3hari' => '3 Hari', '5hari' => '5 Hari',
                    '7hari' => '7 Hari', default => $p->durasi
                } }}
            </span>
        </td>
        <td class="px-5 py-4">{{ \Carbon\Carbon::parse($p->mulai)->format('d M Y H:i') }}</td>
        <td class="px-2 py-4">
            @if($p->logistik === 'cod')
                <div>
                    <span class="rounded-full bg-orange-50 text-orange-700 px-2 py-1 text-xs font-medium">
                        C.O.D
                    </span>
                    @if($p->alamat_pengiriman)
                        <p class="mt-1 text-xs text-slate-500 max-w-[160px] leading-snug">
                            {{ $p->alamat_pengiriman }}
                        </p>
                    @else
                        <p class="mt-1 text-xs text-red-400 italic">Alamat belum diisi</p>
                    @endif
                </div>
            @else
                <span class="bg-green-50 text-green-700 px-2 py-1 text-xs font-medium">
                    Ambil di Kantor
                </span>
            @endif
        </td>
        <td class="px-5 py-4 font-semibold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
        <td class="px-5 py-4">
            <div class="flex gap-2">
                <button
                    onclick="openKonfirmasi({{ $p->id }}, '{{ addslashes($p->nama_penyewa) }}', '{{ addslashes($p->alat_nama) }}', '{{ number_format($p->total, 0, ',', '.') }}')"
                    class="rounded-xl bg-[#073090] px-4 py-2 text-xs text-white hover:bg-blue-800 transition">
                    Proses Konfirmasi
                </button>
                <button
                    onclick="tolakPermintaan({{ $p->id }})"
                    class="rounded-xl bg-red-50 px-4 py-2 text-xs text-red-600 hover:bg-red-100 transition">
                    Tolak
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="px-5 py-16 text-center text-slate-400">
            <p class="text-4xl mb-3">📭</p>
            <p>Belum ada permintaan pesanan masuk</p>
        </td>
    </tr>
@endforelse
