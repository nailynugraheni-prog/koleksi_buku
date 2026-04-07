@extends('layouts.app1')

@section('title', 'Master Menu Vendor')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Master Menu</h3>
        <a href="{{ route('vendor.menu.create') }}" class="btn btn-primary">+ Tambah Menu</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Vendor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $i => $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $i }}</td>
                                <td>
                                    @if($menu->path_gambar)
                                        <img src="{{ asset('storage/'.$menu->path_gambar) }}" width="70" class="rounded">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $menu->nama_menu }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>{{ $menu->nama_vendor }}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('vendor.menu.edit', $menu->idmenu) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('vendor.menu.destroy', $menu->idmenu) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada menu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $menus->links() }}
        </div>
    </div>
</div>
@endsection