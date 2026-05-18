<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Guest Antrian</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="bg-light">
<div id="antrian-app"
     data-page="guest"
     data-snapshot-url="{{ route('antrian.snapshot') }}"
     data-csrf="{{ csrf_token() }}">

    <div class="container py-4">
        <h4 class="mb-3">Halaman Guest</h4>

        @if(session('success'))
            <div class="alert alert-success fw-semibold shadow-sm">
                {{ session('success') }}
            </div>
        @elseif($lastNumber)
            <div class="alert alert-success fw-semibold shadow-sm">
                Nomor antrian Anda: <strong>{{ $lastNumber }}</strong>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold py-3">
                Ambil Nomor Antrian
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('guest.antrian.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        @error('nama')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Layanan / Poli</label>
                        <select name="layanan_id" class="form-control" required>
                            <option value="">-- pilih layanan --</option>
                            @foreach($layanans as $layan)
                                <option value="{{ $layan->id }}" @selected(old('layanan_id') == $layan->id)>
                                    {{ $layan->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                        @error('layanan_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold text-uppercase tracking-wider">
                            Ambil Nomor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/antrian-realtime.js') }}"></script>
</body>
</html>