<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    private function setupMidtrans(): void
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function buildItemDetails(array $cart): array
    {
        $itemDetails = [];

        foreach ($cart as $item) {
            $idmenu = $item['idmenu'] ?? null;
            $namaMenu = $item['nama_menu'] ?? null;
            $harga = $item['harga'] ?? null;
            $jumlah = $item['jumlah'] ?? null;

            if ($idmenu === null || $namaMenu === null || $harga === null || $jumlah === null) {
                throw new \Exception('Data keranjang tidak valid.');
            }

            $price = (int) $harga;
            $quantity = (int) $jumlah;
            $name = trim((string) $namaMenu);

            if ($price <= 0) {
                throw new \Exception('Harga menu tidak valid.');
            }

            if ($quantity <= 0) {
                throw new \Exception('Jumlah menu tidak valid.');
            }

            if ($name === '') {
                throw new \Exception('Nama menu tidak valid.');
            }

            $itemDetails[] = [
                'id'       => (string) $idmenu,
                'price'    => $price,
                'quantity' => $quantity,
                'name'     => $name,
            ];
        }

        if (empty($itemDetails)) {
            throw new \Exception('Item details kosong.');
        }

        return $itemDetails;
    }

    public function checkout()
    {
        if (!session('guest_user_id')) {
            return redirect()->route('customer.start');
        }

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        try {
            $itemDetails = $this->buildItemDetails($cart);
            $total = array_sum(array_map(function ($item) {
                return $item['price'] * $item['quantity'];
            }, $itemDetails));

            if ($total <= 0) {
                throw new \Exception('Total pembayaran tidak valid.');
            }

            DB::beginTransaction();

            // Simpan pesanan awal
            $idpesanan = DB::table('pesanan')->insertGetId([
                'nama'         => session('guest_name'),
                'timestamp'    => now(),
                'total'        => 0,
                'metode_bayar' => 0,
                'status_bayar' => 0,
            ], 'idpesanan');

            // Simpan detail pesanan
            foreach ($cart as $item) {
                DB::table('detail_pesanan')->insert([
                    'idpesanan' => $idpesanan,
                    'idmenu'    => $item['idmenu'],
                    'jumlah'    => $item['jumlah'],
                    'harga'     => $item['harga'],
                    'subtotal'  => $item['subtotal'],
                    'catatan'   => $item['catatan'] ?? null,
                    'timestamp' => now(),
                ]);
            }

            $orderId = 'ORDER-' . $idpesanan . '-' . now()->format('YmdHis');

            $this->setupMidtrans();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => session('guest_name') ?? 'Guest',
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::table('pesanan')
                ->where('idpesanan', $idpesanan)
                ->update([
                    'total'             => $total,
                    'order_id_midtrans' => $orderId,
                    'snap_token'        => $snapToken,
                    'timestamp'         => now(),
                ]);

            DB::commit();

            return view('customer.pay', compact('snapToken', 'orderId', 'total'));

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal checkout: ' . $e->getMessage());
        }
    }

    public function notification(Request $request)
    {
        $this->setupMidtrans();

        try {
            $notif = new Notification();
        } catch (\Throwable $e) {
            return response('Invalid notification', 400);
        }

        $orderId = $notif->order_id;
        $status = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status ?? null;

        $pesanan = DB::table('pesanan')
            ->where('order_id_midtrans', $orderId)
            ->first();

        if (! $pesanan) {
            return response('Order not found', 404);
        }

        if ($status === 'settlement') {
            $statusBayar = 1;
        } elseif ($status === 'capture' && $fraudStatus === 'accept') {
            $statusBayar = 1;
        } elseif ($status === 'pending') {
            $statusBayar = 0;
        } else {
            $statusBayar = 2;
        }

        DB::table('pesanan')
            ->where('idpesanan', $pesanan->idpesanan)
            ->update([
                'status_bayar' => $statusBayar,
            ]);

        return response('OK', 200);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            DB::table('pesanan')
                ->where('order_id_midtrans', $orderId)
                ->update([
                    'status_bayar' => 1,
                ]);

            session()->forget('cart');
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Pembayaran berhasil dan status pesanan sudah lunas.');
    }
}