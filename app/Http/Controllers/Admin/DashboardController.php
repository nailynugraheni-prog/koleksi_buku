<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $counts = [
            'users'    => DB::table('users')->count(),
            'roles'    => DB::table('role')->count(),
            'kategori' => DB::table('kategori')->count(),
            'buku'     => DB::table('buku')->count(),
        ];

        $latestBooks = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->leftJoin('users', 'buku.created_by', '=', 'users.iduser')
            ->select(
                'buku.kode',
                'buku.judul',
                'buku.pengarang',
                'kategori.nama_kategori as kategori',
                'users.username as creator'
            )
            ->orderByDesc('buku.idbuku')
            ->limit(5)
            ->get();

        $topKategori = DB::table('buku')
            ->join('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('kategori.nama_kategori', DB::raw('count(buku.idbuku) as total'))
            ->groupBy('kategori.nama_kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('counts', 'latestBooks', 'topKategori'));
    }
}
