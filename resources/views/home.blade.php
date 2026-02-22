<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <!-- small local fixes in case style.css overrides bootstrap util -->
    <style>
      /* hanya mempengaruhi form auth ini supaya tombol selalu full-width */
      .auth-form-light .d-grid .btn,
      .auth-form-light .btn-auth-full {
        display: block;
        width: 100%;
      }

      /* beri jarak antar tombol jika ada dua tombol vertikal */
      .auth-form-light .d-grid .btn + .btn,
      .auth-form-light .btn-auth-full + .btn-auth-full {
        margin-top: 0.5rem;
      }

      /* jika ingin tampilan tombol Google sedikit beda (opsional) */
      .btn-google {
        background: #f76b7b;          /* atau gunakan kelas btn-danger jika mau */
        border: none;
        color: #fff;
      }
    </style>
</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
                <div class="col-lg-4 mx-auto">

                    <div class="auth-form-light text-left p-5">

                        <div class="brand-logo text-center">
                            <img src="{{ asset('assets/images/logo.svg') }}">
                        </div>

                        <h4>Hello! let's get started</h4>
                        <h6 class="font-weight-light">Sign in to continue.</h6>

                        {{-- SUCCESS MESSAGE --}}
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- ERROR GLOBAL --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- FORM LOGIN --}}
                        <form method="POST" action="{{ route('login') }}" class="pt-3">
                            @csrf

                            <div class="form-group">
                                <input type="text"
                                       name="username"
                                       value="{{ old('username') }}"
                                       class="form-control form-control-lg"
                                       placeholder="Username"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-lg"
                                       placeholder="Password"
                                       required>
                            </div>

                            <!-- kedua tombol berada di sini, full-width dan ada gap -->
                            <div class="mt-3 d-grid gap-2">
                                <button type="submit"
                                        class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                    LOGIN
                                </button>

                                <!-- jadikan <a> ini full-width juga dengan class tambahan -->
                                <a href="{{ route('google.redirect') }}"
                                   class="btn btn-google btn-lg font-weight-medium btn-auth-full"
                                   role="button">
                                   Login with Google
                                </a>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/todolist.js') }}"></script>
<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>

</body>
</html>