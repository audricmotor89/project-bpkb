<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuan;
use App\Models\Reimburse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $stats  = $this->getStats($user);
        $charts = $this->getCharts($user);

        return match ($user->role) {
            'SUPER_ADMIN'  => view('dashboard.super_admin', compact('user', 'stats', 'charts')),
            'ADMIN_PUSAT'  => view('dashboard.admin_pusat', compact('user', 'stats', 'charts')),
            'ADMIN_CABANG' => view('dashboard.admin_cabang', compact('user', 'stats', 'charts')),
            default        => abort(403),
        };
    }

    private function getStats($user): array
    {
        $base = Pengajuan::query()
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()));

        return [
            'total'        => (clone $base)->count(),
            'menunggu'     => (clone $base)->where('status', 'MENUNGGU')->count(),
            'diproses'     => (clone $base)->where('status', 'DIPROSES')->count(),
            'disetujui'    => (clone $base)->where('status', 'DISETUJUI')->count(),
            'ditolak'      => (clone $base)->where('status', 'DITOLAK')->count(),
            'total_cabang' => Cabang::where('aktif', 1)->count(),
            'total_users'  => User::where('aktif', 1)->count(),
            'terbaru'      => (clone $base)->with(['cabang','detailBpkb','detailSertifikat'])
                                ->latest('tgl_dibuat')->limit(5)->get(),
        ];
    }

    private function getCharts($user): array
    {
        // Tren pengajuan 6 bulan terakhir
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        $tren = $months->map(function ($m) use ($user) {
            $base = Pengajuan::query()
                ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))
                ->whereYear('tgl_dibuat', $m->year)
                ->whereMonth('tgl_dibuat', $m->month);
            return [
                'label'     => $m->translatedFormat('M Y'),
                'total'     => (clone $base)->count(),
                'disetujui' => (clone $base)->where('status', 'DISETUJUI')->count(),
                'ditolak'   => (clone $base)->where('status', 'DITOLAK')->count(),
            ];
        });

        // Distribusi status semua data
        $base = Pengajuan::query()
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()));

        return [
            'tren_labels'     => $tren->pluck('label'),
            'tren_total'      => $tren->pluck('total'),
            'tren_disetujui'  => $tren->pluck('disetujui'),
            'tren_ditolak'    => $tren->pluck('ditolak'),
            'status_counts'   => [
                (clone $base)->where('status', 'MENUNGGU')->count(),
                (clone $base)->where('status', 'DIPROSES')->count(),
                (clone $base)->where('status', 'DISETUJUI')->count(),
                (clone $base)->where('status', 'DITOLAK')->count(),
            ],
        ];
    }
}
