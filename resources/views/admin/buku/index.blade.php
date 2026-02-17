@extends('layouts.app')

@section('title', 'List Buku')

@section('content')

<div class="page-header">
    <h1 class="mb-0">List Buku</h1>
    <a href="{{ route('admin.buku.create') }}" class="btn btn-primary">
        + Tambah Buku
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">ID</th>
                        <th style="width:110px">Kode</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Kategori</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-center" style="width:190px">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bukus as $b)
                        <tr>
                            <td>{{ $b->idbuku }}</td>
                            <td>{{ $b->kode }}</td>
                            <td>{{ $b->judul }}</td>
                            <td>{{ $b->pengarang }}</td>
                            <td>{{ $b->nama_kategori }}</td>
                            <td>{{ $b->created_by_name }}</td>
                            <td class="text-center">

                                <a href="{{ route('admin.buku.edit', $b->idbuku) }}"
                                   class="btn btn-sm btn-outline-warning">
                                   Edit
                                </a>

                                <form action="{{ route('admin.buku.delete', $b->idbuku) }}"
                                      method="POST"
                                      style="display:inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus buku \"{{ addslashes($b->judul) }}\" ?')">
                                        Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Belum ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination (jika pakai paginate) --}}
        @if(method_exists($bukus, 'links'))
            <div class="card-footer">
                {{ $bukus->links() }}
            </div>
        @endif

    </div>
</div>

@endsection