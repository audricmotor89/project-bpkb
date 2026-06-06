<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\JaminanKerja;
use App\Models\LampiranJaminanKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanJaminanKerjaController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $query = $this->buildQuery($request, $user);
        $data  = $query->with(['cabang', 'lampiran'])->get();

        $summary = [
            'total'   => $data->count(),
            'aktif'   => $data->where('status', 'AKTIF')->count(),
            'kembali' => $data->where('status', 'KEMBALI')->count(),
        ];

        $rows = $this->buildQuery($request, $user)->with(['cabang', 'lampiran'])->paginate(20)->withQueryString();

        return view('laporan-jaminan-kerja.index', compact('rows', 'cabangList', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $user   = Auth::user();
        $data   = $this->buildQuery($request, $user)->with(['cabang', 'penerimanya', 'pengembaliannya', 'lampiran'])->get();

        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        $cabangNama = $request->cabang_id
            ? optional($cabangList->find($request->cabang_id))->nama_cabang
            : 'Semua Cabang';

        $filter = [
            'status'    => $request->status ?? 'Semua',
            'cabang'    => $cabangNama,
            'tgl_dari'  => $request->tgl_dari,
            'tgl_sampai'=> $request->tgl_sampai,
        ];

        // Encode foto ke base64 untuk PDF
        $data->each(function ($jaminan) {
            $jaminan->lampiran->each(function ($lamp) {
                if (str_starts_with($lamp->mime_type, 'image/')) {
                    $path = Storage::path('private/jaminan-kerja/' . $lamp->nama_file_storage);
                    if (file_exists($path)) {
                        $lamp->base64 = 'data:' . $lamp->mime_type . ';base64,' . base64_encode(file_get_contents($path));
                    }
                }
            });
        });

        $pdf = Pdf::loadView('laporan-jaminan-kerja.pdf', compact('data', 'filter'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-ijasah-' . now()->format('YmdHis') . '.pdf');
    }

    private function buildQuery(Request $request, $user)
    {
        return JaminanKerja::where('has_ijasah', true)
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))
            ->when($request->cabang_id,   fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->tgl_dari,    fn($q) => $q->whereDate('created_at', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai,  fn($q) => $q->whereDate('created_at', '<=', $request->tgl_sampai))
            ->when($request->cari,        fn($q) => $q->where(function ($q2) use ($request) {
                $q2->where('nama_karyawan', 'like', '%'.$request->cari.'%')
                   ->orWhere('no_ktp', 'like', '%'.$request->cari.'%')
                   ->orWhere('no_jaminan', 'like', '%'.$request->cari.'%');
            }))
            ->orderBy('created_at', 'desc');
    }
}
