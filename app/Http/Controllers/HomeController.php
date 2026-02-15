<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ((int)($user->idrole ?? 0) === 1) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home.dashboard');
        }
        return view('home'); // <-- form login kamu ada di resources/views/home.blade.php
    }

    public function dashboard()
    {
        return view('user.dashboard'); // buat file ini bila perlu
    }
}
