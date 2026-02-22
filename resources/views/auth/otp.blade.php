@extends('layouts.app')

@section('content')
<div class="container">
  <h3>Masukkan Kode OTP</h3>

  @if(session('info')) <div class="alert alert-info">{{ session('info') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

  <form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <div class="form-group">
      <label for="otp">Kode 6 digit</label>
      <input id="otp" name="otp" type="text" class="form-control" maxlength="6" required autofocus>
      @error('otp') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-primary mt-2">Verifikasi</button>
  </form>

  <form method="POST" action="{{ route('otp.resend') }}" class="mt-2">
    @csrf
    <button class="btn btn-link">Kirim ulang OTP</button>
  </form>
</div>
@endsection