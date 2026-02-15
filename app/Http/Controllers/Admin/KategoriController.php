<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // list
    public function index()
    {
        $kategoris = DB::table('kategori')->orderBy('idkategori','desc')->get();
        return view('admin.kategori.index', compact('kategoris'));
    }

    // show create form
    public function create()
    {
        return view('admin.kategori.create');
    }

    // store new kategori
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:100',
        ]);

        DB::table('kategori')->insert([
            'nama_kategori' => $data['nama_kategori']
        ]);

        return redirect()->route('admin.kategori.index')->with('success','Kategori berhasil ditambahkan.');
    }

    // show edit form
    public function edit($id)
    {
        $kategori = DB::table('kategori')->where('idkategori', $id)->first();
        if (!$kategori) {
            return redirect()->route('admin.kategori.index')->with('error','Kategori tidak ditemukan.');
        }
        return view('admin.kategori.edit', compact('kategori'));
    }

    // update kategori
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:100',
        ]);

        $updated = DB::table('kategori')->where('idkategori', $id)->update([
            'nama_kategori' => $data['nama_kategori']
        ]);

        if ($updated) {
            return redirect()->route('admin.kategori.index')->with('success','Kategori berhasil diupdate.');
        }

        return redirect()->route('admin.kategori.index')->with('error','Tidak ada perubahan atau gagal update.');
    }

    // delete kategori (cek relasi ke buku)
    public function destroy($id)
    {
        $hasBuku = DB::table('buku')->where('idkategori', $id)->exists();
        if ($hasBuku) {
            return redirect()->route('admin.kategori.index')->with('error','Kategori masih mempunyai buku, hapus/ubah buku terlebih dahulu.');
        }

        $deleted = DB::table('kategori')->where('idkategori', $id)->delete();
        if ($deleted) {
            return redirect()->route('admin.kategori.index')->with('success','Kategori berhasil dihapus.');
        }

        return redirect()->route('admin.kategori.index')->with('error','Gagal menghapus kategori.');
    }
}
