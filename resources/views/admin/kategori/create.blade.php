<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Kategori</title>
</head>
<body>

  <h1>Tambah Kategori</h1>

  @if($errors->any())
    <ul>
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  @endif

  <form action="{{ route('admin.kategori.store') }}" method="POST">
    @csrf
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required maxlength="100">
    <button type="submit">Simpan</button>
    <a href="{{ route('admin.kategori.index') }}">Batal</a>
  </form>

</body>
</html>
