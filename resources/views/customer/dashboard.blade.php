<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Guest</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
<div class="container-scroller">
<div class="container-fluid page-body-wrapper full-page-wrapper">
<div class="content-wrapper pt-4">
<div class="container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between mb-4">
        <div>
            <h3>Pemesanan Kantin</h3>
            <small class="text-muted">{{ session('guest_name') }}</small>
        </div>

        <a href="{{ route('customer.start') }}" class="btn btn-outline-primary">
            Ganti Guest
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- PILIH VENDOR -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customer.dashboard') }}">
                <div class="row">
                    <div class="col-md-9">
                        <select name="vendor_id" class="form-control" onchange="this.form.submit()">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->idvendor }}"
                                    {{ (string)$selectedVendorId === (string)$vendor->idvendor ? 'selected' : '' }}>
                                    {{ $vendor->nama_vendor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- MENU -->
        <div class="col-md-8">
            <h4 class="mb-3">Menu</h4>

            <div class="row">
                @forelse($menus as $menu)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">

                        @if($menu->path_gambar)
                            <img src="{{ asset('storage/'.$menu->path_gambar) }}"
                                 style="height:180px; object-fit:cover;">
                        @endif

                        <div class="card-body">
                            <h5>{{ $menu->nama_menu }}</h5>
                            <p>Rp {{ number_format($menu->harga,0,',','.') }}</p>

                            <!-- 🔥 ADD TO CART -->
                            <form method="POST" action="{{ route('customer.cart.add') }}">
                                @csrf
                                <input type="hidden" name="idmenu" value="{{ $menu->idmenu }}">

                                <input type="number" name="jumlah" value="1" min="1"
                                       class="form-control mb-2">

                                <input type="text" name="catatan"
                                       class="form-control mb-2"
                                       placeholder="Catatan">

                                <button class="btn btn-success w-100">
                                    + Keranjang
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            Tidak ada menu
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KERANJANG -->
        <div class="col-md-4">
            <h4 class="mb-3">Keranjang</h4>

            <div class="card">
                <div class="card-body">

                    @if(!empty($cart) && count($cart) > 0)
                        @foreach($cart as $item)
                        <div class="border-bottom mb-2 pb-2">
                            <strong>{{ $item['nama_menu'] }}</strong><br>
                            <small>{{ $item['catatan'] ?? '-' }}</small><br>

                            {{ $item['jumlah'] }} x Rp {{ number_format($item['harga'],0,',','.') }}

                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    Rp {{ number_format($item['subtotal'],0,',','.') }}
                                </span>

                                <!-- 🔥 REMOVE ITEM -->
                                <form method="POST"
                                      action="{{ route('customer.cart.remove', $item['idmenu']) }}">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">x</button>
                                </form>
                            </div>
                        </div>
                        @endforeach

                        <hr>

                        <h5>Total:
                            Rp {{ number_format($cartTotal,0,',','.') }}
                        </h5>

                        <!-- 🔥 CHECKOUT -->
                        <form method="POST" action="{{ route('customer.cart.checkout') }}">
                            @csrf
                            <button class="btn btn-primary w-100 mt-2">
                                Checkout / Bayar
                            </button>
                        </form>

                    @else
                        <div class="alert alert-info mb-0">
                            Keranjang kosong
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
</div>
</div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>