<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = DB::table('customer')
            ->orderByDesc('idcustomer')
            ->get();

        return view('admin.customer.index', compact('customers'));
    }

    public function createBlob()
    {
        return view('admin.customer.form', [
            'title' => 'Tambah Customer 1 (Simpan BLOB)',
            'action' => route('admin.customer.storeBlob'),
            'mode'   => 'blob',
        ]);
    }

    public function createPath()
    {
        return view('admin.customer.form', [
            'title' => 'Tambah Customer 2 (Simpan File Path)',
            'action' => route('admin.customer.storePath'),
            'mode'   => 'path',
        ]);
    }

    public function storeBlob(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kodepos_kelurahan' => 'required|string|max:50',
            'foto_data' => 'required|string',
        ]);

        [$mime, $binary] = $this->decodeDataUri($data['foto_data']);

        // Perbaikan untuk PostgreSQL bytea menggunakan format Hex
        DB::table('customer')->insert([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'provinsi' => $data['provinsi'],
            'kota' => $data['kota'],
            'kecamatan' => $data['kecamatan'],
            'kodepos_kelurahan' => $data['kodepos_kelurahan'],
            'foto_blob' => DB::raw("'\\x" . bin2hex($binary) . "'"), 
            'foto_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.customer.index')
            ->with('success', 'Customer berhasil disimpan sebagai BLOB.');
    }

    public function storePath(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kodepos_kelurahan' => 'required|string|max:50',
            'foto_data' => 'required|string',
        ]);

        [$mime, $binary] = $this->decodeDataUri($data['foto_data']);

        $ext = match ($mime) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => 'jpg',
        };

        $fileName = 'customer/' . Str::uuid() . '.' . $ext;
        Storage::disk('public')->put($fileName, $binary);

        DB::table('customer')->insert([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'provinsi' => $data['provinsi'],
            'kota' => $data['kota'],
            'kecamatan' => $data['kecamatan'],
            'kodepos_kelurahan' => $data['kodepos_kelurahan'],
            'foto_blob' => null,
            'foto_path' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.customer.index')
            ->with('success', 'Customer berhasil disimpan sebagai file.');
    }

    private function decodeDataUri(string $dataUri): array
    {
        if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.*)$/', $dataUri, $matches)) {
            abort(422, 'Format foto tidak valid.');
        }

        $mime = $matches[1];
        $base64 = str_replace(' ', '+', $matches[2]);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            abort(422, 'Foto tidak bisa dibaca.');
        }

        return [$mime, $binary];
    }
}
