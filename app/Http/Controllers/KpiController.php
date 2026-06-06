<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $bulan = $request->get('bulan', '');

        $cabangList = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        $stats = $cabangList->map(function ($cabang) use ($tahun, $bulan) {
            $base = Pengajuan::where('cabang_id', $cabang->id)
                ->whereYear('tgl_dibuat', $tahun)
                ->when($bulan, fn($q) => $q->whereMonth('tgl_dibuat', $bulan));

            $total     = (clone $base)->count();
            $disetujui = (clone $base)->where('status', 'DISETUJUI')->count();
            $ditolak   = (clone $base)->where('status', 'DITOLAK')->count();
            $diproses  = (clone $base)->where('status', 'DIPROSES')->count();
            $menunggu  = (clone $base)->where('status', 'MENUNGGU')->count();

            $avgJam = (clone $base)
                ->whereNotNull('tgl_diproses')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tgl_dibuat, tgl_diproses)) as avg_jam')
                ->value('avg_jam');

            return [
                'cabang'         => $cabang->nama_cabang,
                'cabang_id'      => $cabang->id,
                'total'          => $total,
                'disetujui'      => $disetujui,
                'ditolak'        => $ditolak,
                'diproses'       => $diproses,
                'menunggu'       => $menunggu,
                'approval_rate'  => $total > 0 ? round($disetujui / $total * 100, 1) : 0,
                'avg_jam_proses' => round($avgJam ?? 0, 1),
                'avg_hari_proses'=> $avgJam ? round($avgJam / 24, 1) : 0,
            ];
        })->sortByDesc('total')->values();

        // Trend bulanan untuk chart
        $trend = Pengajuan::whereYear('tgl_dibuat', $tahun)
            ->selectRaw('MONTH(tgl_dibuat) as bulan, COUNT(*) as total,
                SUM(status="DISETUJUI") as disetujui,
                SUM(status="DITOLAK") as ditolak')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $trendData = [];
        for ($m = 1; $m <= 12; $m++) {
            $trendData[] = [
                'label'    => \Carbon\Carbon::create($tahun, $m, 1)->translatedFormat('M'),
                'total'    => $trend[$m]->total ?? 0,
                'disetujui'=> $trend[$m]->disetujui ?? 0,
                'ditolak'  => $trend[$m]->ditolak ?? 0,
            ];
        }

        return view('kpi.index', compact('stats', 'cabangList', 'tahun', 'bulan', 'trendData'));
    }
}
