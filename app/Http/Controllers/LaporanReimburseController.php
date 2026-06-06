<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\LampiranReimburse;
use App\Models\Reimburse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class LaporanReimburseController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        $kategori   = Reimburse::labelKategori();

        $data    = $this->query($request, $user)->get();
        $summary = $this->buildSummary($data);

        $reimburse = $this->query($request, $user)->paginate(30)->withQueryString();

        return view('reimburse.laporan.index', compact('reimburse', 'cabangList', 'kategori', 'summary'));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $data = $this->query($request, $user)->get();

        $filename = 'laporan-reimburse-' . now()->format('YmdHis') . '.xlsx';
        $path     = storage_path('app/private/temp/' . $filename);
        @mkdir(dirname($path), 0755, true);

        $writer = new Writer();
        $writer->openToFile($path);

        $bold = (new Style())->setFontBold();
        $writer->addRow(Row::fromValues([
            'No. Reimburse','Tanggal Pengeluaran','Cabang','Nama Pemohon','Jabatan',
            'Kategori','Keterangan','Nominal Diajukan (Rp)','Nominal Disetujui (Rp)',
            'Status','Tanggal Diproses','Diproses Oleh','Catatan',
        ], $bold));

        foreach ($data as $r) {
            $writer->addRow(Row::fromValues([
                $r->no_reimburse,
                $r->tanggal_pengeluaran?->format('d/m/Y'),
                $r->cabang?->nama_cabang,
                $r->nama_pemohon,
                $r->jabatan,
                $r->kategori,
                $r->keterangan,
                $r->nominal_diajukan,
                $r->nominal_disetujui,
                $r->status,
                $r->tgl_diproses?->format('d/m/Y H:i'),
                $r->pemrosesnya?->nama_lengkap,
                $r->catatan_pusat,
            ]));
        }

        $writer->close();
        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    public function exportPdf(Request $request)
    {
        $user    = Auth::user();
        $data    = $this->query($request, $user)->get();
        $summary = $this->buildSummary($data);
        $filter  = $request->only(['tgl_dari','tgl_sampai','cabang_id','kategori','status']);

        $pdf = Pdf::loadView('reimburse.laporan.pdf', compact('data', 'summary', 'filter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-reimburse-' . now()->format('YmdHis') . '.pdf');
    }

    public function fotoIndex(Request $request)
    {
        $user       = Auth::user();
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        $kategori   = Reimburse::labelKategori();

        $query = LampiranReimburse::with(['reimburse.cabang', 'reimburse.pembuatnya'])
            ->where('mime_type', 'like', 'image/%')
            ->whereHas('reimburse', function ($q) use ($user, $request) {
                if ($user->isAdminCabang()) {
                    $q->whereIn('cabang_id', $user->cabangIds());
                }
                if ($request->cabang_id) {
                    $q->where('cabang_id', $request->cabang_id);
                }
                if ($request->kategori) {
                    $q->where('kategori', $request->kategori);
                }
                if ($request->tgl_dari) {
                    $q->whereDate('tanggal_pengeluaran', '>=', $request->tgl_dari);
                }
                if ($request->tgl_sampai) {
                    $q->whereDate('tanggal_pengeluaran', '<=', $request->tgl_sampai);
                }
            })
            ->latest('created_at');

        $total = $query->count();
        $fotos = $query->paginate(24)->withQueryString();

        return view('reimburse.laporan.foto', compact('fotos', 'cabangList', 'kategori', 'total'));
    }

    private function query(Request $request, $user)
    {
        return Reimburse::with(['cabang', 'pembuatnya', 'pemrosesnya', 'lampiran'])
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))
            ->when($request->cabang_id,   fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->kategori,    fn($q) => $q->where('kategori', $request->kategori))
            ->when($request->tgl_dari,    fn($q) => $q->whereDate('tanggal_pengeluaran', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai,  fn($q) => $q->whereDate('tanggal_pengeluaran', '<=', $request->tgl_sampai))
            ->when($request->status,      fn($q) => $q->whereIn('status', (array)$request->status))
            ->orderBy('tanggal_pengeluaran', 'desc');
    }

    private function buildSummary($data): array
    {
        return [
            'total'          => $data->count(),
            'menunggu'       => $data->where('status', 'MENUNGGU')->count(),
            'disetujui'      => $data->where('status', 'DISETUJUI')->count(),
            'ditolak'        => $data->where('status', 'DITOLAK')->count(),
            'total_diajukan' => $data->sum('nominal_diajukan'),
            'total_disetujui'=> $data->where('status', 'DISETUJUI')->sum('nominal_disetujui'),
        ];
    }
}
