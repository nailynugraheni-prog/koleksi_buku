<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isVendor']);
    }

    public function index()
    {
        $totalMenu = DB::table('menu')->count();
        $totalVendor = DB::table('vendor')->count();
        $totalPesananLunas = DB::table('pesanan')->where('status_bayar', 1)->count();
        $totalPesananPending = DB::table('pesanan')->where('status_bayar', 0)->count();

        return view('vendor.dashboard', compact(
            'totalMenu',
            'totalVendor',
            'totalPesananLunas',
            'totalPesananPending'
        ));
    }
}