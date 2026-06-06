@extends('layouts.app')
@section('title','Laporan Reimburse')
@section('page-title','Laporan Pengajuan Reimburse')

@section('content')
{{-- Summary --}}
<div class="row g-2 mb-4">
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2 px-2">
                <i class="bi bi-folder2 text-secondary fs-5"></i>
                <div class="fw-bold fs-5">{{ $summary['total'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Total</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2 px-2">
                <i class="bi bi-hourglass-split text-warning fs-5"></i>
                <div class="fw-bold fs-5 text-warning">{{ $summary['menunggu'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2 px-2">
                <i class="bi bi-check-circle text-success fs-5"></i>
                <div class="fw-bold fs-5 text-success">{{ $summary['disetujui'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2 px-2">
                <i class="bi bi-x-circle text-danger fs-5"></i>
                <div class="fw-bold fs-5 text-danger">{{ $summary['ditolak'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Ditolak</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-2">
        <div class="card border-0 shadow-sm text-center bg-primary text-white">
            <div class="card-body py-2 px-2">
                <i class="bi bi-cash-stack fs-5"></i>
                <div class="fw-bold" style="font-size:0.8rem;">Rp {{ number_format($summary['total_diajukan'],0,',','.') }}</div>
                <div style="font-size:0.7rem;">Total Diajukan</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-2">
        <div class="card border-0 shadow-sm text-center bg-success text-white">
            <div class="card-body py-2 px-2">
                <i class="bi bi-cash-coin fs-5"></i>
                <div class="fw-bold" style="font-size:0.8rem;">Rp {{ number_format($summary['total_disetujui'],0,',','.') }}</div>
                <div style="font-size:0.7rem;">Total Dicairkan</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">Data Laporan Reimburse</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.reimburse.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('laporan.reimburse.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
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
                    <th>No. Reimburse</th>
                    <th>Tgl Pengeluaran</th>
                    <th>Cabang</th>
                    <th>Pemohon</th>
                    <th>Keterangan</th>
                    <th>Lampiran Bukti</th>
                    <th class="text-end">Total Diajukan</th>
                    <th class="text-end">Total Disetujui</th>
                    <th>Status</th>
                    <th>Diproses Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reimburse as $r)
                @php $badge = ['MENUNGGU'=>'warning','DISETUJUI'=>'success','DITOLAK'=>'danger']; @endphp
                <tr>
                    <td>
                        <a href="{{ route('reimburse.show', $r) }}" class="font-monospace fw-semibold text-decoration-none">
                            {{ $r->no_reimburse }}
                        </a>
                    </td>
                    <td>{{ $r->tanggal_pengeluaran?->format('d/m/Y') }}</td>
                    <td>{{ $r->cabang?->kode_cabang }}</td>
                    <td>{{ $r->nama_pemohon }}</td>
                    <td class="text-muted">{{ Str::limit($r->keterangan, 30) }}</td>
                    <td>
                        {{-- Tabel mini lampiran dengan kategori, jenis, nominal --}}
                        @if($r->lampiran->count() > 0)
                        <table class="table table-borderless mb-0" style="font-size:0.7rem;min-width:260px;">
                            <thead>
                                <tr class="text-muted">
                                    <th class="py-0 px-1">Kategori</th>
                                    <th class="py-0 px-1">Jenis</th>
                                    <th class="py-0 px-1 text-center">📎</th>
                                    <th class="py-0 px-1 text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($r->lampiran as $lamp)
                                <tr>
                                    <td class="py-0 px-1">
                                        <span class="badge bg-secondary" style="font-size:0.65rem;">
                                            {{ $kategori[$lamp->kategori_biaya] ?? ($lamp->kategori_biaya ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="py-0 px-1 text-muted">{{ $lamp->jenis_dokumen }}</td>
                                    <td class="py-0 px-1 text-center">
                                        @if(str_starts_with($lamp->mime_type,'image/'))
                                            <a href="{{ route('reimburse.lampiran.download', $lamp) }}" target="_blank">
                                                <img src="{{ route('reimburse.lampiran.download', $lamp) }}"
                                                    style="width:28px;height:28px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                                            </a>
                                        @else
                                            <a href="{{ route('reimburse.lampiran.download', $lamp) }}" target="_blank">
                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="py-0 px-1 text-end fw-semibold">
                                        {{ $lamp->nominal ? 'Rp '.number_format($lamp->nominal,0,',','.') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="border-top">
                                    <td colspan="3" class="py-0 px-1 text-end fw-bold text-primary" style="font-size:0.7rem;">Total:</td>
                                    <td class="py-0 px-1 text-end fw-bold text-primary" style="font-size:0.7rem;">
                                        Rp {{ number_format($r->lampiran->sum('nominal'),0,',','.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end fw-semibold">Rp {{ number_format($r->nominal_diajukan,0,',','.') }}</td>
                    <td class="text-end {{ $r->status === 'DISETUJUI' ? 'text-success fw-semibold' : 'text-muted' }}">
                        {{ $r->nominal_disetujui ? 'Rp '.number_format($r->nominal_disetujui,0,',','.') : '-' }}
                    </td>
                    <td><span class="badge bg-{{ $badge[$r->status] ?? 'secondary' }}">{{ $r->status }}</span></td>
                    <td>{{ $r->pemrosesnya?->nama_lengkap ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $reimburse->links() }}</div>
</div>
@endsection
