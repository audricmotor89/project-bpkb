<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StockListController extends Controller
{
    // ── Halaman Stock List utama ──────────────────────────────────────────

    public function index(Request $request)
    {
        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $stock = $this->query($request)
            ->paginate(30)
            ->withQueryString();

        $summary = [
            'total'    => $this->query($request)->count(),
            'bpkb'     => $this->query($request)->where('jenis_jaminan', 'BPKB')->count(),
            'sertif'   => $this->query($request)->where('jenis_jaminan', 'SERTIFIKAT')->count(),
            'overdue'  => $this->query($request)
                            ->whereRaw('DATEDIFF(CURDATE(), DATE(tgl_diproses)) > 1')
                            ->count(),
        ];

        return view('stock.index', compact('stock', 'cabangList', 'summary'));
    }

    // ── Print QR satu item ────────────────────────────────────────────────

    public function printQr(Pengajuan $pengajuan)
    {
        $pengajuan->load(['cabang', 'detailBpkb', 'detailSertifikat']);
        $qrUrl = route('stock.scan', $pengajuan->qr_token);
        $qrSvg = QrCode::format('svg')->size(200)->errorCorrection('H')->generate($qrUrl);

        return view('stock.print-qr', compact('pengajuan', 'qrSvg', 'qrUrl'));
    }

    // ── Print QR batch — semua yang overdue > 1 hari ─────────────────────

    public function printQrBatch(Request $request)
    {
        $items = $this->query($request)
            ->whereRaw('DATEDIFF(CURDATE(), DATE(tgl_diproses)) > 1')
            ->with(['cabang', 'detailBpkb', 'detailSertifikat'])
            ->get();

        $qrItems = $items->map(function ($p) {
            $url = route('stock.scan', $p->qr_token);
            return [
                'pengajuan' => $p,
                'qrSvg'     => QrCode::format('svg')->size(160)->errorCorrection('H')->generate($url),
                'qrUrl'     => $url,
            ];
        });

        return view('stock.print-qr-batch', compact('qrItems'));
    }

    // ── Halaman konfirmasi scan QR (tanpa filter role — semua yg login) ──

    public function scan(string $token)
    {
        $pengajuan = Pengajuan::where('qr_token', $token)
            ->with(['cabang', 'detailBpkb', 'detailSertifikat', 'pengambilnya'])
            ->firstOrFail();

        $qrUrl = route('stock.scan', $token);
        $qrSvg = QrCode::format('svg')->size(140)->errorCorrection('H')->generate($qrUrl);

        return view('stock.scan', compact('pengajuan', 'qrSvg'));
    }

    // ── Konfirmasi diambil via QR ─────────────────────────────────────────

    public function confirmAmbil(Request $request, string $token)
    {
        $pengajuan = Pengajuan::where('qr_token', $token)->firstOrFail();

        if ($pengajuan->tgl_diambil) {
            return redirect()->route('stock.scan', $token)
                ->with('info', 'Item ini sudah pernah dikonfirmasi diambil.');
        }

        if ($pengajuan->status !== 'DISETUJUI') {
            return redirect()->route('stock.scan', $token)
                ->with('error', 'Hanya item berstatus DISETUJUI yang dapat dikonfirmasi.');
        }

        $fotoPath = null;
        if ($request->hasFile('foto_pengambilan')) {
            $fotoPath = $request->file('foto_pengambilan')
                ->store('foto-pengambilan', 'public');
        }

        $pengajuan->update([
            'tgl_diambil'       => now(),
            'diambil_oleh'      => Auth::user()->id,
            'foto_pengambilan'  => $fotoPath,
        ]);

        return redirect()->route('stock.scan', $token)
            ->with('success', "✓ {$pengajuan->no_pengajuan} berhasil dikonfirmasi DIAMBIL oleh " . Auth::user()->nama_lengkap);
    }

    // ── Tandai diambil dari halaman stock list (tombol manual) ───────────

    public function tandaiDiambil(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->tgl_diambil) {
            return back()->with('error', 'Item ini sudah ditandai diambil.');
        }

        $request->validate([
            'foto_pengambilan' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'foto_pengambilan.required' => 'Foto konsumen menerima jaminan wajib diupload.',
            'foto_pengambilan.mimes'    => 'Foto harus berformat JPG atau PNG.',
            'foto_pengambilan.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $fotoPath = $request->file('foto_pengambilan')
            ->store('foto-pengambilan', 'public');

        $pengajuan->update([
            'tgl_diambil'      => now(),
            'diambil_oleh'     => Auth::user()->id,
            'foto_pengambilan' => $fotoPath,
        ]);

        return back()->with('success', "{$pengajuan->no_pengajuan} berhasil ditandai DIAMBIL.");
    }

    // ── Query dasar stock (belum diambil, sudah disetujui) ───────────────

    private function query(Request $request)
    {
        return Pengajuan::with(['cabang', 'detailBpkb', 'detailSertifikat'])
            ->where('status', 'DISETUJUI')
            ->whereNull('tgl_diambil')
            ->when($request->jenis,     fn($q) => $q->where('jenis_jaminan', $request->jenis))
            ->when($request->cabang_id, fn($q) => $q->where('cabang_id', $request->cabang_id))
            ->when($request->aging_min !== null && $request->aging_min !== '',
                fn($q) => $q->whereRaw('DATEDIFF(CURDATE(), DATE(tgl_diproses)) >= ?', [$request->aging_min])
            )
            ->orderByRaw('DATEDIFF(CURDATE(), DATE(tgl_diproses)) DESC');
    }
}
