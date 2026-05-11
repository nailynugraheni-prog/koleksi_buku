<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode PDF</title>
</head>
<body style="font-family: DejaVu Sans, sans-serif; text-align: center; padding-top: 80px;">
    <h4 style="margin-bottom: 8px;">{{ $store->nama_toko }}</h4>
    <p style="margin-bottom: 20px;">{{ $store->barcode }}</p>

    @php
        $generator = new \Milon\Barcode\DNS1D();
    @endphp

    <div style="display: inline-block;">
        {!! $generator->getBarcodeHTML($store->barcode, 'C128', 2, 60) !!}
    </div>
</body>
</html>