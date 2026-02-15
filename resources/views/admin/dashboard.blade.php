@extends('layouts.app')

@section('title','Dashboard Admin')
@section('page-title','Dashboard Admin')

@push('page-style')
<style>
  /* style sederhana khusus halaman dashboard */
  #stats { display:flex; gap:1rem; margin-bottom:1.5rem; }
  .stat { background:#fff; padding:1rem; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.04); min-width:120px; }
  table.table { background:#fff; border-radius:6px; overflow:hidden; }
</style>
@endpush

@section('content')
  <h1 class="mb-3">Dashboard Admin</h1>

  <section id="stats" class="mb-4">
    <div class="stat">
      <strong>Users</strong>
      <div>{{ $counts['users'] }}</div>
    </div>
    <div class="stat">
      <strong>Roles</strong>
      <div>{{ $counts['roles'] }}</div>
    </div>
    <div class="stat">
      <strong>Kategori</strong>
      <div>{{ $counts['kategori'] }}</div>
    </div>
    <div class="stat">
      <strong>Buku</strong>
      <div>{{ $counts['buku'] }}</div>
    </div>
  </section>

  <section id="latest-books" class="mb-4">
    <h2>Buku Terbaru</h2>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Kategori</th>
            <th>Pembuat</th>
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
            <tr><td colspan="5">Belum ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section id="top-kategori">
    <h2>Top Kategori</h2>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr><th>Kategori</th><th>Total</th></tr>
        </thead>
        <tbody>
          @forelse($topKategori as $k)
            <tr>
              <td>{{ $k->nama_kategori }}</td>
              <td>{{ $k->total }}</td>
            </tr>
          @empty
            <tr><td colspan="2">Belum ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection

@push('page-js')
<script>
  // Contoh: inisialisasi chart jika mau pakai chart untuk statistik
  console.log('Dashboard ready');
</script>
@endpush
