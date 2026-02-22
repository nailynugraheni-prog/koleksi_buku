@component('mail::message')
# Kode OTP

Kode verifikasi untuk login: **{{ $otp }}**

Kode ini berlaku selama 10 menit.

Jika tidak melakukan permintaan, abaikan email ini.
@endcomponent