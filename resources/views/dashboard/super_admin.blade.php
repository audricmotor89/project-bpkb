@extends('layouts.app')
@section('title','Dashboard Administrator')
@section('page-title','Dashboard Administrator')

@section('content')
<div class="row g-3 mb-4">
    @foreach([
        ['Total Pengajuan', $stats['total'],        'primary',   'folder2'],
        ['Menunggu',        $stats['menunggu'],      'warning',   'hourglass-split'],
        ['Total Cabang',    $stats['total_cabang'],  'info',      'building'],
        ['Total Pengguna',  $stats['total_users'],   'secondary', 'people'],
    ] as [$label, $val, $color, $icon])
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-bold text-{{ $color }}">{{ $val }}</div></div>
                <i class="bi bi-{{ $icon }} fs-1 text-{{ $color }} opacity-25"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Tren Pengajuan 6 Bulan Terakhir</h6></div>
            <div class="card-body"><canvas id="trenChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Distribusi Status</h6></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" style="max-height:200px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-semibold">Pengajuan Terbaru</h6>
                <a href="{{ route('adminpusat.index') }}" class="btn btn-sm btn-outline-primary">Lihat & Proses</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light"><tr><th>No. Pengajuan</th><th>Cabang</th><th>Jenis</th><th>Nasabah</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($stats['terbaru'] as $p)
                        @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
                        <tr>
                            <td><a href="{{ route('adminpusat.show', $p) }}" class="text-decoration-none font-monospace">{{ $p->no_pengajuan }}</a></td>
                            <td>{{ $p->cabang?->nama_cabang }}</td>
                            <td><span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">{{ $p->jenis_jaminan }}</span></td>
                            <td>{{ $detail?->nama_nasabah ?? '-' }}</td>
                            <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Aksi Cepat</h6></div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.cabang.index') }}" class="btn btn-outline-primary text-start btn-sm"><i class="bi bi-building me-2"></i>Kelola Cabang</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary text-start btn-sm"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
                <a href="{{ route('adminpusat.index') }}?status=MENUNGGU" class="btn btn-outline-warning text-start btn-sm"><i class="bi bi-hourglass-split me-2"></i>Pengajuan Menunggu</a>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary text-start btn-sm"><i class="bi bi-archive me-2"></i>Stock List</a>
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary text-start btn-sm"><i class="bi bi-bar-chart me-2"></i>Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('trenChart'), {
    type: 'bar',
    data: {
        labels: @json($charts['tren_labels']),
        datasets: [
            { label: 'Total', data: @json($charts['tren_total']), backgroundColor: 'rgba(26,35,126,0.15)', borderColor: '#1a237e', borderWidth: 1 },
            { label: 'Disetujui', data: @json($charts['tren_disetujui']), backgroundColor: 'rgba(25,135,84,0.6)' },
            { label: 'Ditolak', data: @json($charts['tren_ditolak']), backgroundColor: 'rgba(220,53,69,0.6)' },
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
