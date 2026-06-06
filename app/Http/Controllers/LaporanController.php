<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\LampiranDokumen;
use App\Models\Pengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $query = $this->queryLaporan($request, $user);
        $data  = $query->get();

        $summary = [
            'total'      => $data->count(),
            'bpkb'       => $data->where('jenis_jaminan', 'BPKB')->count(),
            'sertifikat' => $data->where('jenis_jaminan', 'SERTIFIKAT')->count(),
            'menunggu'   => $data->where('status', 'MENUNGGU')->count(),
            'diproses'   => $data->where('status', 'DIPROSES')->count(),
            'disetujui'  => $data->where('status', 'DISETUJUI')->count(),
            'ditolak'    => $data->where('status', 'DITOLAK')->count(),
            'total_pinjaman' => $data->sum(fn($p) => optional($p->detailBpkb)->total_pinjaman
                                                    + optional($p->detailSertifikat)->total_pinjaman),
        ];

        $pengajuan = $this->queryLaporan($request, $user)->paginate(30)->withQueryString();

        return view('laporan.index', compact('pengajuan', 'cabangList', 'summary'));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $data = $this->queryLaporan($request, $user)->get();

        $filename = 'laporan-pengajuan-' . now()->format('YmdHis') . '.xlsx';
        $path     = storage_path('app/private/temp/' . $filename);
        @mkdir(dirname($path), 0755, true);

        $writer = new Writer();
        $writer->openToFile($path);

        $headerStyle = (new Style())->setFontBold();
        $header = Row::fromValues([
            'No. Pengajuan','Tanggal','Cabang','Jenis Jaminan','Nama Nasabah',
            'No. KTP','No. Kartu Piutang','Total Pinjaman','Status',
            'Tanggal Diproses','Diproses Oleh','Catatan',
            'No. Polisi','Merek','Tipe','No. BPKB','No. Sertifikat',
        ], $headerStyle);
        $writer->addRow($header);

        foreach ($data as $p) {
            $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat;
            $writer->addRow(Row::fromValues([
                $p->no_pengajuan,
                $p->tgl_dibuat?->format('d/m/Y H:i'),
                $p->cabang?->nama_cabang,
                $p->jenis_jaminan,
                $detail?->nama_nasabah,
                $detail?->no_ktp,
                $detail?->no_kartu_piutang,
                $detail?->total_pinjaman,
                $p->status,
                $p->tgl_diproses?->format('d/m/Y H:i'),
                $p->pemrosesnya?->nama_lengkap,
                $p->catatan_pusat,
                $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb?->no_polisi : '-',
                $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb?->merek_motor : '-',
                $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb?->tipe_motor : '-',
                $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb?->no_bpkb : '-',
                $p->jenis_jaminan === 'SERTIFIKAT' ? $p->detailSertifikat?->no_sertifikat : '-',
            ]));
        }

        $writer->close();

        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    public function exportPdf(Request $request)
    {
        $user    = Auth::user();
        $data    = $this->queryLaporan($request, $user)->get();
        $summary = [
            'total'      => $data->count(),
            'bpkb'       => $data->where('jenis_jaminan', 'BPKB')->count(),
            'sertifikat' => $data->where('jenis_jaminan', 'SERTIFIKAT')->count(),
            'menunggu'   => $data->where('status', 'MENUNGGU')->count(),
            'diproses'   => $data->where('status', 'DIPROSES')->count(),
            'disetujui'  => $data->where('status', 'DISETUJUI')->count(),
            'ditolak'    => $data->where('status', 'DITOLAK')->count(),
            'total_pinjaman' => $data->sum(fn($p) => optional($p->detailBpkb)->total_pinjaman
                                                    + optional($p->detailSertifikat)->total_pinjaman),
        ];
        $filter = $request->only(['tgl_dari','tgl_sampai','cabang_id','jenis','status']);

        $pdf = Pdf::loadView('laporan.pdf', compact('data','summary','filter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pengajuan-' . now()->format('YmdHis') . '.pdf');
    }

    public function cetakSurat(Pengajuan $pengajuan)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($pengajuan->cabang_id, $user->cabangIds())) abort(403);

        $pengajuan->load(['cabang','pembuatnya','detailBpkb','detailSertifikat']);
        $pdf = Pdf::loadView('laporan.surat-pengajuan', compact('pengajuan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('surat-pengajuan-' . $pengajuan->no_pengajuan . '.pdf');
    }

    public function fotoIndex(Request $request)
    {
        $user       = Auth::user();
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $query = LampiranDokumen::with(['pengajuan.cabang', 'pengajuan.detailBpkb', 'pengajuan.detailSertifikat'])
            ->where('mime_type', 'like', 'image/%')
            ->whereHas('pengajuan', function ($q) use ($user, $request) {
                if ($user->isAdminCabang()) {
                    $q->whereIn('cabang_id', $user->cabangIds());
                }
                if ($request->cabang_id) {
                    $q->where('cabang_id', $request->cabang_id);
                }
                if ($request->jenis) {
                    $q->where('jenis_jaminan', $request->jenis);
                }
                if ($request->tgl_dari) {
                    $q->whereDate('tgl_dibuat', '>=', $request->tgl_dari);
                }
                if ($request->tgl_sampai) {
                    $q->whereDate('tgl_dibuat', '<=', $request->tgl_sampai);
                }
            })
            ->latest('created_at');

        $total  = $query->count();
        $fotos  = $query->paginate(24)->withQueryString();

        return view('laporan.foto', compact('fotos', 'cabangList', 'total'));
    }

    private function queryLaporan(Request $request, $user)
    {
        return Pengajuan::with(['cabang','detailBpkb','detailSertifikat','pembuatnya','pemrosesnya'])
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))
            ->when($request->cabang_id,   fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->jenis,       fn($q) => $q->where('jenis_jaminan', $request->jenis))
            ->when($request->tgl_dari,    fn($q) => $q->whereDate('tgl_dibuat', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai,  fn($q) => $q->whereDate('tgl_dibuat', '<=', $request->tgl_sampai))
            ->when($request->status,      fn($q) => $q->whereIn('status', (array)$request->status))
            ->orderBy('tgl_dibuat');
    }
}
