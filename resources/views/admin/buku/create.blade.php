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

<form id="createBukuForm" action="{{ route('admin.buku.store') }}" method="POST" novalidate>
@csrf

<div class="mb-3">
    <label class="form-label" for="idkategori">Kategori</label>
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
    <label class="form-label" for="kode">Kode</label>
    <input type="text" name="kode" id="kode" class="form-control" readonly required>
</div>

<div class="mb-3">
    <label class="form-label" for="judul">Judul</label>
    <input type="text" name="judul" id="judul" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label" for="pengarang">Pengarang</label>
    <input type="text" name="pengarang" id="pengarang" class="form-control" required>
</div>

<div id="formAlert" class="alert alert-warning d-none" role="alert" aria-live="polite"></div>

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
data-target="#createBukuForm"
data-default-text="Simpan"
data-busy-text="Menyimpan...">

<span class="spinner-border spinner-border-sm me-2 btn-spinner d-none"></span>
<span class="btn-text">Simpan</span>

</button>

<a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Kembali</a>
</div>

</div>
</div>

<script>
document.getElementById('idkategori').addEventListener('change', function () {

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
.catch(error => console.error(error));

});
</script>

@endsection