<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        
        $vendorId = session('vendor_id');

        $orders = DB::table('pesanan as p')
            ->when($vendorId, function ($query) use ($vendorId) {
                $query->where('p.idvendor', $vendorId);
            })
            ->select(
                'p.idpesanan',
                'p.nama',
                'p.total',
                'p.status_bayar'
            )
            ->orderByDesc('p.idpesanan')
            ->get();

        $selectedId = $request->query('id');
        $details = collect();

        if ($selectedId) {
            $details = DB::table('detail_pesanan as dp')
                ->join('menu as m', 'm.idmenu', '=', 'dp.idmenu')
                ->where('dp.idpesanan', $selectedId)
                ->select(
                    'm.nama_menu',
                    'dp.jumlah',
                    'dp.harga',
                    'dp.subtotal'
                )
                ->get();
        }

        return view('vendor.orders.index', compact('orders', 'details', 'selectedId'));
    }

    public function scanQr()
    {
        return view('vendor.orders.scan-qr');
    }

    public function scanQrResult(Request $request)
    {
        $request->validate([
            'qr_value' => 'required|string',
        ]);

        $vendorId = session('vendor_id'); // ganti jika session kamu beda
        $rawValue = trim($request->qr_value);

        $decoded = json_decode($rawValue, true);
        $idPesanan = is_array($decoded)
            ? ($decoded['idpesanan'] ?? $decoded['id'] ?? null)
            : $rawValue;

        if (!$idPesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Isi QR code tidak valid.',
            ], 422);
        }

        $order = DB::table('pesanan as p')
            ->when($vendorId, function ($query) use ($vendorId) {
                $query->where('p.idvendor', $vendorId);
            })
            ->where('p.idpesanan', $idPesanan)
            ->select(
                'p.idpesanan',
                'p.nama',
                'p.total',
                'p.status_bayar'
            )
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan untuk vendor ini.',
            ], 404);
        }

        $details = DB::table('detail_pesanan as dp')
            ->join('menu as m', 'm.idmenu', '=', 'dp.idmenu')
            ->where('dp.idpesanan', $idPesanan)
            ->select(
                'm.nama_menu',
                'dp.jumlah',
                'dp.harga',
                'dp.subtotal'
            )
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'QR code berhasil dibaca.',
            'order' => $order,
            'details' => $details,
        ]);
    }
}