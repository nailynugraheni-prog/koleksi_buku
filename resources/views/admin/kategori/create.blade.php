@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
  <div class="page-header d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Tambah Kategori</h1>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">Kembali</a>
  </div>

  {{-- Errors (server-side) --}}
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form id="createKategoriForm" action="{{ route('admin.kategori.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
          <label for="nama_kategori" class="form-label">Nama Kategori</label>
          <input
            id="nama_kategori"
            name="nama_kategori"
            type="text"
            class="form-control {{ $errors->has('nama_kategori') ? 'is-invalid' : '' }}"
            value="{{ old('nama_kategori') }}"
            required
            maxlength="100"
          >
          @if($errors->has('nama_kategori'))
            <div class="invalid-feedback">
              {{ $errors->first('nama_kategori') }}
            </div>
          @endif
        </div>

        {{-- tempat pesan error/invalid (client-side) --}}
        <div id="formAlert" class="alert alert-warning d-none" role="alert" aria-live="polite"></div>

        {{-- noscript fallback --}}
        <noscript>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">Batal</a>
          </div>
        </noscript>
      </form>

      {{-- tombol di luar form (dikelola form-global.js) --}}
      <div class="d-flex gap-2 mt-3">
        <button
          type="button"
          class="btn btn-primary js-external-submit"
          data-target="#createKategoriForm"
          data-default-text="Simpan"
          data-busy-text="Menyimpan...">
          <span class="spinner-border spinner-border-sm me-2 btn-spinner d-none" role="status" aria-hidden="true"></span>
          <span class="btn-text">Simpan</span>
        </button>

        <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </div>
  </div>
@endsection