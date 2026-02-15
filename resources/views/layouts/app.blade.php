<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', config('app.name','Laravel'))</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

  <!-- Vite (your app css/js) -->
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  {{-- Style Global --}}
  <style>
    /* contoh style global (sesuaikan preferensimu: light green theme) */
    :root { --brand-100: #e6f9ec; --brand-500: #2fa66b; --brand-700: #1f7a49; }
    body { background: var(--brand-100); }
    .sidebar { min-height: 100vh; background:#fff; border-right:1px solid #e6e6e6; }
    .nav-link.active { background: linear-gradient(90deg, rgba(47,166,107,0.12), transparent); color: var(--brand-700); }
  </style>

</head>
<body>
  <div id="app">
    {{-- NAVBAR partial --}}
    @include('partials.navbar') {{-- pindah file _navbar.html -> resources/views/partials/_navbar.blade.php --}}

    <div class="container-fluid">
      <div class="row g-0">
        {{-- SIDEBAR partial --}}
        <aside class="col-md-3 col-lg-2 sidebar p-3">
          @include('partials.sidebar') {{-- partial sidebar (Dashboard, Buku, Kategori) --}}
        </aside>

        {{-- MAIN content --}}
        <main class="col-md-9 col-lg-10 p-4">
          {{-- halaman bisa memasang page-header --}}
          <div class="mb-3">
            <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
          </div>

          @yield('content')
        </main>
      </div>
    </div>

    {{-- FOOTER partial --}}
    <footer class="mt-4">
      @include('partials.footer')
    </footer>
  </div>

  {{-- JS Global (vite sudah mem-bundle) --}}
  {{-- tambahan JS global kecil jika perlu --}}
  <script>
    // contoh helper: highlight active menu jika tidak pakai routeIs
    document.addEventListener('DOMContentLoaded', function(){
      // nothing for now
    });
  </script>

  {{-- JS Page --}}
  @stack('page-js')
  @yield('page-js')
</body>
</html>
