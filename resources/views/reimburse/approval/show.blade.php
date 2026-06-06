@extends('layouts.app')
@section('title','Proses Reimburse')
@section('page-title','Proses Approval Reimburse')

@section('content')
@php
    $badge = ['MENUNGGU'=>'warning','DISETUJUI'=>'success','DITOLAK'=>'danger'];
    $bisaProses = $reimburse->status === 'MENUNGGU';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('reimburse.approval.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <span class="badge bg-{{ $badge[$reimburse->status] ?? 'secondary' }} fs-6 px-3">{{ $reimburse->status }}</span>
</div>

{{-- Batch Item List (when batch has multiple items) --}}
@if($batchItems->count() > 1)
<div class="card border-0 shadow-sm mb-3 border-start border-primary border-3">
    <div class="card-header bg-white py-2 d-flex align-items-center gap-2">
        <i class="bi bi-layers text-primary"></i>
        <h6 class="mb-0 fw-semibold small">Batch Pengajuan — {{ $batchItems->count() }} item</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>No. Reimburse</th>
                    <th>Tgl Pengeluaran</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end">Nominal</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batchItems as $i => $item)
                <tr class="{{ $item->id === $reimburse->id ? 'table-primary' : ($item->status === 'MENUNGGU' ? 'table-warning' : '') }}">
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="font-monospace fw-semibold">
                        {{ $item->no_reimburse }}
                        @if($item->id === $reimburse->id)
                            <span class="badge bg-primary ms-1" style="font-size:0.65rem">Ini</span>
                        @endif
                    </td>
                    <td>{{ $item->tanggal_pengeluaran?->format('d/m/Y') }}</td>
                    <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                    <td class="text-muted">{{ Str::limit($item->keterangan, 30) }}</td>
                    <td class="text-end">Rp {{ number_format($item->nominal_diajukan, 0, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $badge[$item->status] ?? 'secondary' }}">{{ $item->status }}</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            @if($item->id !== $reimburse->id)
                            <a href="{{ route('reimburse.approval.show', $item) }}" class="btn btn-xs btn-outline-primary" style="padding:2px 6px;font-size:0.72rem">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                            @if($item->status === 'MENUNGGU')
                            <button type="button" class="btn btn-xs btn-success" style="padding:2px 6px;font-size:0.72rem"
                                data-bs-toggle="modal" data-bs-target="#batchSetujui{{ $item->id }}">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger" style="padding:2px 6px;font-size:0.72rem"
                                data-bs-toggle="modal" data-bs-target="#batchTolak{{ $item->id }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="5" class="text-end fw-semibold small">Total Diajukan</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($batchItems->sum('nominal_diajukan'), 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Batch item modals --}}
@foreach($batchItems as $item)
@if($item->status === 'MENUNGGU')
<div class="modal fade" id="batchSetujui{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-success"><i class="bi bi-check-circle me-2"></i>Setujui Reimburse</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('reimburse.approval.approve', $item) }}">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        <strong>{{ $item->no_reimburse }}</strong><br>
                        {{ $item->keterangan }}<br>
                        <span class="text-primary fw-semibold">Rp {{ number_format($item->nominal_diajukan, 0, ',', '.') }}</span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Disetujui (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nominal_disetujui" class="form-control"
                                value="{{ $item->nominal_diajukan }}" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan (opsional)</label>
                        <textarea name="catatan_pusat" class="form-control" rows="2" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin menyetujui?')">
                        <i class="bi bi-check-circle me-1"></i>SETUJUI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="batchTolak{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Reimburse</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('reimburse.approval.reject', $item) }}">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        <strong>{{ $item->no_reimburse }}</strong><br>
                        {{ $item->keterangan }}<br>
                        <span class="text-primary fw-semibold">Rp {{ number_format($item->nominal_diajukan, 0, ',', '.') }}</span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_pusat" class="form-control" rows="3"
                            placeholder="Tulis alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin menolak?')">
                        <i class="bi bi-x-circle me-1"></i>TOLAK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Info Pengajuan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold font-monospace">{{ $reimburse->no_reimburse }}</h6>
            </div>
            <div class="card-body small">
                <div class="row g-3">
                    <div class="col-md-6"><span class="text-muted">Nama Pemohon</span><br><strong>{{ $reimburse->nama_pemohon }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Jabatan</span><br><strong>{{ $reimburse->jabatan ?: '-' }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Asal Cabang</span><br><strong>{{ $reimburse->cabang?->nama_cabang }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Tanggal Pengeluaran</span><br><strong>{{ $reimburse->tanggal_pengeluaran?->format('d M Y') }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Kategori</span><br>
                        <span class="badge bg-secondary">{{ $kategori[$reimburse->kategori] ?? $reimburse->kategori }}</span>
                    </div>
                    <div class="col-md-6"><span class="text-muted">Dibuat Oleh</span><br><strong>{{ $reimburse->pembuatnya?->nama_lengkap }}</strong></div>
                    <div class="col-12"><span class="text-muted">Keterangan</span><br><strong>{{ $reimburse->keterangan }}</strong></div>
                </div>
            </div>
        </div>

        {{-- Nominal --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Nominal Pengajuan</h6>
            </div>
            <div class="card-body text-center">
                <div class="text-muted small mb-1">Nominal yang Diajukan</div>
                <div class="display-6 fw-bold text-primary">
                    Rp {{ number_format($reimburse->nominal_diajukan, 0, ',', '.') }}
                </div>
                @if($reimburse->nominal_disetujui)
                <div class="mt-2 text-muted small">Nominal disetujui:</div>
                <div class="fs-4 fw-bold text-success">Rp {{ number_format($reimburse->nominal_disetujui, 0, ',', '.') }}</div>
                @endif
            </div>
        </div>

        {{-- Lampiran --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-images me-2"></i>Lampiran Bukti ({{ $reimburse->lampiran->count() }} file)</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($reimburse->lampiran as $lamp)
                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center h-100">
                            @if(str_starts_with($lamp->mime_type, 'image/'))
                                <a href="{{ route('reimburse.approval.lampiran.download', $lamp) }}" target="_blank">
                                    <img src="{{ route('reimburse.approval.lampiran.download', $lamp) }}"
                                         class="img-fluid rounded mb-2"
                                         style="max-height:140px;object-fit:cover;width:100%;"
                                         alt="{{ $lamp->nama_file_asli }}">
                                </a>
                            @else
                                <a href="{{ route('reimburse.approval.lampiran.download', $lamp) }}" target="_blank"
                                   class="d-block py-4 text-danger text-decoration-none">
                                    <i class="bi bi-file-earmark-pdf fs-1 d-block"></i>
                                    <span class="small">Buka PDF</span>
                                </a>
                            @endif
                            <span class="badge bg-secondary d-block mb-1">{{ $lamp->jenis_dokumen }}</span>
                            <div class="text-muted" style="font-size:0.72rem;">{{ $lamp->nama_file_asli }}</div>
                            <div class="text-muted" style="font-size:0.72rem;">{{ $lamp->ukuran_format }}</div>
                            <a href="{{ route('reimburse.approval.lampiran.download', $lamp) }}"
                               class="btn btn-outline-primary btn-sm mt-2 w-100" target="_blank">
                                <i class="bi bi-download me-1"></i>Unduh
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-12"><p class="text-muted small mb-0">Tidak ada lampiran.</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Form Approval --}}
        @if($bisaProses)
        <div class="row g-3">
            {{-- SETUJUI --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm border-start border-success border-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-semibold text-success"><i class="bi bi-check-circle me-2"></i>Setujui Reimburse</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reimburse.approval.approve', $reimburse) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nominal Disetujui (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal_disetujui" class="form-control @error('nominal_disetujui') is-invalid @enderror"
                                        value="{{ old('nominal_disetujui', $reimburse->nominal_diajukan) }}" min="1" required>
                                    @error('nominal_disetujui')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Default: nominal yang diajukan. Ubah jika perlu disesuaikan.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Catatan (opsional)</label>
                                <textarea name="catatan_pusat" class="form-control" rows="2"
                                    placeholder="Catatan tambahan untuk pemohon...">{{ old('catatan_pusat') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Yakin menyetujui reimburse ini?')">
                                <i class="bi bi-check-circle me-1"></i>SETUJUI
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TOLAK --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-semibold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Reimburse</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reimburse.approval.reject', $reimburse) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="catatan_pusat" class="form-control @error('catatan_pusat') is-invalid @enderror"
                                    rows="4" placeholder="Tulis alasan penolakan secara jelas..." required>{{ old('catatan_pusat') }}</textarea>
                                @error('catatan_pusat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Yakin menolak reimburse ini?')">
                                <i class="bi bi-x-circle me-1"></i>TOLAK
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-secondary">
            <i class="bi bi-lock me-1"></i>
            Pengajuan sudah berstatus final: <strong>{{ $reimburse->status }}</strong>
            @if($reimburse->catatan_pusat)
            <br><em class="small">Catatan: {{ $reimburse->catatan_pusat }}</em>
            @endif
        </div>
        @endif
    </div>

    {{-- Timeline --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Riwayat</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex gap-3 align-items-start mb-3 pb-3 border-bottom">
                    <div class="text-success mt-1"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="small">
                        <div class="fw-semibold">Pengajuan Dibuat</div>
                        <div class="text-muted">{{ $reimburse->created_at?->format('d M Y H:i') }}</div>
                        <div class="text-muted">{{ $reimburse->pembuatnya?->nama_lengkap }}</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div class="mt-1 text-{{ $badge[$reimburse->status] ?? 'secondary' }}">
                        @if($reimburse->status === 'MENUNGGU')
                            <i class="bi bi-hourglass-split"></i>
                        @elseif($reimburse->status === 'DISETUJUI')
                            <i class="bi bi-check-circle-fill"></i>
                        @else
                            <i class="bi bi-x-circle-fill"></i>
                        @endif
                    </div>
                    <div class="small">
                        @if($reimburse->status === 'MENUNGGU')
                            <div class="fw-semibold text-warning">Menunggu Approval</div>
                            <div class="text-muted">Belum diproses</div>
                        @else
                            <div class="fw-semibold text-{{ $badge[$reimburse->status] }}">{{ $reimburse->status }}</div>
                            <div class="text-muted">{{ $reimburse->tgl_diproses?->format('d M Y H:i') }}</div>
                            <div class="text-muted">{{ $reimburse->pemrosesnya?->nama_lengkap }}</div>
                            @if($reimburse->status === 'DISETUJUI')
                            <div class="fw-semibold text-success">Rp {{ number_format($reimburse->nominal_disetujui, 0, ',', '.') }}</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
