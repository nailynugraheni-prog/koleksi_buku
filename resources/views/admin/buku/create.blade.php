@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')

<div class="page-header">
    <h1 class="mb-0">Tambah Buku</h1>
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

<form action="{{ route('admin.buku.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label class="form-label">Kategori</label>
    <select name="idkategori" id="idkategori" class="form-control" required>
        <option value="">-- Pilih Kategori --</option>
        @foreach($kategoris as $k)
            <option value="{{ $k->idkategori }}">
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Kode</label>
    <input type="text" name="kode" id="kode" class="form-control" readonly required>
</div>

<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="judul" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Pengarang</label>
    <input type="text" name="pengarang" class="form-control" required>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Kembali</a>

</form>

</div>
</div>

{{-- SCRIPT AUTO GENERATE --}}
<script>
document.getElementById('idkategori').addEventListener('change', function() {

    let idkategori = this.value;

    if (!idkategori) {
        document.getElementById('kode').value = '';
        return;
    }

    fetch(`/admin/buku/generate-kode/${idkategori}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('kode').value = data.kode;
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
</script>

@endsection