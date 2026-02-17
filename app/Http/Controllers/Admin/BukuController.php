<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bukus = DB::table('buku')
            ->join('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->leftJoin('users', 'buku.created_by', '=', 'users.iduser')
            ->select(
                'buku.*',
                'kategori.nama_kategori',
                'users.username as created_by_name'
            )
            ->orderBy('idbuku','asc')
            ->get();

        return view('admin.buku.index', compact('bukus'));
    }

    public function generateKode($idkategori)
    {
        $kategori = DB::table('kategori')
            ->where('idkategori', $idkategori)
            ->first();

        if (!$kategori) {
            return response()->json(['kode' => null]);
        }

        $nama = strtoupper($kategori->nama_kategori);

        // Huruf pertama & ketiga
        if (strlen($nama) >= 3) {
            $prefix = $nama[0] . $nama[2];
        } else {
            $prefix = substr($nama, 0, 2);
        }

        $lastBook = DB::table('buku')
            ->where('idkategori', $idkategori)
            ->orderBy('idbuku', 'desc')
            ->first();

        if ($lastBook) {
            $lastNumber = intval(substr($lastBook->kode, -2));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $number = str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        return response()->json([
            'kode' => $prefix . '-' . $number
        ]);
    }

    public function create()
    {
        $kategoris = DB::table('kategori')->get();
        return view('admin.buku.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required'
        ]);

        DB::table('buku')->insert([
            'kode' => $data['kode'],
            'judul' => $data['judul'],
            'pengarang' => $data['pengarang'],
            'idkategori' => $data['idkategori'],
            'created_by' => auth()->user()->iduser
        ]);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $buku = DB::table('buku')->where('idbuku', $id)->first();
        $kategoris = DB::table('kategori')->get();

        if (!$buku) {
            return redirect()->route('admin.buku.index')
                ->with('error', 'Buku tidak ditemukan.');
        }

        return view('admin.buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kode' => 'required|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required'
        ]);

        DB::table('buku')
            ->where('idbuku', $id)
            ->update([
                'kode' => $data['kode'],
                'judul' => $data['judul'],
                'pengarang' => $data['pengarang'],
                'idkategori' => $data['idkategori']
            ]);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy($id)
    {
        DB::table('buku')->where('idbuku', $id)->delete();

        return redirect()->route('admin.buku.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}