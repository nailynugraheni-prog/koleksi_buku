@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
  <div class="page-header d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Edit Kategori</h1>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">Kembali</a>
  </div>

  {{-- Errors --}}
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
      <form action="{{ route('admin.kategori.update', $kategori->idkategori) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label for="nama_kategori" class="form-label">Nama Kategori</label>
          <input
            id="nama_kategori"
            name="nama_kategori"
            type="text"
            class="form-control {{ $errors->has('nama_kategori') ? 'is-invalid' : '' }}"
            value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
            required
            maxlength="100"
          >
          @if($errors->has('nama_kategori'))
            <div class="invalid-feedback">
              {{ $errors->first('nama_kategori') }}
            </div>
          @endif
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection