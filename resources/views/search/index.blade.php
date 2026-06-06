@extends('layouts.app')
@section('title','Hasil Pencarian')
@section('page-title','Hasil Pencarian')

@section('content')

<div class="mb-3">
    <form action="{{ route('search') }}" method="GET" class="d-flex gap-2" style="max-width:500px;">
        <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Cari pengajuan, nasabah, nomor BPKB..." autofocus>
        <button class="btn btn-primary px-3"><i class="bi bi-search"></i></button>
    </form>
</div>

@if(strlen($q) < 2)
<div class="alert alert-info">Ketik minimal 2 karakter untuk mencari.</div>
@else

{{-- Pengajuan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-folder2 me-2"></i>Pengajuan Jaminan
            <span class="badge bg-secondary ms-1">{{ $pengajuan->count() }}</span>
        </h6>
    </div>
    @if($pengajuan->isEmpty())
    <div class="card-body text-muted small">Tidak ada pengajuan yang cocok.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>No. Pengajuan</th><th>Cabang</th><th>Jenis</th><th>Nasabah</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($pengajuan as $p)
                @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
                <tr>
                    <td class="font-monospace fw-semibold">{{ $p->no_pengajuan }}</td>
                    <td>{{ $p->cabang?->kode_cabang }}</td>
                    <td><span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }}">{{ $p->jenis_jaminan }}</span></td>
                    <td>{{ $detail?->nama_nasabah ?? '-' }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td>
                        <a href="{{ route('pengajuan.show', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Reimburse --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2"></i>Reimburse
            <span class="badge bg-secondary ms-1">{{ $reimburse->count() }}</span>
        </h6>
    </div>
    @if($reimburse->isEmpty())
    <div class="card-body text-muted small">Tidak ada reimburse yang cocok.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>No. Reimburse</th><th>Cabang</th><th>Pemohon</th><th>Kategori</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($reimburse as $r)
                <tr>
                    <td class="font-monospace fw-semibold">{{ $r->no_reimburse }}</td>
                    <td>{{ $r->cabang?->kode_cabang }}</td>
                    <td>{{ $r->nama_pemohon }}</td>
                    <td><span class="badge bg-secondary">{{ $r->kategori }}</span></td>
                    <td><span class="badge badge-{{ $r->status }}">{{ $r->status }}</span></td>
                    <td>
                        <a href="{{ route('reimburse.show', $r) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endif
@endsection
