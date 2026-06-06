<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim($request->get('q', ''));
        $user = Auth::user();

        if (strlen($q) < 2) {
            return view('search.index', ['q' => $q, 'pengajuan' => collect(), 'reimburse' => collect()]);
        }

        $pengajuan = Pengajuan::with(['cabang', 'detailBpkb', 'detailSertifikat'])
            ->when($user->isAdminCabang(), fn($qb) => $qb->whereIn('cabang_id', $user->cabangIds()))
            ->where(fn($qb) => $qb
                ->where('no_pengajuan', 'like', "%{$q}%")
                ->orWhereHas('detailBpkb',      fn($d) => $d->where('nama_nasabah', 'like', "%{$q}%")->orWhere('no_bpkb', 'like', "%{$q}%")->orWhere('no_polisi', 'like', "%{$q}%"))
                ->orWhereHas('detailSertifikat', fn($d) => $d->where('nama_nasabah', 'like', "%{$q}%")->orWhere('no_sertifikat', 'like', "%{$q}%"))
            )
            ->latest('tgl_dibuat')->limit(20)->get();

        $reimburse = Reimburse::with(['cabang'])
            ->when($user->isAdminCabang(), fn($qb) => $qb->whereIn('cabang_id', $user->cabangIds()))
            ->where(fn($qb) => $qb
                ->where('no_reimburse', 'like', "%{$q}%")
                ->orWhere('nama_pemohon', 'like', "%{$q}%")
                ->orWhere('keterangan', 'like', "%{$q}%")
            )
            ->latest('tanggal_pengeluaran')->limit(20)->get();

        return view('search.index', compact('q', 'pengajuan', 'reimburse'));
    }
}
