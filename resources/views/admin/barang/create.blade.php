@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')

<div class="page-header">
    <h1 class="mb-0">Tambah Barang</h1>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
  <div class="card-body">

    <form action="{{ route('admin.barang.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="50"
               value="{{ old('nama') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga</label>
        <input type="number" name="harga" class="form-control" min="0"
               value="{{ old('harga') }}" required>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

  </div>
</div>

@endsection