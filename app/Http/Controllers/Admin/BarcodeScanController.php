<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BarcodeScanController extends Controller
{
    public function index()
    {
        return view('admin.barang.scan-label');
    }

    public function find($id)
    {
        $barang = DB::table('barang')
            ->where('id_barang', $id)
            ->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama'      => $barang->nama,
                'harga'     => $barang->harga,
            ]
        ]);
    }
}