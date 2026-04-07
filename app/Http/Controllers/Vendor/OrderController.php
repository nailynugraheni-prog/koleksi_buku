<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua pesanan
        $orders = DB::table('pesanan')
            ->orderByDesc('idpesanan')
            ->get();

        // ambil detail kalau ada id dipilih
        $selectedId = $request->id;

        $details = [];
        if ($selectedId) {
            $details = DB::table('detail_pesanan as dp')
                ->join('menu as m', 'dp.idmenu', '=', 'm.idmenu')
                ->select('dp.*', 'm.nama_menu')
                ->where('dp.idpesanan', $selectedId)
                ->get();
        }

        return view('vendor.orders.index', compact('orders', 'details', 'selectedId'));
    }
}