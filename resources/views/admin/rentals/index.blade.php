<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-xl font-bold mb-4 text-slate-800">Permohonan Sewa Baru</h2>
    
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-slate-50 text-xs uppercase text-slate-500">
                <th class="p-3">Kode</th>
                <th class="p-3">Customer</th>
                <th class="p-3">Alat</th>
                <th class="p-3">Total</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($permohonan as $item)
            <tr class="border-b hover:bg-slate-50">
                <td class="p-3 font-bold text-teal-600">{{ $item->booking_code }}</td>
                <td class="p-3">{{ $item->user->name }}</td>
                <td class="p-3">{{ $item->gear->name }}</td>
                <td class="p-3">Rp {{ number_format($item->total_price) }}</td>
                <td class="p-3 flex justify-center gap-2">
                    <form action="{{ route('rentals.confirm', $item->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="bg-teal-500 text-white px-3 py-1 rounded-lg text-xs font-bold">
                            Konfirmasi Pembayaran
                        </button>
                    </form>

                    <form action="{{ route('rentals.cancel', $item->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="border-2 border-red-500 text-red-500 px-3 py-1 rounded-lg text-xs font-bold">
                            Batalkan
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>