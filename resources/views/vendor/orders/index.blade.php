@extends('layouts.app1')

@section('title', 'Pesanan Vendor')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">Daftar Pesanan</h2>

    <div class="row">

        {{-- LIST PESANAN --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Pesanan</h5>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $o)
                            <tr>
                                <td>{{ $o->idpesanan }}</td>
                                <td>{{ $o->nama }}</td>
                                <td>{{ $o->total }}</td>
                                <td>
                                    @if($o->status_bayar == 1)
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('vendor.orders.index', ['id' => $o->idpesanan]) }}"
                                       class="btn btn-sm btn-primary">
                                       Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DETAIL PESANAN --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Pesanan</h5>
                </div>

                <div class="card-body">

                    @if($selectedId)

                        <p><strong>ID Pesanan:</strong> {{ $selectedId }}</p>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $d)
                                <tr>
                                    <td>{{ $d->nama_menu }}</td>
                                    <td>{{ $d->jumlah }}</td>
                                    <td>{{ $d->harga }}</td>
                                    <td>{{ $d->subtotal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    @else
                        <p class="text-muted">Klik "Detail" untuk melihat isi pesanan</p>
                    @endif

                </div>
            </div>
        </div>

    </div>

</div>

@endsection