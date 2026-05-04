@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Customer</h4>
        <div>
            <a href="{{ route('admin.customer.createBlob') }}" class="btn btn-primary btn-sm">Tambah Customer 1</a>
            <a href="{{ route('admin.customer.createPath') }}" class="btn btn-success btn-sm">Tambah Customer 2</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Provinsi</th>
                            <th>Kota</th>
                            <th>Kecamatan</th>
                            <th>Kodepos/Kelurahan</th>
                            <th>Foto</th>
                            <th>Jenis Simpan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customer->nama }}</td>
                                <td>{{ $customer->alamat }}</td>
                                <td>{{ $customer->provinsi }}</td>
                                <td>{{ $customer->kota }}</td>
                                <td>{{ $customer->kecamatan }}</td>
                                <td>{{ $customer->kodepos_kelurahan }}</td>
                                <td>
                                    @if($customer->foto_blob)
                                        <img src="data:image/jpeg;base64,{{ base64_encode(stream_get_contents($customer->foto_blob)) }}"
                                             alt="Foto Customer"
                                             style="width:80px;height:80px;object-fit:cover;">
                                    @elseif($customer->foto_path)
                                        <img src="{{ asset('storage/' . $customer->foto_path) }}"
                                             alt="Foto Customer"
                                             style="width:80px;height:80px;object-fit:cover;">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($customer->foto_blob)
                                        BLOB
                                    @elseif($customer->foto_path)
                                        FILE
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection