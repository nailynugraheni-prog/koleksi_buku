<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function showForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Tidak ada proses login yang pending.');
        }

        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $id = session('otp_user_id');

        if (!$id) {
            return redirect()->route('login')
                ->with('error', 'Sesi tidak ditemukan.');
        }

        $user = User::where('iduser', $id)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // =====================
        // CEK EXPIRY
        // =====================
        $expiry = Cache::get('otp_expiry_'.$id);

        if (!$expiry || now()->greaterThan($expiry)) {
            $user->update(['otp' => null]);

            Cache::forget('otp_expiry_'.$id);
            Cache::forget('otp_attempts_'.$id);
            session()->forget('otp_user_id');

            return back()->withErrors([
                'otp' => 'OTP kadaluwarsa.'
            ]);
        }

        // =====================
        // CEK ATTEMPTS
        // =====================
        $attempts = Cache::get('otp_attempts_'.$id, 0);

        if ($attempts >= 5) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan.'
            ]);
        }

        // =====================
        // CEK KODE OTP
        // =====================
        if ($user->otp !== $request->otp) {
            Cache::increment('otp_attempts_'.$id);

            return back()->withErrors([
                'otp' => 'OTP salah.'
            ]);
        }

        // =====================
        // ✅ LOGIN USER
        // =====================
        Auth::login($user);
        $request->session()->regenerate();

        // Bersihkan OTP
        $user->update(['otp' => null]);
        Cache::forget('otp_expiry_'.$id);
        Cache::forget('otp_attempts_'.$id);
        session()->forget('otp_user_id');

        // =====================
        // 🔥 REDIRECT BERDASARKAN ROLE
        // =====================
        switch ((int) $user->idrole) {
            case 1:
                return redirect()->route('admin.dashboard');

            case 2:
                return redirect()->route('vendor.dashboard');

            case 3:
                return redirect()->route('customer.dashboard');

            default:
                return redirect('/');
        }
    }

    public function resend(Request $request)
    {
        $id = session('otp_user_id');

        if (!$id) {
            return redirect()->route('login')
                ->with('error', 'Sesi tidak ditemukan.');
        }

        $user = User::where('iduser', $id)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        $resendCount = Cache::get('otp_resend_'.$id, 0);

        if ($resendCount >= 3) {
            return back()->with('error', 'Melebihi batas kirim ulang.');
        }

        $otp = random_int(100000, 999999);

        $user->update([
            'otp' => (string) $otp
        ]);

        Cache::put('otp_expiry_'.$id, now()->addMinutes(10), now()->addMinutes(10));
        Cache::put('otp_attempts_'.$id, 0, now()->addMinutes(10));
        Cache::put('otp_resend_'.$id, $resendCount + 1, now()->addMinutes(15));

        Mail::to($user->email)->send(new SendOtpMail($otp));

        return back()->with('info', 'OTP baru telah dikirim.');
    }
}