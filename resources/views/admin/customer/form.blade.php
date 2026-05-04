@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">{{ $title }}</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" id="customerForm">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="nama" class="form-control" placeholder="Nama" value="{{ old('nama') }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="provinsi" class="form-control" placeholder="Provinsi" value="{{ old('provinsi') }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="kota" class="form-control" placeholder="Kota" value="{{ old('kota') }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="kecamatan" class="form-control" placeholder="Kecamatan" value="{{ old('kecamatan') }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="kodepos_kelurahan" class="form-control" placeholder="Kodepos / Kelurahan" value="{{ old('kodepos_kelurahan') }}" required>
                    </div>

                    <div class="col-12">
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center">
                            <video id="video" autoplay playsinline class="w-100" style="min-height:220px; background:#000;"></video>
                            <img id="preview" class="w-100 mt-2" style="min-height:220px; object-fit:cover; background:#f8f9fa;" alt="Preview Foto">
                        </div>
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div>
                            <button type="button" class="btn btn-primary" id="btnStartCamera">Buka Kamera</button>
                            <button type="button" class="btn btn-warning" id="btnCapture">Ambil Foto</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>

                    <input type="hidden" name="foto_data" id="foto_data">
                    <canvas id="canvas" style="display:none;"></canvas>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let stream = null;

    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const fotoDataInput = document.getElementById('foto_data');

    document.getElementById('btnStartCamera').addEventListener('click', async function () {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            video.srcObject = stream;
        } catch (error) {
            alert('Kamera tidak bisa dibuka: ' + error.message);
        }
    });

    document.getElementById('btnCapture').addEventListener('click', function () {
        if (!video.srcObject) {
            alert('Buka kamera dulu.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        preview.src = dataUrl;
        fotoDataInput.value = dataUrl;
    });

    document.getElementById('customerForm').addEventListener('submit', function (e) {
        if (!fotoDataInput.value) {
            e.preventDefault();
            alert('Silakan ambil foto dulu.');
        }
    });
</script>
@endsection