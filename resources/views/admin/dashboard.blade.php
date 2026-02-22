@extends('layouts.app')

@section('title','Dashboard Admin')

@section('content')

<div class="page-header">
    <h1 class="mb-0">Dashboard Admin</h1>
    <small class="text-muted">Ringkasan Sistem</small>
</div>

<div class="mb-3 d-flex flex-wrap align-items-center">
    <a href="{{ route('admin.dashboard.certificatePdf') }}" target="_blank"
       class="btn btn-success me-2" style="width:auto; display:inline-block;">
        Cetak Sertifikat (PDF)
    </a>

    <a href="{{ route('admin.dashboard.pdf') }}" target="_blank"
       class="btn btn-primary" style="width:auto; display:inline-block;">
        Cetak Dashboard (PDF)
    </a>
</div>

<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted">Users</h6>
                <h3 class="mb-0">{{ $counts['users'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted">Roles</h6>
                <h3 class="mb-0">{{ $counts['roles'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted">Kategori</h6>
                <h3 class="mb-0">{{ $counts['kategori'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted">Buku</h6>
                <h3 class="mb-0">{{ $counts['buku'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

</div>



<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Buku Terbaru</h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Kategori</th>
                        <th>Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestBooks as $b)
                        <tr>
                            <td>{{ $b->kode }}</td>
                            <td>{{ $b->judul }}</td>
                            <td>{{ $b->pengarang }}</td>
                            <td>{{ $b->kategori }}</td>
                            <td>{{ $b->creator }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                Belum ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Top Kategori</h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Total Buku</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topKategori as $k)
                        <tr>
                            <td>{{ $k->nama_kategori }}</td>
                            <td>{{ $k->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4">
                                Belum ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection