@extends('layouts.app')
@section('title','Jaminan Kerja')
@section('page-title','Daftar Jaminan Kerja Karyawan')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4">
        <a href="{{ route('jaminan-kerja.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ !request('status') ? 'border: 2px solid #1a237e !important;' : '' }}">
                <div class="fs-4 fw-bold text-dark">{{ $summary['semua'] }}</div>
                <div class="small text-muted">Semua</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4">
        <a href="{{ route('jaminan-kerja.index', ['status'=>'AKTIF']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='AKTIF' ? 'border: 2px solid #0a3622 !important;' : '' }}">
                <div class="fs-4 fw-bold text-success">{{ $summary['aktif'] }}</div>
                <div class="small text-muted">Aktif (Tersimpan)</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4">
        <a href="{{ route('jaminan-kerja.index', ['status'=>'KEMBALI']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3" style="{{ request('status')==='KEMBALI' ? 'border: 2px solid #856404 !important;' : '' }}">
                <div class="fs-4 fw-bold text-warning">{{ $summary['kembali'] }}</div>
                <div class="small text-muted">Sudah Dikembalikan</div>
            </div>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">
            Daftar Jaminan Kerja
            @if(request('status'))
                — <span class="badge bg-{{ request('status') === 'AKTIF' ? 'success' : 'warning text-dark' }}">{{ request('status') }}</span>
            @endif
        </h6>
        @if(auth()->user()->isAdminCabang() || auth()->user()->isSuperAdmin())
        <a href="{{ route('jaminan-kerja.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Catat Jaminan Baru
        </a>
        @endif
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="cari" class="form-control form-control-sm" value="{{ request('cari') }}" placeholder="No. jaminan / nama / NIK">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Jaminan</label>
                <select name="jaminan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="AKTE"   @selected(request('jaminan')==='AKTE')>Akte Kelahiran</option>
                    <option value="BPKB"   @selected(request('jaminan')==='BPKB')>BPKB</option>
                    <option value="IJASAH" @selected(request('jaminan')==='IJASAH')>Ijasah</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="AKTIF"   @selected(request('status')==='AKTIF')>Aktif</option>
                    <option value="KEMBALI" @selected(request('status')==='KEMBALI')>Kembali</option>
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
                    <th>No. Jaminan</th>
                    <th>Tanggal</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Cabang</th>
                    <th>Jaminan Diserahkan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $jk)
                <tr>
                    <td><span class="fw-semibold font-monospace">{{ $jk->no_jaminan }}</span></td>
                    <td>{{ $jk->created_at?->format('d/m/Y') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $jk->nama_karyawan }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $jk->no_ktp }}</div>
                    </td>
                    <td>{{ $jk->jabatan }}</td>
                    <td>{{ $jk->cabang?->kode_cabang }}</td>
                    <td>
                        @if($jk->has_akte)   <span class="badge bg-info text-dark me-1">Akte</span> @endif
                        @if($jk->has_bpkb)   <span class="badge bg-primary me-1">BPKB</span> @endif
                        @if($jk->has_ijasah) <span class="badge bg-success me-1">Ijasah</span> @endif
                    </td>
                    <td>
                        @if($jk->status === 'AKTIF')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-warning text-dark">Kembali</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('jaminan-kerja.show', $jk) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data jaminan kerja.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $data->links() }}</div>
</div>
@endsection
