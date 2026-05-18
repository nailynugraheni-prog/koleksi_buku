@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div id="antrian-app"
         data-page="admin"
         data-snapshot-url="{{ route('antrian.snapshot') }}"
         data-csrf="{{ csrf_token() }}">

        <h4 class="mb-3">Halaman Admin</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div>Total</div>
                        <h3 id="admin-total">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div>Menunggu</div>
                        <h3 id="admin-waiting">{{ $stats['waiting'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div>Dipanggil</div>
                        <h3 id="admin-called">{{ $stats['called'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div>Terlambat</div>
                        <h3 id="admin-skipped">{{ $stats['skipped'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div>Selesai</div>
                        <h3 id="admin-done">{{ $stats['done'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-bold">Daftar Antrian</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>No Antrian</th>
                        <th>Layanan / Poli</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="admin-rows">
                        @forelse($rows as $row)
                            <tr>
                                <td class="fw-bold">
                                    {{ $row->kode_antrian ?? $row->nomor_antrian ?? $row->kode_layanan }}
                                </td>

                                <td>
                                    {{ $row->nama_layanan ?? ($row->layanan->nama_layanan ?? '-') }}
                                </td>

                                <td>{{ $row->nama }}</td>

                                <td>
                                    <span class="badge
                                        @if(in_array(strtolower($row->status), ['menunggu', 'waiting'])) bg-warning text-dark
                                        @elseif(in_array(strtolower($row->status), ['dipanggil', 'called'])) bg-success
                                        @elseif(in_array(strtolower($row->status), ['skipped', 'terlambat'])) bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        @if(in_array(strtolower($row->status), ['menunggu', 'waiting']))
                                            <form method="POST" action="{{ route('admin.antrian.call', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-primary">Panggil</button>
                                            </form>
                                        @endif

                                        @if(in_array(strtolower($row->status), ['dipanggil', 'called']))
                                            <form method="POST" action="{{ route('admin.antrian.done', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success">Selesai</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.antrian.skip', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-danger">Lewati</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.antrian.recall', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-dark">Panggil Ulang</button>
                                            </form>
                                        @endif

                                        @if(in_array(strtolower($row->status), ['skipped', 'terlambat']))
                                            <form method="POST" action="{{ route('admin.antrian.call', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-info text-white">Panggil Lagi</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada antrian terdaftar hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/antrian-realtime.js') }}"></script>
@endsection