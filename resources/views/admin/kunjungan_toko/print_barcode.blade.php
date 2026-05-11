@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body text-center">
            <h4 class="mb-1">{{ $store->nama_toko }}</h4>
            <p class="mb-3">{{ $store->barcode }}</p>

            @php
                $generator = new \Milon\Barcode\DNS1D();
            @endphp

            <div class="d-inline-block">
                {!! $generator->getBarcodeHTML($store->barcode, 'C128', 2, 60) !!}
            </div>

            <div class="mt-3 no-print">
                <a href="{{ route('admin.kunjungan_toko.download_pdf', $store->barcode) }}" class="btn btn-primary">
                    Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection