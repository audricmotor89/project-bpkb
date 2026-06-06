@extends('layouts.app')
@section('title','Laporan Ijasah Karyawan')
@section('page-title','Laporan Ijasah Karyawan')

@section('content')

{{-- Summary --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-dark">{{ $summary['total'] }}</div>
            <div class="small text-muted">Total Ijasah</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success">{{ $summary['aktif'] }}</div>
            <div class="small text-muted">Ijasah Masih Tersimpan</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning">{{ $summary['kembali'] }}</div>
            <div class="small text-muted">Ijasah Sudah Keluar</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-mortarboard me-2 text-success"></i>Laporan Ijasah Karyawan
        </h6>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.jaminan-kerja.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="cari" class="form-control form-control-sm" value="{{ request('cari') }}" placeholder="Nama / NIK / No. Jaminan">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status Ijasah</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="AKTIF"   @selected(request('status')==='AKTIF')>Masih Tersimpan</option>
                    <option value="KEMBALI" @selected(request('status')==='KEMBALI')>Sudah Keluar</option>
                </select>
            </div>
            @if(auth()->user()->isAdminPusat() || auth()->user()->isSuperAdmin())
            <div class="col-md-2">
                <label class="form-label small mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $c)
                    <option value="{{ $c->id }}" @selected(request('cabang_id') == $c->id)>{{ $c->kode_cabang }}</option>
                    @endforeach
                </select>
            </div>
            @endif
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
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Cabang</th>
                    <th>Tgl. Masuk</th>
                    <th>Foto Penerimaan</th>
                    <th>Status</th>
                    <th>Foto Pengembalian</th>
                    <th>Tgl. Kembali</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $jk)
                @php
                    $fotoPenerima = $jk->lampiran->where('jenis_dokumen','FOTO_PENERIMAAN')->first();
                    $fotoKembali  = $jk->lampiran->where('jenis_dokumen','FOTO_PENGEMBALIAN')->first();
                @endphp
                <tr>
                    <td><span class="fw-semibold font-monospace">{{ $jk->no_jaminan }}</span></td>
                    <td>
                        <div class="fw-semibold">{{ $jk->nama_karyawan }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $jk->no_ktp }}</div>
                    </td>
                    <td>{{ $jk->jabatan }}</td>
                    <td>{{ $jk->cabang?->kode_cabang }}</td>
                    <td>{{ $jk->tgl_masuk_kerja?->format('d/m/Y') }}</td>
                    <td>
                        @if($fotoPenerima)
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $fotoPenerima) }}" target="_blank">
                                <img src="{{ route('jaminan-kerja.lampiran.preview', $fotoPenerima) }}"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($jk->status === 'AKTIF')
                            <span class="badge bg-success">Masih Tersimpan</span>
                        @else
                            <span class="badge bg-warning text-dark">Sudah Keluar</span>
                        @endif
                    </td>
                    <td>
                        @if($fotoKembali)
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $fotoKembali) }}" target="_blank">
                                <img src="{{ route('jaminan-kerja.lampiran.preview', $fotoKembali) }}"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $jk->tgl_dikembalikan?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('jaminan-kerja.show', $jk) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data ijasah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $rows->links() }}</div>
</div>
@endsection
