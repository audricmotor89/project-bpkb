@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total Pengajuan', $stats['total'],     'primary', 'folder2'],
        ['Menunggu',        $stats['menunggu'],  'warning', 'hourglass-split'],
        ['Disetujui',       $stats['disetujui'], 'success', 'check-circle'],
        ['Ditolak',         $stats['ditolak'],   'danger',  'x-circle'],
    ] as [$label, $val, $color, $icon])
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-3 fw-bold text-{{ $color }}">{{ $val }}</div>
                </div>
                <i class="bi bi-{{ $icon }} fs-1 text-{{ $color }} opacity-25"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    {{-- Tren Chart --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Tren Pengajuan 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body"><canvas id="trenChart" height="120"></canvas></div>
        </div>
    </div>
    {{-- Donut Chart --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Distribusi Status</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" style="max-height:200px;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Pengajuan Terbaru --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold">Pengajuan Terbaru</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('pengajuan.create-bpkb') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>BPKB</a>
            <a href="{{ route('pengajuan.create-sertifikat') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Sertifikat</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>No. Pengajuan</th><th>Jenis</th><th>Nasabah</th><th>Status</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                @forelse($stats['terbaru'] as $p)
                @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
                <tr>
                    <td><a href="{{ route('pengajuan.show', $p) }}" class="text-decoration-none font-monospace">{{ $p->no_pengajuan }}</a></td>
                    <td><span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">{{ $p->jenis_jaminan }}</span></td>
                    <td>{{ $detail?->nama_nasabah ?? '-' }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td>{{ $p->tgl_dibuat?->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stats['total'] > 5)
    <div class="card-footer bg-white"><a href="{{ route('pengajuan.index') }}" class="text-decoration-none small">Lihat semua →</a></div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const trenLabels = @json($charts['tren_labels']);
new Chart(document.getElementById('trenChart'), {
    type: 'line',
    data: {
        labels: trenLabels,
        datasets: [
            { label: 'Total', data: @json($charts['tren_total']), borderColor: '#1a237e', backgroundColor: 'rgba(26,35,126,0.08)', tension: 0.3, fill: true },
            { label: 'Disetujui', data: @json($charts['tren_disetujui']), borderColor: '#198754', tension: 0.3 },
            { label: 'Ditolak', data: @json($charts['tren_ditolak']), borderColor: '#dc3545', tension: 0.3 },
        ]
    },
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Menunggu','Diproses','Disetujui','Ditolak'],
        datasets: [{ data: @json($charts['status_counts']), backgroundColor: ['#ffc107','#0d6efd','#198754','#dc3545'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
@endpush
