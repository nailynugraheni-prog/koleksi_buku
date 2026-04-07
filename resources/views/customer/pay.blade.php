<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @php
        $isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL);
        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp

    <script
        type="text/javascript"
        src="{{ $snapUrl }}"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper pt-4">
            <div class="container">

                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-2">Pembayaran Midtrans</h3>
                        <p class="mb-1">Order ID: <strong>{{ $orderId }}</strong></p>
                        <p class="mb-3">Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>

                        <button id="pay-button" class="btn btn-primary">
                            Buka Pembayaran
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function openSnap() {
        window.snap.pay(@json($snapToken), {
            onSuccess: function (result) {
                window.location.href = @json(route('customer.payment.success')) + '?order_id=' + encodeURIComponent(@json($orderId));
            },
            onPending: function (result) {
                alert('Pembayaran masih pending.');
            },
            onError: function (result) {
                alert('Pembayaran gagal.');
            },
            onClose: function () {
                alert('Popup pembayaran ditutup.');
            }
        });
    }

    document.getElementById('pay-button').addEventListener('click', openSnap);

    window.addEventListener('load', function () {
        openSnap();
    });
</script>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>