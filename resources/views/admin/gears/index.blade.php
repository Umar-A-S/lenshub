<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Lab - Inventory LensHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-microchip me-2"></i>Inventory Test Lab</h2>
        <div class="btn-group">
            <a href="{{ route('gears.index') }}" class="btn btn-outline-secondary btn-sm">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('gears.index', ['category_id' => $cat->id]) }}" class="btn btn-outline-primary btn-sm">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Tambah Unit Baru (Quick Add)</div>
        <div class="card-body">
            <form action="{{ route('gears.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->prefix }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Nama Barang (ex: Sony A7II)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="rent_price" class="form-control" placeholder="Harga Sewa" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="penalty_fee" class="form-control" placeholder="Denda" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover bg-white rounded shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Unit Code</th>
                    <th>Nama & Kategori</th>
                    <th>Status Ops</th>
                    <th>Kondisi Fisik</th>
                    <th>Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gears as $gear)
                <tr class="align-middle">
                    <td class="fw-bold text-primary">{{ $gear->unit_code }}</td>
                    <td>
                        <div class="fw-bold">{{ $gear->name }}</div>
                        <small class="text-muted">{{ $gear->category->name }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $gear->status == 'available' ? 'bg-success' : ($gear->status == 'maintenance' ? 'bg-danger' : 'bg-warning') }}">
                            {{ strtoupper($gear->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-capitalize">
                            <i class="fas fa-circle {{ $gear->condition_status == 'baik' ? 'text-success' : 'text-warning' }} me-1"></i>
                            {{ $gear->condition_status }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group border rounded p-1">
                            <form action="{{ route('gears.update-status', [$gear->id, 'available']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light" title="Set Available"><i class="fas fa-check text-success"></i></button>
                            </form>
                            <form action="{{ route('gears.update-status', [$gear->id, 'maintenance']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light" title="Set Maintenance"><i class="fas fa-tools text-danger"></i></button>
                            </form>

                            <div class="vr mx-1"></div>

                            <form action="{{ route('gears.update-condition', [$gear->id, 'rusak']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light" title="Set Rusak"><i class="fas fa-heart-crack text-warning"></i></button>
                            </form>
                            <form action="{{ route('gears.update-condition', [$gear->id, 'baik']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light" title="Set Baik"><i class="fas fa-shield-halved text-primary"></i></button>
                            </form>

                            <div class="vr mx-1"></div>

                            <form action="{{ route('gears.destroy', $gear->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pindahkan ke tempat sampah?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light" title="Soft Delete"><i class="fas fa-trash-alt text-secondary"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada unit. Coba tambah di atas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>