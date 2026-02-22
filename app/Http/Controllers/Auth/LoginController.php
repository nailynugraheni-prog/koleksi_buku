<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class LoginController extends Controller
{
    // GET /login → langsung ke home (form login ada di home)
    public function showLoginForm()
    {
        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        // =========================
        // 1. VALIDASI INPUT
        // =========================
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $username = $request->input('username');
        $password = $request->input('password');

        // =========================
        // 2. CARI USER
        // =========================
        $user = DB::table('users')->where('username', $username)->first();

        if (! $user) {
            return redirect()->back()
                ->withErrors(['username' => 'Username tidak ditemukan.'])
                ->withInput();
        }

        // =========================
        // 3. CEK PASSWORD
        // =========================
        $passwordOk = false;

        if (Hash::check($password, $user->password)) {
            $passwordOk = true;
        } else {
            // fallback legacy plaintext password
            if (hash_equals((string)$user->password, (string)$password)) {
                $passwordOk = true;

                // upgrade password ke hash
                DB::table('users')
                    ->where('iduser', $user->iduser)
                    ->update([
                        'password' => Hash::make($password)
                    ]);
            }
        }

        if (! $passwordOk) {
            return redirect()->back()
                ->withErrors(['password' => 'Password salah.'])
                ->withInput();
        }

        // =========================
        // 4. GENERATE OTP
        // =========================
        if (empty($user->email)) {
            return redirect()->back()
                ->withErrors(['username' => 'User belum memiliki email.'])
                ->withInput();
        }

        $otp = random_int(100000, 999999);

        // simpan OTP ke DB (sesuai soal)
        DB::table('users')
            ->where('iduser', $user->iduser)
            ->update([
                'otp' => (string) $otp
            ]);

        // =========================
        // 5. SIMPAN OTP KE CACHE (EXPIRY & ATTEMPT)
        // =========================
        Cache::put('otp_expiry_'.$user->iduser, now()->addMinutes(10), now()->addMinutes(10));
        Cache::put('otp_attempts_'.$user->iduser, 0, now()->addMinutes(10));

        // =========================
        // 6. KIRIM EMAIL OTP
        // =========================
        Mail::to($user->email)->send(new SendOtpMail($otp));

        // =========================
        // 7. SIMPAN SESSION PENDING OTP
        // =========================
        session([
            'otp_user_id' => $user->iduser
        ]);

        // ⚠️ JANGAN LOGIN DULU
        // ⚠️ JANGAN Auth::login()
        // ⚠️ JANGAN SET SESSION USER

        // =========================
        // 8. REDIRECT KE HALAMAN OTP
        // =========================
        return redirect()
            ->route('otp.form')
            ->with('info', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout berhasil!');
    }
}