<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Kategori</title>
</head>
<body>

  <h1>Edit Kategori</h1>

  @if($errors->any())
    <ul>
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  @endif

  <form action="{{ route('admin.kategori.update', $kategori->idkategori) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required maxlength="100">
    <button type="submit">Update</button>
    <a href="{{ route('admin.kategori.index') }}">Batal</a>
  </form>

</body>
</html>
