@extends('layouts.app')
@section('title','Audit Log Sistem')
@section('page-title','Audit Log Sistem')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2 text-primary"></i>Seluruh Aktivitas Sistem</h6>
        <span class="badge bg-secondary">{{ $logs->total() }} entri</span>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Pengguna</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Aksi</label>
                <select name="aksi" class="form-select form-select-sm">
                    <option value="">Semua Aksi</option>
                    @foreach(['BUAT_PENGAJUAN','UBAH_STATUS','EDIT_DATA','LOGIN','LOGOUT'] as $a)
                        <option value="{{ $a }}" @selected(request('aksi')===$a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status Baru</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['MENUNGGU','DIPROSES','DISETUJUI','DITOLAK'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Sampai Tanggal</label>
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
                    <th style="width:160px">Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>No. Pengajuan</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-muted font-monospace" style="font-size:0.75rem;">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $log->user?->nama_lengkap ?? '—' }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $log->user?->role }}</div>
                    </td>
                    <td>
                        @php
                            $aksiColor = match($log->aksi) {
                                'BUAT_PENGAJUAN' => 'primary',
                                'UBAH_STATUS'    => 'warning',
                                'EDIT_DATA'      => 'info',
                                'LOGIN'          => 'success',
                                'LOGOUT'         => 'secondary',
                                default          => 'dark',
                            };
                        @endphp
                        <span class="badge bg-{{ $aksiColor }}">{{ $log->aksi }}</span>
                    </td>
                    <td>
                        @if($log->pengajuan)
                            <a href="{{ route('adminpusat.show', $log->pengajuan) }}" class="font-monospace text-decoration-none">
                                {{ $log->pengajuan->no_pengajuan }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($log->status_lama || $log->status_baru)
                            @if($log->status_lama)
                                <span class="badge badge-{{ $log->status_lama }}">{{ $log->status_lama }}</span>
                                <i class="bi bi-arrow-right small mx-1"></i>
                            @endif
                            @if($log->status_baru)
                                <span class="badge badge-{{ $log->status_baru }}">{{ $log->status_baru }}</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->keterangan }}">
                        {{ $log->keterangan ?: '—' }}
                    </td>
                    <td class="text-muted font-monospace" style="font-size:0.72rem;">{{ $log->ip_address ?: '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data audit log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
</div>
@endsection
