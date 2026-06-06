@extends('layouts.app')
@section('title','Aging BPKB')
@section('page-title','Laporan Aging BPKB')

@section('content')

{{-- Summary Cards --}}
<div class="row g-2 mb-4">
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <i class="bi bi-folder2 text-secondary fs-5"></i>
                <div class="fw-bold fs-5">{{ $summary['total'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Total BPKB</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center border-top border-3 border-danger">
            <div class="card-body py-2">
                <i class="bi bi-hourglass-split text-danger fs-5"></i>
                <div class="fw-bold fs-5 text-danger">{{ $summary['belum_diambil'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Belum Diambil</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center border-top border-3 border-success">
            <div class="card-body py-2">
                <i class="bi bi-check-circle text-success fs-5"></i>
                <div class="fw-bold fs-5 text-success">{{ $summary['sudah_diambil'] }}</div>
                <div class="text-muted" style="font-size:0.7rem;">Sudah Diambil</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2">
                <div class="small fw-semibold mb-2 text-muted">Aging Belum Diambil</div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-success px-3 py-2" style="font-size:0.8rem;">
                        0 hari&nbsp;<strong>{{ $summary['hari_0'] }}</strong>
                    </span>
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size:0.8rem;">
                        1–7 hari&nbsp;<strong>{{ $summary['hari_1_7'] }}</strong>
                    </span>
                    <span class="badge px-3 py-2" style="background:#fd7e14;font-size:0.8rem;">
                        8–14 hari&nbsp;<strong>{{ $summary['hari_8_14'] }}</strong>
                    </span>
                    <span class="badge bg-danger px-3 py-2" style="font-size:0.8rem;">
                        15–30 hari&nbsp;<strong>{{ $summary['hari_15_30'] }}</strong>
                    </span>
                    <span class="badge bg-dark px-3 py-2" style="font-size:0.8rem;">
                        &gt;30 hari&nbsp;<strong>{{ $summary['hari_30plus'] }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Daftar Aging BPKB</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('aging.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </a>
            <a href="{{ route('aging.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" @selected(request('cabang_id')==$c->id)>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status Pengambilan</label>
                <select name="status_ambil" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="BELUM" @selected(request('status_ambil')==='BELUM')>Belum Diambil</option>
                    <option value="SUDAH" @selected(request('status_ambil')==='SUDAH')>Sudah Diambil</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Aging Min (hari)</label>
                <input type="number" name="aging_min" class="form-control form-control-sm"
                    value="{{ request('aging_min') }}" min="0" placeholder="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Aging Maks (hari)</label>
                <input type="number" name="aging_max" class="form-control form-control-sm"
                    value="{{ request('aging_max') }}" min="0" placeholder="∞">
            </div>
            <div class="col-md-1">
                <button class="btn btn-secondary btn-sm w-100"><i class="bi bi-search"></i></button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('aging.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Cabang</th>
                    <th>Tgl Disetujui</th>
                    <th>Nama Nasabah</th>
                    <th>No. BPKB</th>
                    <th>No. Polisi</th>
                    <th>Merek / Tipe</th>
                    <th>No. Kartu Piutang</th>
                    <th class="text-center">Aging</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $p)
                @php
                    $d     = $p->detailBpkb;
                    $aging = $p->aging_hari;
                    $color = $p->aging_color;
                @endphp
                <tr class="{{ $p->tgl_diambil ? '' : ($aging > 30 ? 'table-danger' : ($aging > 14 ? 'table-warning' : '')) }}">
                    <td class="font-monospace fw-semibold">{{ $p->no_pengajuan }}</td>
                    <td>{{ $p->cabang?->nama_cabang }}</td>
                    <td>{{ $p->tgl_diproses?->format('d/m/Y') }}</td>
                    <td>{{ $d?->nama_nasabah }}</td>
                    <td class="font-monospace">{{ $d?->no_bpkb }}</td>
                    <td>{{ $d?->no_polisi }}</td>
                    <td>{{ $d?->merek_motor }} {{ $d?->tipe_motor }}</td>
                    <td class="font-monospace">{{ $d?->no_kartu_piutang }}</td>
                    <td class="text-center">
                        @if($p->tgl_diambil)
                            <span class="text-muted small">—</span>
                        @else
                            <span class="badge px-2 py-1 fw-bold"
                                style="font-size:0.8rem;
                                @if($color === 'success') background:#198754;color:#fff;
                                @elseif($color === 'warning') background:#ffc107;color:#000;
                                @elseif($color === 'orange') background:#fd7e14;color:#fff;
                                @elseif($color === 'danger') background:#dc3545;color:#fff;
                                @else background:#212529;color:#fff;
                                @endif">
                                {{ $aging }} hari
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($p->tgl_diambil)
                            <span class="badge bg-success">Sudah Diambil</span>
                            <div class="text-muted" style="font-size:0.68rem;">
                                {{ $p->tgl_diambil->format('d/m/Y H:i') }}<br>
                                {{ $p->pengambilnya?->nama_lengkap }}
                            </div>
                        @else
                            <span class="badge bg-secondary">Belum Diambil</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$p->tgl_diambil)
                        <form method="POST" action="{{ route('aging.tandai', $p) }}"
                            onsubmit="return confirm('Tandai BPKB {{ $p->no_pengajuan }} sudah diambil?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-success" title="Tandai sudah diambil">
                                <i class="bi bi-check2-circle me-1"></i>Diambil
                            </button>
                        </form>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Tidak ada data BPKB sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $pengajuan->links() }}</div>
</div>
@endsection
