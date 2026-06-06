@extends('layouts.app')
@section('title','Laporan Pengajuan')
@section('page-title','Laporan Pengajuan')

@section('content')
{{-- Summary --}}
<div class="row g-2 mb-4">
    @foreach([
        ['Total','total','secondary','folder2'],
        ['BPKB','bpkb','primary','car-front'],
        ['Sertifikat','sertifikat','info','file-earmark-text'],
        ['Menunggu','menunggu','warning','hourglass-split'],
        ['Diproses','diproses','primary','gear'],
        ['Disetujui','disetujui','success','check-circle'],
        ['Ditolak','ditolak','danger','x-circle'],
    ] as [$label,$key,$color,$icon])
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2 px-2">
                <i class="bi bi-{{ $icon }} text-{{ $color }} fs-5"></i>
                <div class="fw-bold fs-5">{{ $summary[$key] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-12 col-md-2">
        <div class="card border-0 shadow-sm text-center bg-dark text-white">
            <div class="card-body py-2 px-2">
                <i class="bi bi-currency-dollar fs-5"></i>
                <div class="fw-bold" style="font-size:0.85rem;">Rp {{ number_format($summary['total_pinjaman'],0,',','.') }}</div>
                <div style="font-size:0.7rem;">Total Pinjaman</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">Data Laporan</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('laporan.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            @if(!auth()->user()->isAdminCabang())
            <div class="col-md-3">
                <label class="form-label small mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" @selected(request('cabang_id')==$c->id)>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            @endif
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
                    <th>Cabang</th>
                    <th>Jenis</th>
                    <th>Nasabah</th>
                    <th>No. KTP</th>
                    <th>No. Kartu Piutang</th>
                    <th>Total Pinjaman</th>
                    <th>Status</th>
                    <th>Diproses Oleh</th>
                    <th>Cetak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $p)
                @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
                <tr>
                    <td class="font-monospace">{{ $p->no_pengajuan }}</td>
                    <td>{{ $p->tgl_dibuat?->format('d/m/Y') }}</td>
                    <td>{{ $p->cabang?->nama_cabang }}</td>
                    <td><span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">{{ $p->jenis_jaminan }}</span></td>
                    <td>{{ $detail?->nama_nasabah }}</td>
                    <td>{{ $detail?->no_ktp }}</td>
                    <td>{{ $detail?->no_kartu_piutang }}</td>
                    <td>Rp {{ number_format($detail?->total_pinjaman ?? 0,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td>{{ $p->pemrosesnya?->nama_lengkap ?? '-' }}</td>
                    <td>
                        <a href="{{ route('pengajuan.cetak', $p) }}" class="btn btn-sm btn-outline-dark" target="_blank">
                            <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $pengajuan->links() }}</div>
</div>
@endsection
