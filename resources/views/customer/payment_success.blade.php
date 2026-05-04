<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-3 text-success">Pembayaran Berhasil</h3>

                                <p class="mb-1">
                                    <strong>ID Pesanan:</strong> {{ $pesanan->idpesanan }}
                                </p>

                                <p class="mb-3">
                                    QR Code berisi ID pesanan di bawah ini:
                                </p>

                                <div class="mb-4">
                                    <img
                                        id="qrImage"
                                        src="{{ $qrCode }}"
                                        alt="QR Code Pesanan"
                                        class="img-fluid"
                                        style="max-width: 250px;"
                                    >
                                </div>

                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="#" class="btn btn-success" onclick="downloadQrCode(); return false;">
                                        Download QR Code
                                    </a>

                                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

<script>
    function downloadQrCode() {
        const img = document.getElementById('qrImage');

        fetch(img.src)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = url;
                link.download = 'qr-pesanan-{{ $pesanan->idpesanan }}.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                window.URL.revokeObjectURL(url);
            })
            .catch(() => {
                alert('Gagal download QR code.');
            });
    }
</script>
</body>
</html>