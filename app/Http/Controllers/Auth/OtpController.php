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
        if (! session('otp_user_id')) {
            return redirect()->route('login')->with('error','Tidak ada proses login yang pending.');
        }

        return view('auth.otp'); // buat view di resources/views/auth/otp.blade.php
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $id = session('otp_user_id');
        if (! $id) {
            return redirect()->route('login')->with('error', 'Sesi tidak ditemukan.');
        }

        $user = User::find($id);
        if (! $user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // cek expiry
        $expiry = Cache::get('otp_expiry_'.$id);
        if (! $expiry || now()->greaterThan($expiry)) {
            $user->update(['otp' => null]);
            Cache::forget('otp_expiry_'.$id);
            Cache::forget('otp_attempts_'.$id);
            session()->forget('otp_user_id');

            return back()->withErrors(['otp' => 'OTP kadaluwarsa.']);
        }

        // cek attempts
        $attempts = Cache::get('otp_attempts_'.$id, 0);
        if ($attempts >= 5) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan.']);
        }

        // cek OTP
        if ($user->otp !== $request->otp) {
            Cache::increment('otp_attempts_'.$id);
            return back()->withErrors(['otp' => 'OTP salah.']);
        }

        // ✅ LOGIN SUKSES
        Auth::loginUsingId($user->iduser);
        $request->session()->regenerate();

        // bersihkan otp
        $user->update(['otp' => null]);
        Cache::forget('otp_expiry_'.$id);
        Cache::forget('otp_attempts_'.$id);
        session()->forget('otp_user_id');

        // 🔥 REDIRECT BERDASARKAN ROLE
        if ((int) $user->idrole === 1) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function resend(Request $request)
    {
        $id = session('otp_user_id');
        if (! $id) return redirect()->route('login')->with('error','Sesi tidak ditemukan.');

        $user = User::find($id);
        if (! $user) return redirect()->route('login')->with('error','User tidak ditemukan.');

        // rate-limit resend (contoh: max 3 resends per 15 minutes)
        $resendCount = Cache::get('otp_resend_'.$id, 0);
        if ($resendCount >= 3) {
            return back()->with('error','Melebihi batas kirim ulang. Coba nanti.');
        }

        $otp = random_int(100000, 999999);
        $user->otp = (string)$otp;
        $user->save();

        Cache::put('otp_expiry_'.$id, now()->addMinutes(10), now()->addMinutes(10));
        Cache::put('otp_attempts_'.$id, 0, now()->addMinutes(10));
        Cache::put('otp_resend_'.$id, $resendCount + 1, now()->addMinutes(15));

        Mail::to($user->email)->send(new SendOtpMail($otp));
        return back()->with('info','OTP baru telah dikirim.');
    }
}