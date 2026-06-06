<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Cabang;
use App\Models\LampiranDokumen;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminPusatController extends Controller
{
    public function index(Request $request)
    {
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $query = Pengajuan::with(['cabang', 'detailBpkb', 'detailSertifikat', 'pembuatnya'])
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->jenis,      fn($q) => $q->where('jenis_jaminan', $request->jenis))
            ->when($request->cabang_id,  fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->tgl_dari,   fn($q) => $q->whereDate('tgl_dibuat', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai, fn($q) => $q->whereDate('tgl_dibuat', '<=', $request->tgl_sampai))
            ->when($request->cari, fn($q) => $q->where(function ($sub) use ($request) {
                $sub->where('no_pengajuan', 'like', '%'.$request->cari.'%')
                    ->orWhereHas('detailBpkb', fn($d) => $d->where('nama_nasabah', 'like', '%'.$request->cari.'%')
                        ->orWhere('no_kartu_piutang', 'like', '%'.$request->cari.'%'))
                    ->orWhereHas('detailSertifikat', fn($d) => $d->where('nama_nasabah', 'like', '%'.$request->cari.'%')
                        ->orWhere('no_kartu_piutang', 'like', '%'.$request->cari.'%'));
            }))
            ->latest('tgl_dibuat');

        $pengajuan = $query->paginate(20)->withQueryString();

        $summary = [
            'menunggu'  => Pengajuan::where('status', 'MENUNGGU')->count(),
            'diproses'  => Pengajuan::where('status', 'DIPROSES')->count(),
            'disetujui' => Pengajuan::where('status', 'DISETUJUI')->count(),
            'ditolak'   => Pengajuan::where('status', 'DITOLAK')->count(),
        ];

        // SLA: pengajuan MENUNGGU lebih dari 3 hari
        $slaOverdue = Pengajuan::where('status', 'MENUNGGU')
            ->where('tgl_dibuat', '<', now()->subDays(3))
            ->count();

        return view('admin_pusat.index', compact('pengajuan', 'cabangList', 'summary', 'slaOverdue'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['cabang','pembuatnya','pemrosesnya','detailBpkb','detailSertifikat','lampiran.uploader','auditLog.user','komentar.user']);
        return view('admin_pusat.show', compact('pengajuan'));
    }

    public function updateStatus(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'status'  => 'required|in:DIPROSES,DISETUJUI,DITOLAK',
            'catatan' => 'required_if:status,DITOLAK|nullable|string|max:1000',
        ], [
            'status.required'  => 'Status wajib dipilih.',
            'status.in'        => 'Status tidak valid.',
            'catatan.required_if' => 'Catatan alasan wajib diisi saat menolak pengajuan.',
        ]);

        if ($pengajuan->status === 'DISETUJUI' || $pengajuan->status === 'DITOLAK') {
            return back()->with('error', 'Pengajuan yang sudah final tidak dapat diubah statusnya.');
        }

        $statusLama = $pengajuan->status;
        $user = Auth::user();

        $pengajuan->update([
            'status'        => $request->status,
            'catatan_pusat' => $request->catatan,
            'diproses_oleh' => $user->id,
            'tgl_diproses'  => now(),
        ]);

        AuditLog::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id'      => $user->id,
            'aksi'         => 'UBAH_STATUS',
            'status_lama'  => $statusLama,
            'status_baru'  => $request->status,
            'keterangan'   => $request->catatan,
            'ip_address'   => $request->ip(),
        ]);

        // Kirim notifikasi ke pembuat pengajuan
        $pembuat = $pengajuan->pembuatnya;
        if ($pembuat) {
            $tipe  = match($request->status) { 'DISETUJUI' => 'SUCCESS', 'DITOLAK' => 'DANGER', default => 'INFO' };
            $label = match($request->status) { 'DISETUJUI' => 'Disetujui', 'DITOLAK' => 'Ditolak', default => 'Sedang Diproses' };
            Notifikasi::kirim(
                userId: $pembuat->id,
                judul:  "Pengajuan {$label}",
                pesan:  "Pengajuan {$pengajuan->no_pengajuan} telah {$label}" . ($request->catatan ? ". Catatan: {$request->catatan}" : '.'),
                tipe:   $tipe,
                url:    route('pengajuan.show', $pengajuan),
                pengajuanId: $pengajuan->id,
            );
        }

        return redirect()->route('adminpusat.show', $pengajuan)
            ->with('success', 'Status pengajuan berhasil diubah menjadi ' . $request->status . '.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'exists:pengajuan,id',
            'status'  => 'required|in:DIPROSES,DISETUJUI,DITOLAK',
            'catatan' => 'required_if:status,DITOLAK|nullable|string|max:1000',
        ], [
            'ids.required'        => 'Pilih minimal satu pengajuan.',
            'status.required'     => 'Status wajib dipilih.',
            'catatan.required_if' => 'Catatan wajib diisi saat menolak pengajuan.',
        ]);

        $list = Pengajuan::whereIn('id', $request->ids)
            ->whereNotIn('status', ['DISETUJUI', 'DITOLAK'])
            ->get();

        if ($list->isEmpty()) {
            return back()->with('error', 'Tidak ada pengajuan yang dapat diproses (sudah berstatus final).');
        }

        $user  = Auth::user();
        $count = 0;

        foreach ($list as $p) {
            $statusLama = $p->status;
            $p->update([
                'status'        => $request->status,
                'catatan_pusat' => $request->catatan,
                'diproses_oleh' => $user->id,
                'tgl_diproses'  => now(),
            ]);

            AuditLog::create([
                'pengajuan_id' => $p->id,
                'user_id'      => $user->id,
                'aksi'         => 'UBAH_STATUS',
                'status_lama'  => $statusLama,
                'status_baru'  => $request->status,
                'keterangan'   => $request->catatan ? '[Bulk] ' . $request->catatan : '[Bulk Approval]',
                'ip_address'   => $request->ip(),
            ]);

            $pembuat = $p->pembuatnya;
            if ($pembuat) {
                $tipe  = match($request->status) { 'DISETUJUI' => 'SUCCESS', 'DITOLAK' => 'DANGER', default => 'INFO' };
                $label = match($request->status) { 'DISETUJUI' => 'Disetujui', 'DITOLAK' => 'Ditolak', default => 'Sedang Diproses' };
                Notifikasi::kirim(
                    userId:      $pembuat->id,
                    judul:       "Pengajuan {$label}",
                    pesan:       "Pengajuan {$p->no_pengajuan} telah {$label}." . ($request->catatan ? " Catatan: {$request->catatan}" : ''),
                    tipe:        $tipe,
                    url:         route('pengajuan.show', $p),
                    pengajuanId: $p->id,
                );
            }
            $count++;
        }

        return back()->with('success', "{$count} pengajuan berhasil diubah statusnya menjadi {$request->status}.");
    }

    public function downloadLampiran(LampiranDokumen $lampiran)
    {
        $path = 'private/lampiran/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $lampiran->nama_file_asli);
    }
}
