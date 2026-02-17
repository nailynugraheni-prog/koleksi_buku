<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">David Grey. H</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

     <!-- Menu Buku -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/buku*') ? 'active' : '' }}" href="{{ url('admin/buku') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>

    <!-- Menu Kategori -->
    <li class="nav-item">
      <a class="nav-link {{ Request::is('admin/kategori*') ? 'active' : '' }}" href="{{ url('admin/kategori') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-shape menu-icon"></i>
      </a>
    </li>
      </a>
    </li>
  </ul>
</nav>