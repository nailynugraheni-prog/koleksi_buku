<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function currentVendorId()
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $vendorId = DB::table('vendor')
            ->where('iduser', $user->iduser)
            ->value('idvendor');

        if (! $vendorId) {
            abort(403, 'Akun vendor belum terhubung ke data vendor.');
        }

        return $vendorId;
    }

    public function index()
    {
        $vendorId = $this->currentVendorId();

        $menus = DB::table('menu as m')
            ->join('vendor as v', 'm.idvendor', '=', 'v.idvendor')
            ->select('m.*', 'v.nama_vendor')
            ->where('m.idvendor', $vendorId)
            ->orderByDesc('m.idmenu')
            ->paginate(10);

        return view('vendor.menu.index', compact('menus'));
    }

    public function create()
    {
        // vendor otomatis dari user yang login
        return view('vendor.menu.create');
    }

    public function store(Request $request)
    {
        $vendorId = $this->currentVendorId();

        $request->validate([
            'nama_menu'   => 'required|string|max:255',
            'harga'       => 'required|integer|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pathGambar = null;
        if ($request->hasFile('path_gambar')) {
            $pathGambar = $request->file('path_gambar')->store('menu', 'public');
        }

        DB::table('menu')->insert([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $pathGambar,
            'idvendor'    => $vendorId,
        ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $vendorId = $this->currentVendorId();

        $menu = DB::table('menu')
            ->where('idmenu', $id)
            ->where('idvendor', $vendorId)
            ->first();

        abort_if(! $menu, 404);

        return view('vendor.menu.edit', compact('menu'));
    }

    public function update(Request $request, $id)
    {
        $vendorId = $this->currentVendorId();

        $request->validate([
            'nama_menu'   => 'required|string|max:255',
            'harga'       => 'required|integer|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $menu = DB::table('menu')
            ->where('idmenu', $id)
            ->where('idvendor', $vendorId)
            ->first();

        abort_if(! $menu, 404);

        $pathGambar = $menu->path_gambar;

        if ($request->hasFile('path_gambar')) {
            if ($pathGambar && Storage::disk('public')->exists($pathGambar)) {
                Storage::disk('public')->delete($pathGambar);
            }

            $pathGambar = $request->file('path_gambar')->store('menu', 'public');
        }

        DB::table('menu')
            ->where('idmenu', $id)
            ->where('idvendor', $vendorId)
            ->update([
                'nama_menu'   => $request->nama_menu,
                'harga'       => $request->harga,
                'path_gambar' => $pathGambar,
            ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy($id)
    {
        $vendorId = $this->currentVendorId();

        $menu = DB::table('menu')
            ->where('idmenu', $id)
            ->where('idvendor', $vendorId)
            ->first();

        abort_if(! $menu, 404);

        if ($menu->path_gambar && Storage::disk('public')->exists($menu->path_gambar)) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        DB::table('menu')
            ->where('idmenu', $id)
            ->where('idvendor', $vendorId)
            ->delete();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}