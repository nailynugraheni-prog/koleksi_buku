<!doctype html>
<html lang="id">
<head>
  {{-- Header: meta, title, favicon --}}
  @include('partials._header')

  {{-- Global CSS (vendors, layout) --}}
  @include('partials._style-global')

  {{-- page-specific styles (support dua nama stack) --}}
  @stack('style-page')
  @stack('page-style')
</head>
<body>
  <div class="container-scroller">
    @include('partials._navbar')

    <div class="container-fluid page-body-wrapper">
      @include('partials._sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>

        @include('partials._footer')
      </div>
    </div>
  </div>

  {{-- Global JS --}}
  @include('partials._scripts-global')

  {{-- page-specific scripts (support dua nama stack) --}}
  @stack('script-page')
  @stack('page-js')
</body>
</html>