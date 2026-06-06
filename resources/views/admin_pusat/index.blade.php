@extends('layouts.app')
@section('title','Dashboard Admin Pusat')
@section('page-title','Semua Pengajuan Masuk')

@section('content')

{{-- SLA Alert --}}
@if($slaOverdue > 0)
<div class="alert alert-danger d-flex align-items-center gap-3 mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
    <div>
        <strong>Peringatan SLA!</strong>
        Terdapat <strong>{{ $slaOverdue }} pengajuan</strong> yang belum diproses lebih dari <strong>3 hari</strong>.
        Segera tindaklanjuti untuk menghindari keterlambatan.
        <a href="?status=MENUNGGU" class="alert-link ms-1">Lihat pengajuan menunggu →</a>
    </div>
</div>
@endif

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    @foreach(['menunggu'=>['warning','hourglass-split'],'diproses'=>['primary','gear'],'disetujui'=>['success','check-circle'],'ditolak'=>['danger','x-circle']] as $key=>[$color,$icon])
    <div class="col-md-3">
        <a href="?status={{ strtoupper($key) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm {{ request('status') === strtoupper($key) ? 'border border-'.$color.' border-2' : '' }}">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">{{ strtoupper($key) }}</div>
                        <div class="fs-3 fw-bold text-{{ $color }}">{{ $summary[$key] }}</div>
                    </div>
                    <i class="bi bi-{{ $icon }} fs-1 text-{{ $color }} opacity-25"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Daftar Pengajuan</h6>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
            <i class="bi bi-funnel me-1"></i>Filter
        </button>
    </div>

    {{-- Filter --}}
    <div class="collapse {{ request()->hasAny(['cari','cabang_id','jenis','tgl_dari','tgl_sampai']) ? 'show' : '' }}" id="filterPanel">
        <div class="card-body border-bottom bg-light py-3">
            <form method="GET" id="filterForm" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari</label>
                    <input type="text" name="cari" class="form-control form-control-sm" value="{{ request('cari') }}" placeholder="No. pengajuan / nasabah">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Cabang</label>
                    <select name="cabang_id" class="form-select form-select-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangList as $c)
                            <option value="{{ $c->id }}" @selected(request('cabang_id')==$c->id)>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Jenis</label>
                    <select name="jenis" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="BPKB" @selected(request('jenis')=='BPKB')>BPKB</option>
                        <option value="SERTIFIKAT" @selected(request('jenis')=='SERTIFIKAT')>Sertifikat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(['MENUNGGU','DIPROSES','DISETUJUI','DITOLAK'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}" title="Dari tanggal">
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ request('tgl_sampai') }}" title="Sampai tanggal">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-secondary btn-sm w-100"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <form method="POST" action="{{ route('adminpusat.bulk') }}" id="bulkForm">
        @csrf
        <div id="bulkBar" class="d-none bg-primary bg-opacity-10 border-bottom px-3 py-2 align-items-center gap-3">
            <span id="bulkCount" class="fw-semibold text-primary small">0 dipilih</span>
            <select name="status" class="form-select form-select-sm" style="width:160px;" required>
                <option value="">— Ubah Status —</option>
                <option value="DIPROSES">DIPROSES</option>
                <option value="DISETUJUI">DISETUJUI</option>
                <option value="DITOLAK">DITOLAK</option>
            </select>
            <input type="text" name="catatan" class="form-control form-control-sm" style="width:260px;" placeholder="Catatan (wajib jika DITOLAK)">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check-all me-1"></i>Terapkan
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="cancelBulk">Batal</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px;">
                            <input type="checkbox" class="form-check-input" id="checkAll" title="Pilih semua">
                        </th>
                        <th>No. Pengajuan</th>
                        <th>Cabang</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Nasabah</th>
                        <th>Total Pinjaman</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $p)
                    @php
                        $detail   = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat;
                        $overdue  = $p->status === 'MENUNGGU' && $p->tgl_dibuat?->lt(now()->subDays(3));
                        $isFinal  = in_array($p->status, ['DISETUJUI','DITOLAK']);
                    @endphp
                    <tr class="{{ $overdue ? 'table-danger' : ($p->status === 'MENUNGGU' ? 'table-warning' : '') }}">
                        <td>
                            @if(!$isFinal)
                            <input type="checkbox" class="form-check-input row-check" name="ids[]" value="{{ $p->id }}">
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold font-monospace">{{ $p->no_pengajuan }}</span>
                            @if($overdue)
                                <span class="badge bg-danger ms-1" title="Belum diproses > 3 hari"><i class="bi bi-clock-fill"></i> SLA</span>
                            @endif
                        </td>
                        <td>{{ $p->cabang?->nama_cabang }}</td>
                        <td>{{ $p->tgl_dibuat?->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">{{ $p->jenis_jaminan }}</span></td>
                        <td>{{ $detail?->nama_nasabah ?? '-' }}</td>
                        <td>Rp {{ number_format($detail?->total_pinjaman ?? 0, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $p->status }} px-2 py-1">{{ $p->status }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('adminpusat.show', $p) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Proses
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="card-footer bg-white">{{ $pengajuan->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
const checkAll  = document.getElementById('checkAll');
const bulkBar   = document.getElementById('bulkBar');
const bulkCount = document.getElementById('bulkCount');
const rowChecks = () => document.querySelectorAll('.row-check');

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    if (checked > 0) {
        bulkBar.classList.remove('d-none');
        bulkBar.classList.add('d-flex');
        bulkCount.textContent = checked + ' dipilih';
    } else {
        bulkBar.classList.remove('d-flex');
        bulkBar.classList.add('d-none');
    }
    checkAll.indeterminate = checked > 0 && checked < rowChecks().length;
    checkAll.checked = checked > 0 && checked === rowChecks().length;
}

checkAll.addEventListener('change', () => {
    rowChecks().forEach(c => c.checked = checkAll.checked);
    updateBulkBar();
});

document.addEventListener('change', e => {
    if (e.target.classList.contains('row-check')) updateBulkBar();
});

document.getElementById('cancelBulk').addEventListener('click', () => {
    rowChecks().forEach(c => c.checked = false);
    checkAll.checked = false;
    bulkBar.classList.remove('d-flex');
    bulkBar.classList.add('d-none');
});

document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const status  = this.querySelector('[name="status"]').value;
    const catatan = this.querySelector('[name="catatan"]').value.trim();
    const checked = document.querySelectorAll('.row-check:checked').length;
    if (checked === 0) { e.preventDefault(); alert('Pilih minimal satu pengajuan.'); return; }
    if (status === 'DITOLAK' && !catatan) { e.preventDefault(); alert('Catatan wajib diisi saat menolak.'); return; }
    if (!confirm(`Ubah ${checked} pengajuan menjadi "${status}"?`)) e.preventDefault();
});
</script>
@endpush
