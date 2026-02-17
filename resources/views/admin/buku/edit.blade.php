@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')

<div class="page-header">
    <h1 class="mb-0">Edit Buku</h1>
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

<form action="{{ route('admin.buku.update', $buku->idbuku) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Kategori</label>
    <select name="idkategori" id="idkategori" class="form-control" required>
        @foreach($kategoris as $k)
            <option value="{{ $k->idkategori }}"
                {{ $buku->idkategori == $k->idkategori ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Kode</label>
    <input type="text" name="kode" id="kode"
        class="form-control"
        value="{{ $buku->kode }}"
        readonly required>
</div>

<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="judul"
        class="form-control"
        value="{{ old('judul', $buku->judul) }}"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Pengarang</label>
    <input type="text" name="pengarang"
        class="form-control"
        value="{{ old('pengarang', $buku->pengarang) }}"
        required>
</div>

<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Kembali</a>

</form>

</div>
</div>

{{-- SCRIPT AUTO UPDATE KODE SAAT GANTI KATEGORI --}}
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