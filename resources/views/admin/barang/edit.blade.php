@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')

<div class="page-header">
    <h1 class="mb-0">Edit Barang</h1>
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

    <form action="{{ route('admin.barang.update', $barang->id_barang) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">ID Barang</label>
        <input type="text" class="form-control" value="{{ $barang->id_barang }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control"
               value="{{ old('nama', $barang->nama) }}" maxlength="50" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga</label>
        <input type="number" name="harga" class="form-control" min="0"
               value="{{ old('harga', $barang->harga) }}" required>
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

  </div>
</div>

@endsection