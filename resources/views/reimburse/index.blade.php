@extends('layouts.app')
@section('title','Daftar Reimburse')
@section('page-title','Pengajuan Reimburse Saya')

@section('content')
{{-- Summary --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Menunggu',$summary['menunggu'],'warning','hourglass-split'],
        ['Disetujui',$summary['disetujui'],'success','check-circle'],
        ['Ditolak',$summary['ditolak'],'danger','x-circle'],
    ] as [$label,$val,$color,$icon])
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-bold text-{{ $color }}">{{ $val }}</div></div>
                <i class="bi bi-{{ $icon }} fs-1 text-{{ $color }} opacity-25"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">Daftar Pengajuan Reimburse</h6>
        <a href="{{ route('reimburse.create') }}" class="btn btn-sm text-white" style="background:#1a237e">
            <i class="bi bi-plus-circle me-1"></i>Buat Pengajuan
        </a>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="cari" class="form-control form-control-sm"
                    value="{{ request('cari') }}" placeholder="No. reimburse / nama pemohon">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Kategori</label>
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Reimburse::labelKategori() as $k => $l)
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
                    <th>Nama Pemohon</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end">Nominal Diajukan</th>
                    <th class="text-end">Nominal Disetujui</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reimburse as $r)
                <tr class="{{ $r->status === 'MENUNGGU' ? 'table-warning' : ($r->status === 'DISETUJUI' ? 'table-success' : '') }}">
                    <td class="font-monospace fw-semibold">{{ $r->no_reimburse }}</td>
                    <td>{{ $r->tanggal_pengeluaran?->format('d/m/Y') }}</td>
                    <td>{{ $r->nama_pemohon }}</td>
                    <td><span class="badge bg-secondary">{{ $r->kategori }}</span></td>
                    <td class="text-muted">{{ Str::limit($r->keterangan, 40) }}</td>
                    <td class="text-end">Rp {{ number_format($r->nominal_diajukan, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold {{ $r->status === 'DISETUJUI' ? 'text-success' : 'text-muted' }}">
                        {{ $r->nominal_disetujui ? 'Rp ' . number_format($r->nominal_disetujui, 0, ',', '.') : '-' }}
                    </td>
                    <td>
                        @php $badge = ['MENUNGGU'=>'warning','DISETUJUI'=>'success','DITOLAK'=>'danger']; @endphp
                        <span class="badge bg-{{ $badge[$r->status] ?? 'secondary' }}">{{ $r->status }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('reimburse.show', $r) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">
                    Belum ada pengajuan reimburse.<br>
                    <a href="{{ route('reimburse.create') }}" class="btn btn-sm mt-2" style="background:#1a237e;color:white">
                        <i class="bi bi-plus-circle me-1"></i>Buat Sekarang
                    </a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $reimburse->links() }}</div>
</div>
@endsection
