<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard PDF</title>
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
    .header { text-align:center; margin-bottom:20px; }
    .cards { display:flex; gap:10px; margin-bottom:20px; }
    .card { flex:1; border:1px solid #ddd; padding:10px; text-align:center; border-radius:6px; }
    table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    th, td { border:1px solid #ddd; padding:6px; font-size:11px; }
    th { background:#f2f2f2; }
    h2 { margin:0 0 8px 0; font-size:16px; }
    .small { color:#666; font-size:11px; }
    /* jika ingin page break: .page-break { page-break-after: always; } */
  </style>
</head>
<body>
  <div class="header">
    <h1>Dashboard Admin</h1>
    <div class="small">Tanggal: {{ date('d-m-Y H:i') }}</div>
  </div>

  <div class="cards">
    <div class="card">
      <div class="small">Users</div>
      <div><strong>{{ $counts['users'] ?? 0 }}</strong></div>
    </div>
    <div class="card">
      <div class="small">Roles</div>
      <div><strong>{{ $counts['roles'] ?? 0 }}</strong></div>
    </div>
    <div class="card">
      <div class="small">Kategori</div>
      <div><strong>{{ $counts['kategori'] ?? 0 }}</strong></div>
    </div>
    <div class="card">
      <div class="small">Buku</div>
      <div><strong>{{ $counts['buku'] ?? 0 }}</strong></div>
    </div>
  </div>

  <h2>Buku Terbaru</h2>
  <table>
    <thead>
      <tr>
        <th>Kode</th><th>Judul</th><th>Pengarang</th><th>Kategori</th><th>Dibuat Oleh</th>
      </tr>
    </thead>
    <tbody>
      @forelse($latestBooks as $b)
        <tr>
          <td>{{ $b->kode }}</td>
          <td>{{ $b->judul }}</td>
          <td>{{ $b->pengarang }}</td>
          <td>{{ $b->kategori }}</td>
          <td>{{ $b->creator }}</td>
        </tr>
      @empty
        <tr><td colspan="5" style="text-align:center">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>

  <h2>Top Kategori</h2>
  <table>
    <thead><tr><th>Kategori</th><th>Total Buku</th></tr></thead>
    <tbody>
      @forelse($topKategori as $k)
        <tr>
          <td>{{ $k->nama_kategori }}</td>
          <td>{{ $k->total }}</td>
        </tr>
      @empty
        <tr><td colspan="2" style="text-align:center">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>

</body>
</html>