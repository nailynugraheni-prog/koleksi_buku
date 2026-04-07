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
          <span class="text-secondary text-small">Vendor</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    <!-- DASHBOARD -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('vendor/dashboard*') ? 'active' : '' }}" href="{{ url('vendor/dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <!-- MASTER MENU -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('vendor/menu*') ? 'active' : '' }}" href="{{ url('vendor/menu') }}">
        <span class="menu-title">Master Menu</span>
        <i class="mdi mdi-food menu-icon"></i>
      </a>
    </li>

    <!-- ORDERS / PESANAN -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('vendor/orders') ? 'active' : '' }}" href="{{ route('vendor.orders.index') }}">
        <span class="menu-title">Pesanan</span>
        <i class="mdi mdi-cart menu-icon"></i>
      </a>
    </li>

  </ul>
</nav>