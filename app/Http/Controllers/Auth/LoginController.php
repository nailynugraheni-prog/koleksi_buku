<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // kalau ada rute GET /login dipanggil, langsung arahkan ke home (form ada di home)
    public function showLoginForm()
    {
        return redirect()->route('home');
    }

    public function login(Request $request)
    {
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

        $user = DB::table('users')->where('username', $username)->first();

        if (! $user) {
            return redirect()->back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        $dbPassword = $user->password;
        $passwordOk = false;

        if (Hash::check($password, $dbPassword)) {
            $passwordOk = true;
        } else {
            if (hash_equals((string)$dbPassword, (string)$password)) {
                $passwordOk = true;
                try {
                    DB::table('users')->where('iduser', $user->iduser)->update([
                        'password' => Hash::make($password)
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        if (! $passwordOk) {
            return redirect()->back()->withErrors(['password' => 'Password salah.'])->withInput();
        }

        // ambil nama role
        $roleName = DB::table('role')->where('idrole', $user->idrole)->value('nama_role');

        // login ke Auth jika model User ada
        if (class_exists(\App\Models\User::class)) {
            $userModel = \App\Models\User::find($user->iduser);
            if ($userModel) {
                Auth::login($userModel);
            }
        } else {
            session()->put('manual_auth', true);
        }

        $request->session()->regenerate();
        $request->session()->put([
            'user_id' => $user->iduser,
            'user_name' => $user->username,
            'user_role_id' => $user->idrole,
            'user_role_name' => $roleName ?? 'User',
        ]);

        $roleLower = strtolower((string)($roleName ?? ''));

        if ($roleLower === 'admin' || (int)$user->idrole === 1) {
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil!');
        }

        return redirect()->route('home.dashboard')->with('success', 'Login berhasil!');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->forget('manual_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout berhasil!');
    }
}
