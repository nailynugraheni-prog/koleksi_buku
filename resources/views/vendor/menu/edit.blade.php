@extends('layouts.app1')

@section('title', 'Edit Menu')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Edit Menu</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('vendor.menu.update', $menu->idmenu) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $menu->nama_menu) }}">
                    @error('nama_menu') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga', $menu->harga) }}">
                    @error('harga') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Baru</label>
                    <input type="file" name="path_gambar" class="form-control">
                    @if($menu->path_gambar)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$menu->path_gambar) }}" width="100" class="rounded">
                        </div>
                    @endif
                    @error('path_gambar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection