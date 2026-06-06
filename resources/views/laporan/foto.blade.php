@extends('layouts.app')
@section('title','Galeri Foto Lampiran')
@section('page-title','Galeri Foto Lampiran')

@section('content')

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('laporan.foto') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" @selected(request('cabang_id') == $c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Jenis Jaminan</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="BPKB"       @selected(request('jenis') === 'BPKB')>BPKB</option>
                    <option value="SERTIFIKAT" @selected(request('jenis') === 'SERTIFIKAT')>Sertifikat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ request('tgl_sampai') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('laporan.foto') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Info jumlah --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        <i class="bi bi-images me-1"></i>
        Menampilkan <strong>{{ $fotos->count() }}</strong> dari <strong>{{ $total }}</strong> foto
    </span>
    <span class="text-muted small">{{ $fotos->currentPage() }} / {{ $fotos->lastPage() }} halaman</span>
</div>

{{-- Grid foto --}}
@if($fotos->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-images fs-1 d-block mb-2"></i>
    Tidak ada foto ditemukan.
</div>
@else
<div class="row g-3">
    @foreach($fotos as $foto)
    @php
        $p      = $foto->pengajuan;
        $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat;
    @endphp
    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
        <div class="card border-0 shadow-sm h-100" style="border-radius:10px;overflow:hidden;">
            {{-- Thumbnail --}}
            <a href="{{ route('lampiran.preview', $foto) }}" target="_blank"
               data-bs-toggle="tooltip" title="{{ $foto->nama_file_asli }}">
                <img src="{{ route('lampiran.preview', $foto) }}"
                     class="card-img-top"
                     style="height:140px;object-fit:cover;"
                     loading="lazy"
                     onerror="this.src='';this.parentElement.innerHTML='<div class=\'text-center py-4 text-muted\'><i class=\'bi bi-image fs-2\'></i></div>'">
            </a>
            {{-- Info --}}
            <div class="card-body p-2">
                <div class="text-truncate fw-semibold" style="font-size:0.72rem;" title="{{ $detail?->nama_nasabah }}">
                    {{ $detail?->nama_nasabah ?? '-' }}
                </div>
                <div class="text-muted" style="font-size:0.68rem;">
                    <span class="badge bg-{{ $p->jenis_jaminan === 'BPKB' ? 'primary' : 'info' }} me-1" style="font-size:0.6rem;">
                        {{ $p->jenis_jaminan }}
                    </span>
                    {{ $p->cabang?->kode_cabang }}
                </div>
                <div class="text-muted text-truncate" style="font-size:0.65rem;" title="{{ $foto->jenis_dokumen }}">
                    {{ $foto->jenis_dokumen }}
                </div>
                <div class="d-flex gap-1 mt-1">
                    <a href="{{ route('lampiran.preview', $foto) }}" target="_blank"
                       class="btn btn-outline-primary btn-sm flex-fill py-0" style="font-size:0.65rem;">
                        <i class="bi bi-eye"></i> Lihat
                    </a>
                    <a href="{{ route('lampiran.download', $foto) }}"
                       class="btn btn-outline-secondary btn-sm flex-fill py-0" style="font-size:0.65rem;">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                <div class="mt-1">
                    <a href="{{ route('pengajuan.show', $p) }}" class="text-muted text-decoration-none"
                       style="font-size:0.62rem;">
                        <i class="bi bi-link-45deg"></i>{{ $p->no_pengajuan }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $fotos->withQueryString()->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});
</script>
@endpush
