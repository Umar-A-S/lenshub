<!DOCTYPE html>
<html>
<head>
    <title>LensHub Debugger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Verifikasi Fitur LensHub</h2>
    
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5>Kontrol Backend</h5>
            <!-- Tombol untuk memicu robot penalty secara manual -->
            <form action="/run-penalty-check" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Jalankan Auto Penalty Sekarang</button>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Penyewa</th>
                <th>Alat</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rentals as $rental)
            <tr>
                <td>{{ $rental->user->name }}</td>
                <td>{{ $rental->gear->name }}</td>
                <td>{{ $rental->end_date->format('d M Y') }}</td>
                <td>
                    <span class="badge {{ $rental->status == 'active' ? 'bg-primary' : 'bg-success' }}">
                        {{ $rental->status }}
                    </span>
                </td>
                <td class="text-danger fw-bold">Rp {{ number_format($rental->penalty_amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    //Lihat isi semua data
    <pre>{{ json_encode($rentals, JSON_PRETTY_PRINT) }}</pre>
</body>
</html>