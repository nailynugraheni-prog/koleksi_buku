<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function startGuest()
    {
        $lastGuest = DB::table('users')
            ->where('username', 'like', 'Guest_%')
            ->orderByDesc('iduser')
            ->value('username');

        $nextNumber = 1;

        if ($lastGuest && preg_match('/Guest_(\d+)/', $lastGuest, $match)) {
            $nextNumber = ((int) $match[1]) + 1;
        }

        $guestName = 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        $iduser = DB::table('users')->insertGetId([
            'username'   => $guestName,
            'password'   => Hash::make(Str::random(32)),
            'idrole'     => 3,
            'email'      => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'iduser');

        session([
            'guest_user_id' => $iduser,
            'guest_name'    => $guestName,
            'cart'          => [],
        ]);

        return redirect()->route('customer.dashboard');
    }

    public function dashboard(Request $request)
    {
        if (!session('guest_user_id')) {
            return redirect()->route('customer.start');
        }

        $vendors = DB::table('vendor')
            ->orderBy('nama_vendor')
            ->get();

        $selectedVendorId = $request->get('vendor_id');

        if (! $selectedVendorId && $vendors->count() > 0) {
            $selectedVendorId = $vendors->first()->idvendor;
        }

        $selectedVendor = null;
        $menus = collect();

        if ($selectedVendorId) {
            $selectedVendor = DB::table('vendor')
                ->where('idvendor', $selectedVendorId)
                ->first();

            $menus = DB::table('menu')
                ->where('idvendor', $selectedVendorId)
                ->orderBy('nama_menu')
                ->get();
        }

        $cart = session('cart', []);
        $cartTotal = collect($cart)->sum('subtotal');

        return view('customer.dashboard', compact(
            'vendors',
            'selectedVendorId',
            'selectedVendor',
            'menus',
            'cart',
            'cartTotal'
        ));
    }

    public function addToCart(Request $request)
    {
        if (!session('guest_user_id')) {
            return redirect()->route('customer.start');
        }

        $request->validate([
            'idmenu' => 'required|integer',
            'jumlah'  => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $menu = DB::table('menu')
            ->where('idmenu', $request->idmenu)
            ->first();

        if (! $menu) {
            return back()->with('error', 'Menu tidak ditemukan.');
        }

        $cart = session('cart', []);
        $idmenu = (int) $menu->idmenu;
        $jumlah = (int) $request->jumlah;
        $harga = (int) $menu->harga;
        $catatan = $request->catatan;

        if (isset($cart[$idmenu])) {
            $cart[$idmenu]['jumlah'] += $jumlah;
            $cart[$idmenu]['subtotal'] = $cart[$idmenu]['jumlah'] * $cart[$idmenu]['harga'];

            if (!empty($catatan)) {
                $cart[$idmenu]['catatan'] = $catatan;
            }
        } else {
            $cart[$idmenu] = [
                'idmenu'      => $idmenu,
                'nama_menu'   => $menu->nama_menu,
                'harga'       => $harga,
                'jumlah'      => $jumlah,
                'subtotal'    => $jumlah * $harga,
                'catatan'     => $catatan,
                'path_gambar' => $menu->path_gambar,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Menu berhasil ditambahkan ke keranjang.');
    }

    public function removeFromCart($idmenu)
    {
        if (!session('guest_user_id')) {
            return redirect()->route('customer.start');
        }

        $cart = session('cart', []);

        if (isset($cart[$idmenu])) {
            unset($cart[$idmenu]);
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}