<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $gUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error','Google auth gagal.');
        }

        if (! $gUser->getEmail()) {
            return redirect()->route('login')->with('error','Akun Google tidak memiliki email.');
        }

        // cari user berdasarkan email
        $user = User::where('email', $gUser->getEmail())->first();

        if (! $user) {
            // buat username dari local part + random suffix supaya unik
            $local = explode('@', $gUser->getEmail())[0];
            $username = $local . '_' . Str::random(4);

            $user = User::create([
                'username' => $username,
                'email' => $gUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                'id_google' => $gUser->getId(),
                // 'idrole' => 2, // set default role jika perlu
            ]);
        } else {
            // update id_google jika belum ada
            if (! $user->id_google) {
                $user->id_google = $gUser->getId();
                $user->save();
            }
        }

        // generate OTP 6 digit
        $otp = random_int(100000, 999999);

        // simpan OTP ke DB sesuai soal (varchar(6))
        $user->otp = (string)$otp;
        $user->save();

        // simpan expiry & attempts di cache (key per user)
        Cache::put('otp_expiry_'.$user->iduser, now()->addMinutes(10), now()->addMinutes(10)); 
        Cache::put('otp_attempts_'.$user->iduser, 0, now()->addMinutes(10));

        // kirim email OTP (gunakan queue jika tersedia)
        Mail::to($user->email)->send(new SendOtpMail($otp));
        // alternatif: Mail::to(...)->queue(new SendOtpMail($otp));

        // simpan iduser pending di session
        session(['otp_user_id' => $user->iduser]);

        return redirect()->route('otp.form')->with('info','OTP telah dikirim ke email Anda.');
    }
}