<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>List Kategori</title>
</head>
<body>

  <h1>List Kategori</h1>

  @if(session('success'))
    <div>{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div>{{ session('error') }}</div>
  @endif

  <a href="{{ route('admin.kategori.create') }}">+ Tambah Kategori</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama Kategori</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($kategoris as $k)
        <tr>
          <td>{{ $k->idkategori }}</td>
          <td>{{ $k->nama_kategori }}</td>
          <td>
            <a href="{{ route('admin.kategori.edit', $k->idkategori) }}">Edit</a>

            <form action="{{ route('admin.kategori.destroy', $k->idkategori) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus kategori?')">
              @csrf
              @method('DELETE')
              <button type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>

</body>
</html>
