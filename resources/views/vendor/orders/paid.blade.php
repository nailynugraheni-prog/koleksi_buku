@extends('layouts.app1')

@section('title', 'Pesanan Lunas')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Pesanan dengan Status Lunas</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pesanan</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $i => $order)
                            <tr>
                                <td>{{ $orders->firstItem() + $i }}</td>
                                <td>#{{ $order->idpesanan }}</td>
                                <td>{{ $order->nama }}</td>
                                <td>{{ $order->email ?? '-' }}</td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->metode_bayar == 1)
                                        Virtual Account
                                    @elseif($order->metode_bayar == 2)
                                        QRIS
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="badge bg-success">Lunas</span></td>
                                <td>
                                    <a href="{{ route('vendor.orders.show', $order->idpesanan) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada pesanan lunas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection