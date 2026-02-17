@extends('layouts.app')

@section('title', 'List Kategori')

@section('content')
  <div class="page-header">
    <h1 class="mb-0">List Kategori</h1>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th style="width:80px">ID</th>
              <th>Nama Kategori</th>
              <th class="text-center" style="width:190px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kategoris as $k)
              <tr>
                <td>{{ $k->idkategori }}</td>
                <td>{{ $k->nama_kategori }}</td>
                <td class="text-center">
                  <a href="{{ route('admin.kategori.edit', $k->idkategori) }}" class="btn btn-sm btn-outline-warning">Edit</a>

                  <form action="{{ route('admin.kategori.delete', $k->idkategori) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Hapus kategori \"{{ addslashes($k->nama_kategori) }}\" ?')">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-4">Belum ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- pagination (jika pake paginate di controller) --}}
      @if(method_exists($kategoris, 'links'))
        <div class="card-footer">
          {{ $kategoris->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection