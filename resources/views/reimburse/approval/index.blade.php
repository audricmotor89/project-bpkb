@extends('layouts.app')
@section('title','Approval Reimburse')
@section('page-title','Approval Pengajuan Reimburse')

@section('content')
{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="text-muted small">Menunggu Approval</div><div class="fs-3 fw-bold text-warning">{{ $summary['menunggu'] }}</div></div>
                <i class="bi bi-hourglass-split fs-1 text-warning opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="text-muted small">Disetujui</div><div class="fs-3 fw-bold text-success">{{ $summary['disetujui'] }}</div></div>
                <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="text-muted small">Ditolak</div><div class="fs-3 fw-bold text-danger">{{ $summary['ditolak'] }}</div></div>
                <i class="bi bi-x-circle fs-1 text-danger opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="small opacity-75">Total Dicairkan</div><div class="fw-bold">Rp {{ number_format($summary['total_cair'], 0, ',', '.') }}</div></div>
                <i class="bi bi-cash-stack fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">Daftar Pengajuan Reimburse</h6>
    </div>

    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="cari" class="form-control form-control-sm" value="{{ request('cari') }}" placeholder="No. / nama pemohon">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" @selected(request('cabang_id')==$c->id)>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Kategori</label>
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($kategori as $k => $l)
                        <option value="{{ $k }}" @selected(request('kategori')===$k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="MENUNGGU"  @selected(request('status')==='MENUNGGU')>Menunggu</option>
                    <option value="DISETUJUI" @selected(request('status')==='DISETUJUI')>Disetujui</option>
                    <option value="DITOLAK"   @selected(request('status')==='DITOLAK')>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}" title="Dari">
                <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ request('tgl_sampai') }}" title="Sampai">
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
                    <th>No. Reimburse</th>
                    <th>Cabang</th>
                    <th>Tgl Pengeluaran</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end">Nominal Diajukan</th>
                    <th class="text-end">Nominal Disetujui</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prevBatch = null;
                    $badge     = ['MENUNGGU'=>'warning','DISETUJUI'=>'success','DITOLAK'=>'danger'];
                @endphp
                @forelse($reimburse as $r)
                    @php $isBatchStart = $r->batch_id && $r->batch_id !== $prevBatch; @endphp
                    @if($isBatchStart)
                        @php
                            // Count siblings in current page
                            $batchCount = $reimburse->filter(fn($x) => $x->batch_id === $r->batch_id)->count();
                            $prevBatch  = $r->batch_id;
                        @endphp
                        @if($batchCount > 1)
                        <tr class="table-secondary">
                            <td colspan="10" class="py-1 px-3 fw-semibold small text-secondary">
                                <i class="bi bi-layers me-1"></i>Batch pengajuan — {{ $r->nama_pemohon }} &middot; {{ $batchCount }} item
                            </td>
                        </tr>
                        @endif
                    @endif
                    <tr class="{{ $r->status === 'MENUNGGU' ? 'table-warning' : '' }}">
                        <td class="font-monospace fw-semibold">{{ $r->no_reimburse }}</td>
                        <td>{{ $r->cabang?->nama_cabang }}</td>
                        <td>{{ $r->tanggal_pengeluaran?->format('d/m/Y') }}</td>
                        <td>{{ $r->nama_pemohon }}</td>
                        <td><span class="badge bg-secondary">{{ $r->kategori }}</span></td>
                        <td class="text-muted">{{ Str::limit($r->keterangan, 35) }}</td>
                        <td class="text-end">Rp {{ number_format($r->nominal_diajukan, 0, ',', '.') }}</td>
                        <td class="text-end {{ $r->status === 'DISETUJUI' ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $r->nominal_disetujui ? 'Rp ' . number_format($r->nominal_disetujui, 0, ',', '.') : '-' }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $badge[$r->status] ?? 'secondary' }}">{{ $r->status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center flex-wrap">
                                <a href="{{ route('reimburse.approval.show', $r) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($r->status === 'MENUNGGU')
                                <button type="button" class="btn btn-sm btn-success" title="Setujui"
                                    data-bs-toggle="modal" data-bs-target="#modalSetujui{{ $r->id }}">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                    data-bs-toggle="modal" data-bs-target="#modalTolak{{ $r->id }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data reimburse.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $reimburse->links() }}</div>
</div>

{{-- Modals Setujui & Tolak per item --}}
@foreach($reimburse as $r)
@if($r->status === 'MENUNGGU')

{{-- Modal Setujui --}}
<div class="modal fade" id="modalSetujui{{ $r->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-success"><i class="bi bi-check-circle me-2"></i>Setujui Reimburse</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('reimburse.approval.approve', $r) }}">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        <strong>{{ $r->no_reimburse }}</strong> — {{ $r->nama_pemohon }}<br>
                        {{ $r->keterangan }}<br>
                        <span class="text-primary fw-semibold">Rp {{ number_format($r->nominal_diajukan, 0, ',', '.') }}</span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Disetujui (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nominal_disetujui" class="form-control"
                                value="{{ $r->nominal_diajukan }}" min="1" required>
                        </div>
                        <div class="form-text">Default: nominal yang diajukan. Ubah jika perlu.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan (opsional)</label>
                        <textarea name="catatan_pusat" class="form-control" rows="2"
                            placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirm('Yakin menyetujui reimburse ini?')">
                        <i class="bi bi-check-circle me-1"></i>SETUJUI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="modalTolak{{ $r->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Reimburse</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('reimburse.approval.reject', $r) }}">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        <strong>{{ $r->no_reimburse }}</strong> — {{ $r->nama_pemohon }}<br>
                        {{ $r->keterangan }}<br>
                        <span class="text-primary fw-semibold">Rp {{ number_format($r->nominal_diajukan, 0, ',', '.') }}</span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_pusat" class="form-control" rows="3"
                            placeholder="Tulis alasan penolakan secara jelas..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin menolak reimburse ini?')">
                        <i class="bi bi-x-circle me-1"></i>TOLAK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif
@endforeach

@endsection
