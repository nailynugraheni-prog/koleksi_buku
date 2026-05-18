<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AntrianController extends Controller
{
    public function guestIndex()
    {
        return view('guest.antrian.index', [
            'layanans' => $this->activeLayanans(),
            'stats' => $this->stats(),
            'lastNumber' => session('nomor_antrian'),
        ]);
    }

    public function storeGuest(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'layanan_id' => ['required', 'integer', 'exists:layanans,id'],
        ]);

        $nomorAntrian = DB::transaction(function () use ($data) {
            $last = DB::table('antrians')
                ->lockForUpdate()
                ->orderByDesc('nomor_urut')
                ->first();

            $nextUrut = $last ? ((int) $last->nomor_urut + 1) : 1;
            $nomor = str_pad((string) $nextUrut, 3, '0', STR_PAD_LEFT);

            DB::table('antrians')->insert([
                'nomor_urut' => $nextUrut,
                'nomor_antrian' => $nomor,
                'layanan_id' => $data['layanan_id'],
                'nama' => $data['nama'],
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $nomor;
        });

        return redirect()
            ->route('guest.antrian.index')
            ->with('success', 'Nomor antrian Anda: ' . $nomorAntrian)
            ->with('nomor_antrian', $nomorAntrian);
    }

    public function adminIndex()
    {
        $this->ensureAdmin();

        return view('admin.antrian.index', [
            'rows' => $this->adminRows(),
            'stats' => $this->stats(),
        ]);
    }

    public function call(Request $request, int $id)
    {
        $this->ensureAdmin();

        $affected = DB::table('antrians')
            ->where('id', $id)
            ->whereIn('status', ['waiting', 'skipped'])
            ->update([
                'status' => 'called',
                'called_at' => now(),
                'updated_at' => now(),
            ]);

        if (! $affected) {
            return back()->with('error', 'Antrian tidak bisa dipanggil.');
        }

        return back()->with('success', 'Antrian berhasil dipanggil.');
    }

    public function skip(int $id)
    {
        $this->ensureAdmin();

        $affected = DB::table('antrians')
            ->where('id', $id)
            ->whereIn('status', ['waiting', 'called'])
            ->update([
                'status' => 'skipped',
                'skipped_at' => now(),
                'updated_at' => now(),
            ]);

        if (! $affected) {
            return back()->with('error', 'Antrian tidak bisa di-skip.');
        }

        return back()->with('success', 'Antrian dipindah ke daftar terlambat.');
    }

    public function recall(int $id)
    {
        $this->ensureAdmin();

        $affected = DB::table('antrians')
            ->where('id', $id)
            ->where('status', 'called') // Sesuai routes, recall memanggil ulang status called
            ->update([
                'called_at' => now(),
                'updated_at' => now(),
            ]);

        if (! $affected) {
            return back()->with('error', 'Antrian tidak bisa dipanggil kembali.');
        }

        return back()->with('success', 'Antrian berhasil dipanggil kembali.');
    }

    public function done(int $id)
    {
        $this->ensureAdmin();

        $affected = DB::table('antrians')
            ->where('id', $id)
            ->whereIn('status', ['waiting', 'called', 'skipped'])
            ->update([
                'status' => 'done',
                'done_at' => now(),
                'updated_at' => now(),
            ]);

        if (! $affected) {
            return back()->with('error', 'Antrian tidak bisa diselesaikan.');
        }

        return back()->with('success', 'Antrian selesai.');
    }

    public function boardIndex()
    {
        return view('papan_antrian.index', [
            'stats' => $this->stats(),
            'current' => $this->currentCalled(),
            'activeRows' => $this->activeRows(),
        ]);
    }

    public function snapshot()
    {
        $current = $this->currentCalled();

        return response()->json([
            'stats' => $this->stats(),
            'current' => $current,
            'current_signature' => $current ? $current->id . '-' . $current->called_at : '',
            'admin_rows_html' => $this->renderAdminRowsHtml(),
            'board_rows_html' => $this->renderBoardRowsHtml(),
            'board_current_html' => $this->renderBoardCurrentHtml($current),
        ]);
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::check(), 403);
        abort_unless((int) Auth::user()->idrole === 1, 403);
    }

    private function activeLayanans()
    {
        return DB::table('layanans')
            ->where('status', 'aktif')
            ->orderBy('nama_layanan')
            ->get();
    }

    private function adminRows()
    {
        return DB::table('antrians')
            ->join('layanans', 'antrians.layanan_id', '=', 'layanans.id')
            ->select('antrians.*', 'layanans.nama_layanan', 'layanans.kode_layanan')
            ->orderBy('antrians.nomor_urut')
            ->get();
    }

    private function activeRows()
    {
        return DB::table('antrians')
            ->join('layanans', 'antrians.layanan_id', '=', 'layanans.id')
            ->select('antrians.*', 'layanans.nama_layanan', 'layanans.kode_layanan')
            ->whereIn('antrians.status', ['waiting', 'called', 'skipped'])
            ->orderBy('antrians.nomor_urut')
            ->get();
    }

    private function currentCalled()
    {
        return DB::table('antrians')
            ->join('layanans', 'antrians.layanan_id', '=', 'layanans.id')
            ->select('antrians.*', 'layanans.nama_layanan', 'layanans.kode_layanan')
            ->where('antrians.status', 'called')
            ->orderByDesc('antrians.called_at')
            ->first();
    }

    private function stats(): array
    {
        return [
            'total' => DB::table('antrians')->count(),
            'waiting' => DB::table('antrians')->where('status', 'waiting')->count(),
            'called' => DB::table('antrians')->where('status', 'called')->count(),
            'skipped' => DB::table('antrians')->where('status', 'skipped')->count(),
            'done' => DB::table('antrians')->where('status', 'done')->count(),
        ];
    }

    // ==========================================
    // THREE HELPER FUNCTIONS FOR RENDERING HTML
    // ==========================================

    private function renderAdminRowsHtml(): string
    {
        $rows = $this->adminRows();

        if ($rows->isEmpty()) {
            return '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada antrian terdaftar hari ini.</td></tr>';
        }

        $html = '';
        foreach ($rows as $row) {
            $status = strtolower($row->status);
            $noAntrian = $row->kode_antrian ?? $row->nomor_antrian ?? $row->kode_layanan;
            $namaLayanan = $row->nama_layanan ?? '-';
            
            if (in_array($status, ['menunggu', 'waiting'])) {
                $badge = 'bg-warning text-dark';
            } elseif (in_array($status, ['dipanggil', 'called'])) {
                $badge = 'bg-success';
            } elseif (in_array($status, ['skipped', 'terlambat'])) {
                $badge = 'bg-danger';
            } else {
                $badge = 'bg-secondary';
            }

            $ucStatus = ucfirst($row->status);
            $csrf = csrf_token();

            $html .= "<tr>
                <td class='fw-bold'>{$noAntrian}</td>
                <td>{$namaLayanan}</td>
                <td>{$row->nama}</td>
                <td><span class='badge {$badge}'>{$ucStatus}</span></td>
                <td>
                    <div class='d-flex gap-2'>";

            // Tombol Panggil
            if (in_array($status, ['menunggu', 'waiting'])) {
                $html .= "<form method='POST' action='" . route('admin.antrian.call', $row->id) . "'>
                    <input type='hidden' name='_token' value='{$csrf}'>
                    <button class='btn btn-sm btn-primary'>Panggil</button>
                </form>";
            }

            // Tombol Selesai, Lewati, Panggil Ulang
            if (in_array($status, ['dipanggil', 'called'])) {
                $html .= "<form method='POST' action='" . route('admin.antrian.done', $row->id) . "'>
                    <input type='hidden' name='_token' value='{$csrf}'>
                    <button class='btn btn-sm btn-success'>Selesai</button>
                </form>
                <form method='POST' action='" . route('admin.antrian.skip', $row->id) . "'>
                    <input type='hidden' name='_token' value='{$csrf}'>
                    <button class='btn btn-sm btn-danger'>Lewati</button>
                </form>
                <form method='POST' action='" . route('admin.antrian.recall', $row->id) . "'>
                    <input type='hidden' name='_token' value='{$csrf}'>
                    <button class='btn btn-sm btn-dark'>Panggil Ulang</button>
                </form>";
            }

            // Tombol Panggil Lagi (Skipped)
            if (in_array($status, ['skipped', 'terlambat'])) {
                $html .= "<form method='POST' action='" . route('admin.antrian.call', $row->id) . "'>
                    <input type='hidden' name='_token' value='{$csrf}'>
                    <button class='btn btn-sm btn-info text-white'>Panggil Lagi</button>
                </form>";
            }

            $html .= "</div></td></tr>";
        }

        return $html;
    }

    private function renderBoardRowsHtml(): string
    {
        $rows = $this->activeRows();

        if ($rows->isEmpty()) {
            return '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada antrian aktif saat ini.</td></tr>';
        }

        $html = '';
        foreach ($rows as $row) {
            $noAntrian = $row->kode_antrian ?? $row->nomor_antrian ?? $row->kode_layanan;
            $namaLayanan = $row->nama_layanan ?? '-';
            $badge = (strtolower($row->status) == 'dipanggil' || strtolower($row->status) == 'called') ? 'bg-success' : 'bg-warning text-dark';
            $ucStatus = ucfirst($row->status);

            $html .= "<tr>
                <td class='fw-bold text-dark'>{$noAntrian}</td>
                <td>{$namaLayanan}</td>
                <td>{$row->nama}</td>
                <td><span class='badge {$badge}'>{$ucStatus}</span></td>
            </tr>";
        }

        return $html;
    }

    private function renderBoardCurrentHtml($current): string
    {
        if (! $current) {
            return '<div class="text-center py-4 text-muted"><h5>Belum ada antrian yang dipanggil.</h5></div>';
        }

        $noAntrian = $current->kode_antrian ?? $current->nomor_antrian ?? $current->kode_layanan ?? '-';
        $namaLayanan = $current->nama_layanan ?? '-';

        return "<div class='text-center py-2'>
            <h6 class='text-uppercase tracking-wider text-muted fw-bold'>Sedang Dipanggil</h6>
            <h1 class='display-1 fw-bold text-success my-1' id='current-kode'>{$noAntrian}</h1>
            <p class='fs-4 text-dark mb-0 fw-semibold' id='current-layanan'>{$namaLayanan}</p>
            <p class='text-muted' id='current-nama'>Nama: {$current->nama}</p>
        </div>";
    }
}
