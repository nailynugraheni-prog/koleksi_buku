@extends('layouts.app1')

@section('title', 'Scan QR Pesanan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Scan QR Pesanan Customer</h2>
        <a href="{{ route('vendor.orders.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Scanner</h5>
                </div>
                <div class="card-body">
                    <div id="reader" style="width:100%;"></div>
                    <p class="mt-3 text-muted mb-0">
                        Arahkan kamera ke QR code customer.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Hasil Scan</h5>
                </div>
                <div class="card-body" id="resultBox">
                    <p class="text-muted mb-0">Belum ada hasil scan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const csrfToken = "{{ csrf_token() }}";
    const beepUrl = "{{ asset('sounds/beep.mp3') }}";

    let html5QrCode;
    let isScanning = true;
    let isProcessed = false;
    let beepSound = new Audio(beepUrl);
    beepSound.preload = 'auto';

    async function playBeep() {
        try {
            beepSound.currentTime = 0;
            await beepSound.play();
        } catch (e) {
            // kalau browser blok autoplay, tetap lanjut proses scan
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
                isScanning = false;
            } catch (e) {
                // ignore
            }
        }
    }

    function renderResult(data) {
        const order = data.order;
        const details = data.details;

        const statusBadge = order.status_bayar == 1
            ? '<span class="badge bg-success">Lunas</span>'
            : '<span class="badge bg-warning text-dark">Pending</span>';

        let rows = '';
        details.forEach((item, index) => {
            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_menu}</td>
                    <td>${item.jumlah}</td>
                    <td>${item.harga}</td>
                    <td>${item.subtotal}</td>
                </tr>
            `;
        });

        const html = `
            <div class="alert alert-success">
                <strong>QR berhasil dibaca.</strong> Scanner sudah dihentikan.
            </div>

            <div class="mb-3">
                <p class="mb-1"><strong>ID Pesanan:</strong> ${order.idpesanan}</p>
                <p class="mb-1"><strong>Nama Customer:</strong> ${order.nama ?? '-'}</p>
                <p class="mb-1"><strong>Total:</strong> ${order.total ?? '-'}</p>
                <p class="mb-1"><strong>Status Bayar:</strong> ${statusBadge}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || '<tr><td colspan="5" class="text-center">Detail pesanan kosong</td></tr>'}
                    </tbody>
                </table>
            </div>

            <button class="btn btn-primary mt-2" onclick="location.reload()">
                Scan Lagi
            </button>
        `;

        document.getElementById('resultBox').innerHTML = html;
    }

    async function onScanSuccess(decodedText) {
        if (isProcessed) return;
        isProcessed = true;

        try {
            await playBeep();
            await stopScanner();

            const response = await fetch("{{ route('vendor.orders.scan-qr.result') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_value: decodedText
                })
            });

            const data = await response.json();

            if (!response.ok) {
                document.getElementById('resultBox').innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message ?? 'Gagal membaca QR code.'}
                    </div>
                `;
                return;
            }

            renderResult(data);

        } catch (error) {
            document.getElementById('resultBox').innerHTML = `
                <div class="alert alert-danger">
                    Terjadi error saat memproses QR code.
                </div>
            `;
        }
    }

    function onScanFailure(error) {
        // biarkan kosong
    }

    document.addEventListener('DOMContentLoaded', async function () {
        html5QrCode = new Html5Qrcode("reader");

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        try {
            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            );
        } catch (e) {
            document.getElementById('resultBox').innerHTML = `
                <div class="alert alert-danger">
                    Kamera tidak bisa dibuka. Pastikan izin kamera sudah diberikan.
                </div>
            `;
        }
    });
</script>
@endsection