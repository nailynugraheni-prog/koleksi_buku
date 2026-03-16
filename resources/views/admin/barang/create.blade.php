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

    {{-- Form: tombol SUBMIT DI LUAR form (sesuai soal) --}}
    <form id="createBarangForm" action="{{ route('admin.barang.store') }}" method="POST" novalidate>
      @csrf

      <div class="mb-3">
        <label class="form-label" for="nama">Nama</label>
        <input id="nama" type="text" name="nama" class="form-control" maxlength="50"
               value="{{ old('nama') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="harga">Harga</label>
        <input id="harga" type="number" name="harga" class="form-control" min="0"
               value="{{ old('harga') }}" required>
      </div>

      {{-- tempat pesan error/invalid --}}
      <div id="formAlert" class="alert alert-warning d-none" role="alert" aria-live="polite"></div>

      {{-- fallback untuk pengguna tanpa JS --}}
      <noscript>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </noscript>

    </form>

    
    <div class="d-flex gap-2 mt-3">
      <button
        type="button"
        class="btn btn-primary js-external-submit"
        data-target="#createBarangForm"
        data-default-text="Simpan"
        data-busy-text="Menyimpan...">
        <span class="spinner-border spinner-border-sm me-2 btn-spinner d-none" role="status" aria-hidden="true"></span>
        <span class="btn-text">Simpan</span>
      </button>

      <a href="{{ route('admin.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

  </div>
</div>

@endsection