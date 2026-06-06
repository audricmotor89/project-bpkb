@extends('layouts.app')
@section('title','KPI Per Cabang')
@section('page-title','KPI & Statistik Per Cabang')

@section('content')

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm" style="width:100px;">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm" style="width:120px;">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i + 1 }}" @selected($bulan == $i + 1)>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm"><i class="bi bi-bar-chart me-1"></i>Tampilkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Highlight Top Cabang --}}
@if($stats->isNotEmpty())
<div class="row g-3 mb-4">
    @php
        $topTotal     = $stats->first();
        $topApproval  = $stats->sortByDesc('approval_rate')->first();
        $topTercepat  = $stats->where('avg_jam_proses', '>', 0)->sortBy('avg_jam_proses')->first();
    @endphp
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="text-muted small">Pengajuan Terbanyak</div>
                <div class="fw-bold fs-5">{{ $topTotal['cabang'] }}</div>
                <div class="text-primary fw-semibold">{{ $topTotal['total'] }} pengajuan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="text-muted small">Approval Rate Tertinggi</div>
                <div class="fw-bold fs-5">{{ $topApproval['cabang'] }}</div>
                <div class="text-success fw-semibold">{{ $topApproval['approval_rate'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <div class="text-muted small">Rata-rata Proses Tercepat</div>
                <div class="fw-bold fs-5">{{ $topTercepat ? $topTercepat['cabang'] : '—' }}</div>
                <div class="text-warning fw-semibold">{{ $topTercepat ? $topTercepat['avg_hari_proses'] . ' hari' : '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Trend Chart --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Trend Pengajuan {{ $tahun }}</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Top 5 Cabang (Total Pengajuan)</h6>
            </div>
            <div class="card-body">
                <canvas id="cabangChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Tabel KPI --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Perbandingan Antar Cabang</h6>
        <span class="text-muted small">{{ $tahun }}{{ $bulan ? ' / Bulan ' . $bulan : '' }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cabang</th>
                    <th class="text-center">Total</th>
                    <th class="text-center text-success">Disetujui</th>
                    <th class="text-center text-danger">Ditolak</th>
                    <th class="text-center text-primary">Diproses</th>
                    <th class="text-center text-warning">Menunggu</th>
                    <th class="text-center">Approval Rate</th>
                    <th class="text-center">Avg Proses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats as $i => $s)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $s['cabang'] }}</td>
                    <td class="text-center fw-bold">{{ $s['total'] }}</td>
                    <td class="text-center text-success fw-semibold">{{ $s['disetujui'] }}</td>
                    <td class="text-center text-danger fw-semibold">{{ $s['ditolak'] }}</td>
                    <td class="text-center text-primary">{{ $s['diproses'] }}</td>
                    <td class="text-center text-warning fw-semibold">{{ $s['menunggu'] }}</td>
                    <td class="text-center">
                        @php $rate = $s['approval_rate']; $rc = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger'); @endphp
                        <span class="badge bg-{{ $rc }}">{{ $rate }}%</span>
                    </td>
                    <td class="text-center">
                        @if($s['avg_hari_proses'] > 0)
                            @php $dc = $s['avg_hari_proses'] <= 1 ? 'success' : ($s['avg_hari_proses'] <= 3 ? 'warning' : 'danger'); @endphp
                            <span class="badge bg-{{ $dc }}">{{ $s['avg_hari_proses'] }} hari</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const trendData = @json($trendData);
const labels    = trendData.map(d => d.label);

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Total', data: trendData.map(d => d.total), backgroundColor: 'rgba(13,110,253,0.6)' },
            { label: 'Disetujui', data: trendData.map(d => d.disetujui), backgroundColor: 'rgba(25,135,84,0.6)' },
            { label: 'Ditolak', data: trendData.map(d => d.ditolak), backgroundColor: 'rgba(220,53,69,0.6)' },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

const statsData = @json($stats->take(5)->values());
new Chart(document.getElementById('cabangChart'), {
    type: 'doughnut',
    data: {
        labels: statsData.map(s => s.cabang),
        datasets: [{ data: statsData.map(s => s.total), backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6c757d'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
