<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <!-- PROFILE -->
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">
            {{ auth()->user()->name ?? session('user_name') ?? 'Guest' }}
          </span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    <!-- DASHBOARD -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <!-- BUKU -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/buku*') ? 'active' : '' }}" href="{{ url('admin/buku') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>

    <!-- KATEGORI -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/kategori*') ? 'active' : '' }}" href="{{ url('admin/kategori') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-shape menu-icon"></i>
      </a>
    </li>

    <!-- BARANG -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#barang-menu"
         aria-expanded="{{ Request::is('admin/barang*') ? 'true' : 'false' }}">
        <span class="menu-title">Barang</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-package-variant menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('admin/barang*') ? 'show' : '' }}" id="barang-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/barang') ? 'active' : '' }}" href="{{ url('admin/barang') }}">Data Barang</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/barang-biasa*') ? 'active' : '' }}" href="{{ url('admin/barang-biasa') }}">Barang (HTML Biasa)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/barang-datatables*') ? 'active' : '' }}" href="{{ url('admin/barang-datatables') }}">Barang (DataTables)</a>
          </li>
        </ul>
      </div>
    </li>

    <!-- MANAGE KOTA -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/cities*') ? 'active' : '' }}" href="{{ url('admin/cities') }}">
        <span class="menu-title">Manage Kota</span>
        <i class="mdi mdi-city menu-icon"></i>
      </a>
    </li>

    <!-- KASIR -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/penjualan*') ? 'active' : '' }}" href="{{ url('admin/penjualan') }}">
        <span class="menu-title">Kasir (Penjualan)</span>
        <i class="mdi mdi-cart menu-icon"></i>
      </a>
    </li>

    <!-- WILAYAH -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#wilayah-menu"
         aria-expanded="{{ Request::is('admin/wilayah*') ? 'true' : 'false' }}">
        <span class="menu-title">Wilayah</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-map menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('admin/wilayah*') ? 'show' : '' }}" id="wilayah-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/wilayah/ajax*') ? 'active' : '' }}" href="{{ url('admin/wilayah/ajax') }}">Wilayah (Ajax)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/wilayah/axios*') ? 'active' : '' }}" href="{{ url('admin/wilayah/axios') }}">Wilayah (Axios)</a>
          </li>
        </ul>
      </div>
    </li>

    <!-- KUNJUNGAN TOKO -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/kunjungan-toko*') ? '' : 'collapsed' }}"
         data-bs-toggle="collapse"
         href="#kunjungan-toko-menu"
         aria-expanded="{{ Request::is('admin/kunjungan-toko*') ? 'true' : 'false' }}">
        <span class="menu-title">Kunjungan Toko</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
      </a>

      <div class="collapse {{ Request::is('admin/kunjungan-toko*') ? 'show' : '' }}" id="kunjungan-toko-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/kunjungan-toko/list-toko*') ? 'active' : '' }}"
               href="{{ route('admin.kunjungan_toko.list') }}">
              List Toko
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/kunjungan-toko/titik-kunjungan*') ? 'active' : '' }}"
               href="{{ route('admin.kunjungan_toko.visit') }}">
              Titik Kunjungan
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- ANTRIAN -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/antrian*') ? 'active' : '' }}"
        href="{{ route('admin.antrian.index') }}">
        <span class="menu-title">Antrian</span>
        <i class="mdi mdi-ticket-confirmation menu-icon"></i>
      </a>
    </li>

    <!-- CUSTOMER -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/customer*') ? '' : 'collapsed' }}" 
        data-bs-toggle="collapse" 
        href="#customer-menu" 
        aria-expanded="{{ Request::is('admin/customer*') ? 'true' : 'false' }}">
        <span class="menu-title">Customer</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-account-group menu-icon"></i>
      </a>

      <div class="collapse {{ Request::is('admin/customer*') ? 'show' : '' }}" id="customer-menu">
        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/customer') ? 'active' : '' }}"
              href="{{ route('admin.customer.index') }}">
              Data Customer
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/customer/create-blob') ? 'active' : '' }}"
              href="{{ route('admin.customer.createBlob') }}">
              Tambah Customer 1 (BLOB)
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/customer/create-path') ? 'active' : '' }}"
              href="{{ route('admin.customer.createPath') }}">
              Tambah Customer 2 (FILE)
            </a>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</nav>