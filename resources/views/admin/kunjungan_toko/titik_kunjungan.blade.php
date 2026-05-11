@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Titik Kunjungan</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Barcode Scanner</div>
        <div class="card-body">

            <div id="reader" style="width: 100%; max-width: 500px;"></div>

            <div class="mt-3">
                <button type="button" class="btn btn-primary" id="btnStart">Mulai Scan</button>
                <button type="button" class="btn btn-secondary" id="btnStop" disabled>Stop</button>
            </div>

            <form action="{{ route('admin.kunjungan_toko.visit.submit') }}" method="POST" class="mt-4">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Barcode Toko</label>
                        <input type="text" name="barcode" id="barcode_scan" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Nama Toko</label>
                        <input type="text" id="nama_toko" class="form-control" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Latitude Toko</label>
                        <input type="text" id="store_latitude" class="form-control" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Longitude Toko</label>
                        <input type="text" id="store_longitude" class="form-control" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Accuracy Toko</label>
                        <input type="text" id="store_accuracy" class="form-control" readonly>
                    </div>
                </div>

                <hr>

                <h6>Posisi Sales</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Latitude Sales</label>
                        <input type="text" name="sales_latitude" id="sales_latitude" class="form-control" readonly required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Longitude Sales</label>
                        <input type="text" name="sales_longitude" id="sales_longitude" class="form-control" readonly required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Accuracy Sales</label>
                        <input type="text" name="sales_accuracy" id="sales_accuracy" class="form-control" readonly required>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" onclick="ambilLokasiSales()">Ambil Lokasi</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </form>

        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let isScanning = false;

    const startBtn = document.getElementById('btnStart');
    const stopBtn = document.getElementById('btnStop');

    async function startScanner() {
        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            await html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: 250
                },
                async (decodedText) => {
                    document.getElementById('barcode_scan').value = decodedText;
                    loadStore(decodedText);
                    stopScanner();
                }
            );

            isScanning = true;
            startBtn.disabled = true;
            stopBtn.disabled = false;
        } catch (err) {
            alert('Scanner gagal dijalankan');
            console.error(err);
        }
    }

    async function stopScanner() {
        try {
            if (html5QrCode && isScanning) {
                await html5QrCode.stop();
                await html5QrCode.clear();
            }
        } catch (err) {
            console.error(err);
        }

        isScanning = false;
        startBtn.disabled = false;
        stopBtn.disabled = true;
    }

    function loadStore(barcode) {
        fetch(`{{ url('/admin/kunjungan-toko/barcode') }}/${encodeURIComponent(barcode)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nama_toko').value = data.data.nama_toko ?? '';
                    document.getElementById('store_latitude').value = data.data.latitude ?? '';
                    document.getElementById('store_longitude').value = data.data.longitude ?? '';
                    document.getElementById('store_accuracy').value = data.data.accuracy ?? '';
                } else {
                    alert('Toko tidak ditemukan');
                }
            })
            .catch(() => alert('Gagal ambil data toko'));
    }

    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation tidak didukung browser'));
                return;
            }

            let bestResult = null;
            const startTime = Date.now();

            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;

                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                    }

                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }

                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        if (bestResult) resolve(bestResult);
                        else reject(new Error("Timeout, tidak dapat posisi"));
                    }
                },
                (error) => {
                    navigator.geolocation.clearWatch(watchId);
                    reject(error);
                },
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );
        });
    }

    async function ambilLokasiSales() {
        try {
            const pos = await getAccuratePosition(50);
            document.getElementById('sales_latitude').value = pos.coords.latitude;
            document.getElementById('sales_longitude').value = pos.coords.longitude;
            document.getElementById('sales_accuracy').value = pos.coords.accuracy;
        } catch (e) {
            alert('Gagal ambil lokasi sales: ' + e.message);
        }
    }

    startBtn.addEventListener('click', startScanner);
    stopBtn.addEventListener('click', stopScanner);
</script>
@endsection