<?php
// app/Http/Controllers/Admin/PenjualanController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index()
    {
        try {
            $barangs = DB::table('barang')
                ->select('id_barang', 'nama', 'harga')
                ->orderBy('id_barang')
                ->get();
        } catch (\Throwable $e) {
            Log::error('Gagal ambil data barang: '.$e->getMessage());
            $barangs = collect();
            session()->flash('error', 'Tabel `barang` tidak ditemukan. Pastikan tabel sudah ada di database.');
        }

        return view('admin.penjualan.index', compact('barangs'));
    }

    public function findBarang($kode)
    {
        try {
            $barang = DB::table('barang')->where('id_barang', $kode)->first();
        } catch (\Throwable $e) {
            Log::error('findBarang error: '.$e->getMessage());
            return response()->json(['found' => false, 'message' => 'Server error'], 500);
        }

        if (! $barang) {
            return response()->json(['found' => false], 404);
        }

        return response()->json(['found' => true, 'data' => $barang]);
    }

    /**
     * Simpan transaksi — versi compatibel Postgres (pakai RETURNING id_penjualan).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*.id_barang' => 'required|string|exists:barang,id_barang',
            'cart.*.jumlah' => 'required|integer|min:1',
            'cart.*.subtotal' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = $request->input('cart');
        $total = (int) $request->input('total');

        DB::beginTransaction();
        try {
            // gunakan Carbon, kirim string ke query
            $now = Carbon::now()->toDateTimeString();

            // Insert penjualan dan ambil id dengan RETURNING (Postgres)
            // Gunakan DB::select untuk menjalankan INSERT ... RETURNING
            $sql = 'INSERT INTO penjualan ("timestamp", total) VALUES (?, ?) RETURNING id_penjualan';
            $res = DB::select($sql, [$now, $total]);

            if (! isset($res[0]->id_penjualan)) {
                throw new \Exception('Gagal mendapatkan id_penjualan dari DB.');
            }

            $idPenjualan = $res[0]->id_penjualan;

            // siapkan penjualan_detail (sesuai struktur tabel penjualan_detail)
            $detailsToInsert = [];
            foreach ($cart as $item) {
                $idBarang = $item['id_barang'];
                $dbBarang = DB::table('barang')->where('id_barang', $idBarang)->first();
                if (! $dbBarang) {
                    throw new \Exception("Barang dengan kode {$idBarang} tidak ditemukan saat proses simpan.");
                }

                $hargaDb = (int) $dbBarang->harga;
                $jumlah = (int) $item['jumlah'];
                $subtotalClient = (int) $item['subtotal'];

                $subtotalCalc = $hargaDb * $jumlah;
                $subtotal = ($subtotalClient === $subtotalCalc) ? $subtotalClient : $subtotalCalc;

                $detailsToInsert[] = [
                    'id_penjualan' => $idPenjualan,
                    'id_barang' => $idBarang,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            // Insert batch ke penjualan_detail
            DB::table('penjualan_detail')->insert($detailsToInsert);

            DB::commit();

            return response()->json([
                'success' => true,
                'id_penjualan' => $idPenjualan
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal simpan penjualan: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: '.$e->getMessage()
            ], 500);
        }
    }
}