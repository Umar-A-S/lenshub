{{-- resources/views/admin/permintaan_rows.blade.php --}}
{{-- Partial view: hanya <tr> rows — diinject realtime ke <tbody id="tabel-permintaan"> --}}

@forelse ($permintaan as $p)
    <tr class="border-b border-[var(--border-default)] hover:bg-[#F9FAFB] even:bg-[#F9FAFB]/50 transition-colors text-center">
        <td class="px-1 py-1 font-semibold text-[var(--color-primary)] text-xs">{{ $p->kode_sewa }}</td>
        <td class="px-5 py-5 text-xs text-[var(--text-primary)] font-medium">{{ $p->nama_penyewa }}</td>
        <td class="px-5 py-5 text-xs text-[var(--text-secondary)]">{{ $p->whatsapp }}</td>
        <td class="px-5 py-5 text-xs font-bold text-[var(--text-primary)]">{{ $p->alat_nama ?: '-' }}</td>
        <td class="px-5 py-5">
            <span class="inline-block rounded-[var(--border-radius-badge)] text-xs text-[#2563EB] uppercase font-bold tracking-wider">
                {{ match($p->durasi) {
                    '12jam' => '12 Jam', '1hari' => '1 Hari',
                    '3hari' => '3 Hari', '5hari' => '5 Hari',
                    '7hari' => '7 Hari', default => $p->durasi
                } }}
            </span>
        </td>
        <td class="px-1 py-1 text-xs text-[var(--text-secondary)]">{{ \Carbon\Carbon::parse($p->mulai)->format('d M Y H:i') }}</td>
        <td class="px-5 py-5">
            @if($p->logistik === 'cod')
                <div class="inline-block">
                    <span class="rounded-[var(--border-radius-badge)] text-[#92400E] px-3 py-1 text-xs font-bold uppercase tracking-wider">
                        C.O.D
                    </span>
                    @if($p->alamat_pengiriman)
                        <p class="mt-2 text-xs text-[var(--text-secondary)] max-w-[200px] leading-snug">
                            {{ $p->alamat_pengiriman }}
                        </p>
                    @else
                        <p class="mt-2 text-xs text-[var(--color-accent-red)] italic">Alamat belum diisi</p>
                    @endif
                </div>
            @else
                <span class="inline-block rounded-[var(--border-radius-badge)] text-[#16A34A] px-1 py-1 text-xs font-bold uppercase tracking-wider">
                    Ambil di Kantor
                </span>
            @endif
        </td>
        <td class="px-1 py-1 font-bold text-[var(--text-primary)] font-mono-numbers text-xs">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
        <td class="px-5 py-5">
            <div class="flex flex-col gap-1 items-center">
                <button
                    onclick="openKonfirmasi({{ $p->id }}, '{{ addslashes($p->nama_penyewa) }}', '{{ addslashes($p->alat_nama) }}', '{{ number_format($p->total, 0, ',', '.') }}')"
                    class="rounded-[var(--border-radius-btn)] bg-[var(--bg-sidebar)] hover:bg-[#1e2a5e]/90 px-2 py-1 text-xs text-white font-semibold transition w-full">
                    Proses Konfirmasi
                </button>
                <button
                    onclick="tolakPermintaan({{ $p->id }})"
                    class="rounded-[var(--border-radius-btn)] bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] px-2 py-1 text-xs font-semibold transition w-full">
                    Tolak
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="px-5 py-16 text-center text-[var(--text-muted)]">
            <p class="text-4xl mb-3">📭</p>
            <p class="text-[var(--fs-body)]">Belum ada permintaan pesanan masuk</p>
        </td>
    </tr>
@endforelse
