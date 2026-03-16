<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function ajax()
    {
        return view('admin.wilayah.ajax');
    }

    public function axios()
    {
        return view('admin.wilayah.axios');
    }

    public function provinsi()
    {
        return DB::table('reg_provinces')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function kota($provinsiId)
    {
        return DB::table('reg_regencies')
            ->select('id', 'name')
            ->where('province_id', $provinsiId)
            ->orderBy('name')
            ->get();
    }

    public function kecamatan($kotaId)
    {
        return DB::table('reg_districts')
            ->select('id', 'name')
            ->where('regency_id', $kotaId)
            ->orderBy('name')
            ->get();
    }

    public function kelurahan($kecamatanId)
    {
        return DB::table('reg_villages')
            ->select('id', 'name')
            ->where('district_id', $kecamatanId)
            ->orderBy('name')
            ->get();
    }
}