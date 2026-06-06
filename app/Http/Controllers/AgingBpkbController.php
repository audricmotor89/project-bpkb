<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class AgingBpkbController extends Controller
{
    public function index(Request $request)
    {
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $data = $this->query($request)->get();
        $summary = $this->buildSummary($data);

        $pengajuan = $this->query($request)->paginate(30)->withQueryString();

        return view('aging.index', compact('pengajuan', 'cabangList', 'summary'));
    }

    public function tandaiDiambil(Pengajuan $pengajuan)
    {
        if ($pengajuan->tgl_diambil) {
            return back()->with('error', 'BPKB ini sudah ditandai diambil sebelumnya.');
        }

        $pengajuan->update([
            'tgl_diambil'  => now(),
            'diambil_oleh' => Auth::user()->id,
        ]);

        return back()->with('success', "BPKB {$pengajuan->no_pengajuan} berhasil ditandai sudah diambil.");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->query($request)->get();

        $filename = 'aging-bpkb-' . now()->format('YmdHis') . '.xlsx';
        $path     = storage_path('app/private/temp/' . $filename);
        @mkdir(dirname($path), 0755, true);

        $writer = new Writer();
        $writer->openToFile($path);

        $bold = (new Style())->setFontBold();
        $writer->addRow(Row::fromValues([
            'No. Pengajuan', 'Cabang', 'Tgl Disetujui', 'Nama Nasabah',
            'No. BPKB', 'No. Polisi', 'Merek/Tipe', 'No. Kartu Piutang',
            'Aging (Hari)', 'Status Pengambilan', 'Tgl Diambil', 'Dicatat Oleh',
        ], $bold));

        foreach ($data as $p) {
            $d = $p->detailBpkb;
            $writer->addRow(Row::fromValues([
                $p->no_pengajuan,
                $p->cabang?->nama_cabang,
                $p->tgl_diproses?->format('d/m/Y'),
                $d?->nama_nasabah,
                $d?->no_bpkb,
                $d?->no_polisi,
                trim(($d?->merek_motor ?? '') . ' ' . ($d?->tipe_motor ?? '')),
                $d?->no_kartu_piutang,
                $p->aging_hari,
                $p->tgl_diambil ? 'SUDAH DIAMBIL' : 'BELUM DIAMBIL',
                $p->tgl_diambil?->format('d/m/Y H:i'),
                $p->pengambilnya?->nama_lengkap,
            ]));
        }

        $writer->close();
        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    public function exportPdf(Request $request)
    {
        $data    = $this->query($request)->get();
        $summary = $this->buildSummary($data);

        $pdf = Pdf::loadView('aging.pdf', compact('data', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aging-bpkb-' . now()->format('YmdHis') . '.pdf');
    }

    private function query(Request $request)
    {
        return Pengajuan::with(['cabang', 'detailBpkb', 'pengambilnya'])
            ->where('jenis_jaminan', 'BPKB')
            ->where('status', 'DISETUJUI')
            ->when($request->cabang_id,   fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->status_ambil === 'BELUM',  fn($q) => $q->whereNull('tgl_diambil'))
            ->when($request->status_ambil === 'SUDAH',  fn($q) => $q->whereNotNull('tgl_diambil'))
            ->when($request->aging_min !== null && $request->aging_min !== '',
                fn($q) => $q->whereRaw(
                    'DATEDIFF(CURDATE(), DATE(tgl_diproses)) >= ?', [$request->aging_min]
                )
            )
            ->when($request->aging_max !== null && $request->aging_max !== '',
                fn($q) => $q->whereRaw(
                    'DATEDIFF(CURDATE(), DATE(tgl_diproses)) <= ?', [$request->aging_max]
                )
            )
            ->orderByRaw('DATEDIFF(CURDATE(), DATE(tgl_diproses)) DESC');
    }

    private function buildSummary($data): array
    {
        $belumDiambil = $data->whereNull('tgl_diambil');

        return [
            'total'          => $data->count(),
            'belum_diambil'  => $belumDiambil->count(),
            'sudah_diambil'  => $data->whereNotNull('tgl_diambil')->count(),
            'hari_0'         => $belumDiambil->filter(fn($p) => $p->aging_hari === 0)->count(),
            'hari_1_7'       => $belumDiambil->filter(fn($p) => $p->aging_hari >= 1 && $p->aging_hari <= 7)->count(),
            'hari_8_14'      => $belumDiambil->filter(fn($p) => $p->aging_hari >= 8 && $p->aging_hari <= 14)->count(),
            'hari_15_30'     => $belumDiambil->filter(fn($p) => $p->aging_hari >= 15 && $p->aging_hari <= 30)->count(),
            'hari_30plus'    => $belumDiambil->filter(fn($p) => $p->aging_hari > 30)->count(),
        ];
    }
}
