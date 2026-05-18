<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Papan Antrian</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="bg-light">
<div id="antrian-app"
     data-page="board"
     data-snapshot-url="{{ route('antrian.snapshot') }}"
     data-csrf="{{ csrf_token() }}"
     data-current-signature="{{ $current ? ($current->id ?? '') . '-' . ($current->called_at ?? '') : '' }}">

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Papan Antrian</h4>
            <button id="voice-toggle" class="btn btn-dark btn-sm">Aktifkan Suara</button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted fw-semibold">Total Antrian</div>
                        <h2 id="board-total" class="fw-bold text-primary mt-2">{{ $stats['total'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body" id="board-current">
                        @if($current)
                            <div class="text-center py-2">
                                <h6 class="text-uppercase tracking-wider text-muted fw-bold">Sedang Dipanggil</h6>
                                <h1 class="display-1 fw-bold text-success my-1" id="current-kode">
                                    {{ $current->kode_antrian ?? $current->nomor_antrian ?? $current->kode_layanan ?? '-' }}
                                </h1>
                                <p class="fs-4 text-dark mb-0 fw-semibold" id="current-layanan">
                                    {{ $current->nama_layanan ?? ($current->layanan->nama_layanan ?? '-') }}
                                </p>
                                <p class="text-muted" id="current-nama">Nama: {{ $current->nama }}</p>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <h5>Belum ada antrian yang dipanggil.</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Daftar Antrian Aktif</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>No Antrian</th>
                        <th>Layanan / Poli</th>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="board-rows">
                        @forelse($activeRows as $row)
                            <tr>
                                <td class="fw-bold text-dark">
                                    {{ $row->kode_antrian ?? $row->nomor_antrian ?? $row->kode_layanan }}
                                </td>
                                <td>
                                    {{ $row->nama_layanan ?? ($row->layanan->nama_layanan ?? '-') }}
                                </td>
                                <td>{{ $row->nama }}</td>
                                <td>
                                    <span class="badge @if(in_array(strtolower($row->status), ['dipanggil', 'called'])) bg-success @else bg-warning text-dark @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Tidak ada antrian aktif saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<audio id="dingdong-audio" src="{{ asset('sounds/dingdong.mp3') }}" preload="auto"></audio>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/antrian-realtime.js') }}"></script>
</body>
</html>