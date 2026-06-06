<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\LampiranReimburse;
use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReimburseApprovalController extends Controller
{
    public function index(Request $request)
    {
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $query = Reimburse::with(['cabang', 'pembuatnya'])
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->kategori,   fn($q) => $q->where('kategori', $request->kategori))
            ->when($request->cabang_id,  fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->tgl_dari,   fn($q) => $q->whereDate('tanggal_pengeluaran', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai, fn($q) => $q->whereDate('tanggal_pengeluaran', '<=', $request->tgl_sampai))
            ->when($request->cari, fn($q) => $q->where(function ($s) use ($request) {
                $s->where('no_reimburse', 'like', '%'.$request->cari.'%')
                  ->orWhere('nama_pemohon', 'like', '%'.$request->cari.'%');
            }))
            ->latest();

        $reimburse = $query->paginate(20)->withQueryString();

        $summary = [
            'menunggu'   => Reimburse::where('status', 'MENUNGGU')->count(),
            'disetujui'  => Reimburse::where('status', 'DISETUJUI')->count(),
            'ditolak'    => Reimburse::where('status', 'DITOLAK')->count(),
            'total_cair' => Reimburse::where('status', 'DISETUJUI')->sum('nominal_disetujui'),
        ];

        $kategori = Reimburse::labelKategori();

        return view('reimburse.approval.index', compact('reimburse', 'cabangList', 'summary', 'kategori'));
    }

    public function show(Reimburse $reimburse)
    {
        $reimburse->load(['cabang', 'pembuatnya', 'pemrosesnya', 'lampiran.uploader']);
        $kategori = Reimburse::labelKategori();

        $batchItems = collect();
        if ($reimburse->batch_id) {
            $batchItems = Reimburse::with(['lampiran'])
                ->where('batch_id', $reimburse->batch_id)
                ->orderBy('id')
                ->get();
        }

        return view('reimburse.approval.show', compact('reimburse', 'kategori', 'batchItems'));
    }

    public function approve(Request $request, Reimburse $reimburse)
    {
        if ($reimburse->status !== 'MENUNGGU') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'nominal_disetujui' => 'required|numeric|min:1',
            'catatan_pusat'     => 'nullable|string|max:500',
        ], [
            'nominal_disetujui.required' => 'Nominal yang disetujui wajib diisi.',
            'nominal_disetujui.min'      => 'Nominal harus lebih dari 0.',
        ]);

        $reimburse->update([
            'status'            => 'DISETUJUI',
            'nominal_disetujui' => $request->nominal_disetujui,
            'catatan_pusat'     => $request->catatan_pusat,
            'diproses_oleh'     => Auth::user()->id,
            'tgl_diproses'      => now(),
        ]);

        return redirect()->route('reimburse.approval.index')
            ->with('success', "Reimburse {$reimburse->no_reimburse} telah DISETUJUI. Nominal: Rp " . number_format($request->nominal_disetujui, 0, ',', '.'));
    }

    public function reject(Request $request, Reimburse $reimburse)
    {
        if ($reimburse->status !== 'MENUNGGU') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_pusat' => 'required|string|max:500',
        ], [
            'catatan_pusat.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $reimburse->update([
            'status'        => 'DITOLAK',
            'catatan_pusat' => $request->catatan_pusat,
            'diproses_oleh' => Auth::user()->id,
            'tgl_diproses'  => now(),
        ]);

        return redirect()->route('reimburse.approval.index')
            ->with('success', "Reimburse {$reimburse->no_reimburse} telah DITOLAK.");
    }

    public function downloadLampiran(LampiranReimburse $lampiran)
    {
        $path = 'private/reimburse/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $lampiran->nama_file_asli);
    }
}
