<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsVendor
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // kalau belum login
        if (!$user) {
            return redirect()->route('home');
        }

        // ambil nama role dari tabel role
        $namaRole = DB::table('role')
            ->where('idrole', $user->idrole)
            ->value('nama_role');

        if ($namaRole === 'vendor') {
            return $next($request);
        }

        abort(403);
    }
}