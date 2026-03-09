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

    {{-- Form: tombol SUBMIT DI LUAR form (sama seperti create) --}}
    <form id="editBarangForm" action="{{ route('admin.barang.update', $barang->id_barang) }}" method="POST" novalidate>
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label" for="id_barang">ID Barang</label>
        <input id="id_barang" type="text" class="form-control" value="{{ $barang->id_barang }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label" for="nama">Nama</label>
        <input id="nama" type="text" name="nama" class="form-control" maxlength="50"
               value="{{ old('nama', $barang->nama) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="harga">Harga</label>
        <input id="harga" type="number" name="harga" class="form-control" min="0"
               value="{{ old('harga', $barang->harga) }}" required>
      </div>

      {{-- tempat pesan error/invalid --}}
      <div id="formAlert" class="alert alert-warning d-none" role="alert" aria-live="polite"></div>

      {{-- fallback untuk pengguna tanpa JS --}}
      <noscript>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </noscript>

    </form>

    {{-- TOMBOL DI LUAR FORM (dikelola oleh form-global.js) --}}
    <div class="d-flex gap-2 mt-3">
      <button
        type="button"
        class="btn btn-primary js-external-submit"
        data-target="#editBarangForm"
        data-default-text="Update"
        data-busy-text="Menyimpan...">
        <span class="spinner-border spinner-border-sm me-2 btn-spinner d-none" role="status" aria-hidden="true"></span>
        <span class="btn-text">Update</span>
      </button>

      <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

  </div>
</div>

@endsection