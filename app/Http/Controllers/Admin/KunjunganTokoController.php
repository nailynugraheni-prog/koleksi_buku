<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KunjunganTokoController extends Controller
{
    public function listToko()
    {
        $stores = DB::table('stores')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.kunjungan_toko.list_toko', compact('stores'));
    }

    public function storeToko(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        $barcode = $this->generateBarcode($request->nama_toko);

        DB::table('stores')->insert([
            'barcode'    => $barcode,
            'nama_toko'  => $request->nama_toko,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'accuracy'   => $request->accuracy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.kunjungan_toko.list')
            ->with('success', 'Data toko berhasil disimpan.');
    }

    private function generateBarcode(string $namaToko): string
    {
        $tanggal = now()->format('dmY');

        $kata = preg_split('/\s+/', trim($namaToko));
        $inisial = '';

        foreach ($kata as $item) {
            if (!empty($item)) {
                $inisial .= strtoupper(substr($item, 0, 1));
            }
        }

        $barcode = $inisial . $tanggal;

        $counter = 1;
        $finalBarcode = $barcode;

        while (DB::table('stores')->where('barcode', $finalBarcode)->exists()) {
            $finalBarcode = $barcode . '-' . $counter;
            $counter++;
        }

        return $finalBarcode;
    }

    public function printBarcode($barcode)
    {
        $store = DB::table('stores')
            ->where('barcode', $barcode)
            ->first();

        if (!$store) {
            abort(404, 'Toko tidak ditemukan.');
        }

        return view('admin.kunjungan_toko.print_barcode', compact('store'));
    }

    public function downloadBarcodePdf($barcode)
    {
        $store = DB::table('stores')
            ->where('barcode', $barcode)
            ->first();

        if (!$store) {
            abort(404, 'Toko tidak ditemukan.');
        }

        $pdf = Pdf::loadView('admin.kunjungan_toko.print_barcode_pdf', compact('store'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('barcode-' . $store->barcode . '.pdf');
    }

    public function getStoreByBarcode($barcode)
    {
        $store = DB::table('stores')
            ->where('barcode', $barcode)
            ->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    public function titikKunjungan()
    {
        return view('admin.kunjungan_toko.titik_kunjungan');
    }

    public function submitKunjungan(Request $request)
    {
        $request->validate([
            'barcode'         => 'required|string',
            'sales_latitude'  => 'required|numeric',
            'sales_longitude' => 'required|numeric',
            'sales_accuracy'  => 'required|numeric',
        ]);

        $store = DB::table('stores')
            ->where('barcode', $request->barcode)
            ->first();

        if (!$store) {
            return back()->with('error', 'Barcode toko tidak ditemukan.');
        }

        $jarak = $this->haversine(
            $store->latitude,
            $store->longitude,
            $request->sales_latitude,
            $request->sales_longitude
        );

        $thresholdEfektif = 10;
        $status = $jarak <= $thresholdEfektif ? 'DITERIMA' : 'DITOLAK';

        DB::table('visit_logs')->insert([
            'barcode'           => $store->barcode,
            'user_id'           => auth()->id(),
            'sales_latitude'    => $request->sales_latitude,
            'sales_longitude'   => $request->sales_longitude,
            'sales_accuracy'    => $request->sales_accuracy,
            'jarak'             => $jarak,
            'threshold_efektif' => $thresholdEfektif,
            'status'            => $status,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return back()->with('success', 'Kunjungan tersimpan dengan status: ' . $status);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }
}