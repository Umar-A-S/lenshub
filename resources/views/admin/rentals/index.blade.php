<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penalty & Transaction Test Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-calculator me-2 text-primary"></i>Penalty & Lifecycle Lab</h2>

    <div class="alert alert-info shadow-sm border-0">
        <strong>Cara Tes:</strong> 
        1. Buat transaksi (via Seeder/Tinker). 
        2. Klik tombol <strong>"Simulasi Telat"</strong>. 
        3. Refresh halaman, lihat apakah <strong>Denda</strong> otomatis bertambah.
    </div>

    <div class="table-responsive">
        <table class="table table-hover bg-white rounded shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Pelanggan</th>
                    <th>Alat</th>
                    <th>Deadline</th>
                    <th>Denda Saat Ini</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rentals as $rent)
                <tr class="align-middle">
                    <td>{{ $rent->user->name }}</td>
                    <td><span class="badge bg-secondary">{{ $rent->gear->unit_code }}</span> {{ $rent->gear->name }}</td>
                    <td>{{ $rent->end_date->format('d M Y, H:i') }}</td>
                    <td>
                        <span class="fw-bold {{ $rent->penalty > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($rent->penalty) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $rent->status == 'active' ? 'bg-primary' : 'bg-success' }}">
                            {{ strtoupper($rent->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            @if($rent->status == 'active')
                                <form action="{{ route('rentals.simulate', $rent->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-warning" title="Simulasi Telat 2 Hari">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('rentals.return', $rent->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        <i class="fas fa-undo me-1"></i> Kembalikan
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-light" disabled>Selesai</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

TABEL RENTAL TERLAMBAT -->
<!-- <h2>⚠️ Rental Terlambat</h2>
<table border="1">
    <thead>
        <tr>
            <th>Gear</th>
            <th>Mulai</th>
            <th>Selesai (Deadline)</th>
            <th>Keterlambatan</th>
            <th>Total Denda</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rentals as $rental)
            {{-- Cek apakah denda > 0 menggunakan Accessor dari Model --}}
            @if($rental->penalty_details['is_late'])
            <tr>
                <td>{{ $rental->gear->name }}</td>
                <td>{{ $rental->start_date->format('d M Y, H:i') }}</td>
                <td>{{ $rental->end_date->format('d M Y, H:i') }}</td>
                
                {{-- Memanggil 'days' dari Accessor penalty_details --}}
                <td style="color: red;">
                    Telat {{ $rental->penalty_details['days'] }} Hari {{ $rental->penalty_details['hours'] }} Jam
                </td>

                {{-- Memanggil 'total' dari Accessor penalty_details --}}
                <td>Rp {{ number_format($rental->penalty_details['total'], 0, ',', '.') }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>

<hr>

<!-- TABEL RENTAL TEPAT WAKTU -->
<!-- <h2>✅ Rental Aman</h2>
<table border="1">
    <thead>
        <tr>
            <th>Gear</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rentals as $rental)
            @if(!$rental->penalty_details['is_late'])
            <tr>
                <td>{{ $rental->gear->name }}</td>
                <td>{{ $rental->start_date->format('d M Y, H:i') }}</td>
                <td>{{ $rental->end_date->format('d M Y, H:i') }}</td>
                <td><span style="color: green;">Tepat Waktu</span></td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
</body>
</html> --> 

<x-app-layout>
    <x-slot name=header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Rental') }}
        </h2>
    </x-slot>
</x-app-layout>