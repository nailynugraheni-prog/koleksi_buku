@extends('layouts.app1')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Detail Pesanan #{{ $order->idpesanan }}</h3>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p class="mb-1"><strong>Nama:</strong> {{ $order->nama }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $order->email ?? '-' }}</p>
            <p class="mb-1"><strong>Total:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            <p class="mb-1"><strong>Status:</strong> Lunas</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $item)
                        <tr>
                            <td>{{ $item->nama_menu }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('vendor.orders.paid') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection