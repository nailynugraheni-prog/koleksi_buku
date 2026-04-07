@extends('layouts.app1')

@section('title', 'Tambah Menu')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Tambah Menu</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('vendor.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu') }}">
                    @error('nama_menu') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga') }}">
                    @error('harga') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    <input type="file" name="path_gambar" class="form-control">
                    @error('path_gambar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection