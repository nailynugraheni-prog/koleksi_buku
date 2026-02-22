<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman dashboard.
     */
    public function index()
    {
        $data = $this->fetchDashboardData();

        return view('admin.dashboard', $data);
    }


    public function dashboardPdf()
    {
        try {
            $data = $this->fetchDashboardData();

            // opsi untuk dompdf (UTF-8 support & remote assets jika perlu)
            $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'defaultFont'     => 'DejaVu Sans',
            ])->loadView('admin.pdf.dashboard', $data)
              ->setPaper('a4', 'portrait');

            $filename = 'dashboard_' . date('Ymd_His') . '.pdf';
            return $pdf->stream($filename);
        } catch (Throwable $e) {
            Log::error('dashboardPdf error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', 'Gagal generate PDF dashboard. Cek log untuk detail.');
        }
    }

    /**
     * Generate sertifikat PDF (A4 landscape) untuk user yang sedang login.
     * Menampilkan nama user dan role.
     * File view: resources/views/admin/pdf/certificate.blade.php
     */
    public function certificatePdf()
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return redirect()->back()->with('error', 'User belum terautentikasi.');
            }

            // Ambil nama role — dua cara: FK idrole atau relasi model user->role()
            $roleName = null;

            // 1) Kalau kolom idrole ada di tabel users
            if (! empty($user->idrole)) {
                $roleName = DB::table('role')->where('idrole', $user->idrole)->value('nama_role');
            }

            // 2) Jika model User punya relasi role() (eloquent)
            if (empty($roleName) && method_exists($user, 'role')) {
                $roleName = optional($user->role)->nama_role;
            }

            $roleName = $roleName ?? 'N/A';

            $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ])->loadView('admin.pdf.certificate', [
                'user'     => $user,
                'roleName' => $roleName,
            ])->setPaper('a4', 'landscape');

            $safeName = $user->username ?? $user->id ?? 'user';
            $filename = 'sertifikat_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $safeName) . '_' . date('Ymd_His') . '.pdf';

            return $pdf->stream($filename);
        } catch (Throwable $e) {
            Log::error('certificatePdf error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal generate sertifikat PDF. Cek log untuk detail.');
        }
    }

    /**
     * Ambil data yang dibutuhkan di dashboard — dipakai oleh index() dan dashboardPdf()
     *
     * @return array
     */
    private function fetchDashboardData(): array
    {
        // counts
        $counts = [
            'users'    => (int) DB::table('users')->count(),
            'roles'    => (int) DB::table('role')->count(),
            'kategori' => (int) DB::table('kategori')->count(),
            'buku'     => (int) DB::table('buku')->count(),
        ];

        // buku terbaru (limit 5)
        $latestBooks = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->leftJoin('users', 'buku.created_by', '=', 'users.iduser')
            ->select(
                'buku.kode',
                'buku.judul',
                'buku.pengarang',
                'kategori.nama_kategori as kategori',
                'users.username as creator'
            )
            ->orderByDesc('buku.idbuku')
            ->limit(5)
            ->get();

        // top kategori (limit 5)
        $topKategori = DB::table('buku')
            ->join('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('kategori.nama_kategori', DB::raw('count(buku.idbuku) as total'))
            ->groupBy('kategori.nama_kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return compact('counts', 'latestBooks', 'topKategori');
    }
}