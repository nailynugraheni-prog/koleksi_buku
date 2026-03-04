<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // daftar barang (paginate) — diurutkan berdasarkan id_barang (asc)
    public function index()
    {
        $barangs = DB::table('barang')
            ->orderBy('id_barang', 'asc')
            ->paginate(12);

        return view('admin.barang.index', compact('barangs'));
    }

    // form tambah
    public function create()
    {
        return view('admin.barang.create');
    }

    // simpan
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        // generate id_barang 8 karakter unik
        do {
            $id = strtoupper(Str::random(8));
        } while (DB::table('barang')->where('id_barang', $id)->exists());

        $createdBy = optional(Auth::user())->iduser ?? Auth::id();

        DB::table('barang')->insert([
            'id_barang'  => $id,
            'nama'       => $data['nama'],
            'harga'      => $data['harga'],
            'timestamp'  => now(),
            'created_by' => $createdBy,
        ]);

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Barang berhasil ditambahkan.');
    }

    // form edit
    public function edit($id)
    {
        $barang = DB::table('barang')->where('id_barang', $id)->first();

        if (! $barang) {
            return redirect()->route('admin.barang.index')
                             ->with('error', 'Barang tidak ditemukan.');
        }

        return view('admin.barang.edit', compact('barang'));
    }

    // update
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $updated = DB::table('barang')->where('id_barang', $id)->update([
            'nama'      => $data['nama'],
            'harga'     => $data['harga'],
            'timestamp' => now(),
        ]);

        if (! $updated) {
            return redirect()->route('admin.barang.index')
                             ->with('error', 'Gagal mengupdate barang (atau tidak ada perubahan).');
        }

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Barang berhasil diupdate.');
    }

    // hapus
    public function destroy($id)
    {
        $deleted = DB::table('barang')->where('id_barang', $id)->delete();

        if (! $deleted) {
            return redirect()->route('admin.barang.index')
                             ->with('error', 'Gagal menghapus barang.');
        }

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Barang berhasil dihapus.');
    }

    public function printLabels(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected' => 'required|array|min:1',
            'selected.*' => 'string|exists:barang,id_barang',
            'start_x' => 'required|integer|min:1|max:5',
            'start_y' => 'required|integer|min:1|max:8',
            'action' => 'nullable|string'
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $columns = 5;
        $rows = 8;
        $perPageSlots = $columns * $rows; // 40


        $startX = (int) $request->start_x; // 1..5
        $startY = (int) $request->start_y; // 1..8


        // posisi start (0-based index)
        $startPos = ($startY - 1) * $columns + ($startX - 1);
        if ($startPos >= $perPageSlots) {
            return back()->withErrors(['start_x' => 'Koordinat start berada di luar kertas label.'])->withInput();
        }


        // ambil barang sesuai urutan selected (selected berisi id_barang)
        $ids = $request->selected;
        $barangsRows = DB::table('barang')->whereIn('id_barang', $ids)->get()->keyBy('id_barang');


        // jaga urutan sesuai $ids
        $items = collect($ids)->map(function($id) use ($barangsRows) {
            return $barangsRows->get($id);
        })->filter()->values()->all();


        // buat halaman (array halaman yang masing2 berisi 40 slot)
        $pages = [];
        $current = array_fill(0, $perPageSlots, null);
        $pos = $startPos;


        foreach ($items as $item) {
            if ($pos >= $perPageSlots) {
                // simpan current page dan buat page baru
                $pages[] = $current;
                $current = array_fill(0, $perPageSlots, null);
                $pos = 0;
            }
            $current[$pos] = $item;
            $pos++;
        }
        // push halaman terakhir
        $pages[] = $current;


        // render view pdf (multi-page)
        $data = [
            'pages' => $pages,
            'columns' => $columns,
            'rows' => $rows,
        ];


        $pdf = PDF::loadView('admin.barang.labels_pdf', $data);
        $filename = 'labels_' . date('Ymd_His') . '.pdf';


        if ($request->action === 'download') {
            return $pdf->download($filename);
        }
        return $pdf->stream($filename);
    }
}