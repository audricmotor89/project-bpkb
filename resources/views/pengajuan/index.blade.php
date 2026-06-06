@extends('layouts.app')
@section('title','Daftar Pengajuan')
@section('page-title','Daftar Pengajuan Jaminan')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md">
        <a href="{{ route('pengajuan.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3 {{ !request('status') ? 'border-primary border-2' : '' }}"
                 style="{{ !request('status') ? 'border: 2px solid #1a237e !important;' : '' }}">
                <div class="fs-4 fw-bold text-dark">{{ $summary['semua'] }}</div>
                <div class="small text-muted">Semua</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('pengajuan.index', ['status'=>'MENUNGGU']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='MENUNGGU' ? 'border: 2px solid #856404 !important;' : '' }}">
                <div class="fs-4 fw-bold text-warning">{{ $summary['menunggu'] }}</div>
                <div class="small text-muted">Menunggu</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('pengajuan.index', ['status'=>'DIPROSES']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='DIPROSES' ? 'border: 2px solid #0a58ca !important;' : '' }}">
                <div class="fs-4 fw-bold text-primary">{{ $summary['diproses'] }}</div>
                <div class="small text-muted">Diproses</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('pengajuan.index', ['status'=>'DISETUJUI']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='DISETUJUI' ? 'border: 2px solid #0a3622 !important;' : '' }}">
                <div class="fs-4 fw-bold text-success">{{ $summary['disetujui'] }}</div>
                <div class="small text-muted">Disetujui</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('pengajuan.index', ['status'=>'DITOLAK']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='DITOLAK' ? 'border: 2px solid #58151c !important;' : '' }}">
                <div class="fs-4 fw-bold text-danger">{{ $summary['ditolak'] }}</div>
                <div class="small text-muted">Ditolak</div>
            </div>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">
            Daftar Pengajuan
            @if(request('status'))
                — <span class="badge badge-{{ request('status') }}">{{ request('status') }}</span>
            @endif
        </h6>
        @if(auth()->user()->isAdminCabang() || auth()->user()->isSuperAdmin())
        <div class="d-flex gap-2">
            <a href="{{ route('pengajuan.create-bpkb') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>BPKB
            </a>
            <a href="{{ route('pengajuan.create-sertifikat') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Sertifikat
            </a>
        </div>
        @endif
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="cari" class="form-control form-control-sm" value="{{ request('cari') }}" placeholder="No. pengajuan / nasabah">
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
            <div class="col-md-2">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ request('tgl_sampai') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-secondary btn-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Nasabah</th>
                    <th>No. Kartu Piutang</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $p)
                @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
                <tr>
                    <td><span class="fw-semibold font-monospace">{{ $p->no_pengajuan }}</span></td>
                    <td>{{ $p->tgl_dibuat?->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">
                            {{ $p->jenis_jaminan }}
                        </span>
                    </td>
                    <td>{{ $detail?->nama_nasabah ?? '-' }}</td>
                    <td>{{ $detail?->no_kartu_piutang ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $p->status }} px-2 py-1">{{ $p->status }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('pengajuan.show', $p) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $pengajuan->links() }}</div>
</div>
@endsection
