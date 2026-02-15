<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // kalau belum login, arahkan ke home (atau login)
        if (!$user) {
            return redirect()->route('home');
        }

        // contoh 1: jika users punya kolom string 'role'
        // if (isset($user->role) && $user->role === 'admin') {
        //     return $next($request);
        // }

        // contoh 2: jika users punya foreign key idrole -> cek tabel role
        $namaRole = DB::table('role')
            ->where('idrole', $user->idrole) // sesuaikan nama kolom user -> idrole
            ->value('nama_role');

        if ($namaRole === 'admin') {
            return $next($request);
        }

        // jika tidak admin, blokir akses
        abort(403);
    }
}
