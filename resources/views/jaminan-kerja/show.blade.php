@extends('layouts.app')
@section('title','Detail Jaminan Kerja')
@section('page-title','Detail Jaminan Kerja')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('jaminan-kerja.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if($jaminanKerja->status === 'AKTIF')
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalKembalikan">
            <i class="bi bi-box-arrow-right me-1"></i>Kembalikan Jaminan
        </button>
        @endif
        @if(auth()->user()->role === 'SUPER_ADMIN')
        <a href="{{ route('jaminan-kerja.edit', $jaminanKerja) }}" class="btn btn-sm btn-outline-warning">
            <i class="bi bi-pencil-square me-1"></i>Edit Data
        </a>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusJaminan">
            <i class="bi bi-trash me-1"></i>Hapus
        </button>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">

        {{-- Info Utama --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-semibold font-monospace">{{ $jaminanKerja->no_jaminan }}</h6>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    @if($jaminanKerja->status === 'AKTIF')
                        <span class="badge bg-success fs-6">Aktif — Tersimpan</span>
                    @else
                        <span class="badge bg-warning text-dark fs-6">Sudah Dikembalikan</span>
                    @endif
                    @php
                        $badgePusat = ['MENUNGGU'=>'secondary','DITERIMA'=>'primary','DITOLAK'=>'danger'];
                        $labelPusat = ['MENUNGGU'=>'Menunggu Konfirmasi Pusat','DITERIMA'=>'Diterima di Kantor Pusat','DITOLAK'=>'Ditolak Kantor Pusat'];
                    @endphp
                    <span class="badge bg-{{ $badgePusat[$jaminanKerja->status_pusat] ?? 'secondary' }}">
                        <i class="bi bi-building me-1"></i>{{ $labelPusat[$jaminanKerja->status_pusat] ?? $jaminanKerja->status_pusat }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2 small">
                    <div class="col-md-6"><span class="text-muted">Nama Karyawan</span><br><strong>{{ $jaminanKerja->nama_karyawan }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. KTP</span><br><strong>{{ $jaminanKerja->no_ktp }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Jabatan</span><br><strong>{{ $jaminanKerja->jabatan }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. HP</span><br><strong>{{ $jaminanKerja->no_hp ?? '-' }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Tanggal Masuk Kerja</span><br><strong>{{ $jaminanKerja->tgl_masuk_kerja?->format('d M Y') }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Asal Cabang</span><br><strong>{{ $jaminanKerja->cabang?->nama_cabang }}</strong></div>
                </div>
                @if($jaminanKerja->catatan)
                <div class="alert alert-light border mt-3 mb-0 small">
                    <i class="bi bi-chat-left-text me-1"></i><strong>Catatan:</strong> {{ $jaminanKerja->catatan }}
                </div>
                @endif
            </div>
        </div>

        {{-- Jaminan yang Diserahkan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2"></i>Jaminan yang Diserahkan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 flex-wrap">
                    @if($jaminanKerja->has_akte)
                    <div class="text-center">
                        <div class="badge bg-info text-dark fs-6 px-3 py-2 mb-1"><i class="bi bi-file-earmark-person me-1"></i>Akte Kelahiran</div>
                        <div class="small text-muted">
                            @foreach($jaminanKerja->lampiranByJenis('AKTE_KELAHIRAN') as $l)
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $l) }}" target="_blank" class="d-block">
                                @if(str_starts_with($l->mime_type, 'image/'))
                                    <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" class="img-thumbnail mt-1" style="max-height:80px;">
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>{{ $l->nama_file_asli }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($jaminanKerja->has_bpkb)
                    <div class="text-center">
                        <div class="badge bg-primary fs-6 px-3 py-2 mb-1"><i class="bi bi-car-front me-1"></i>BPKB</div>
                        <div class="small text-muted">
                            @foreach($jaminanKerja->lampiranByJenis('BPKB') as $l)
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $l) }}" target="_blank" class="d-block">
                                @if(str_starts_with($l->mime_type, 'image/'))
                                    <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" class="img-thumbnail mt-1" style="max-height:80px;">
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>{{ $l->nama_file_asli }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($jaminanKerja->has_ijasah)
                    <div class="text-center">
                        <div class="badge bg-success fs-6 px-3 py-2 mb-1"><i class="bi bi-mortarboard me-1"></i>Ijasah</div>
                        <div class="small text-muted">
                            @foreach($jaminanKerja->lampiranByJenis('IJASAH') as $l)
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $l) }}" target="_blank" class="d-block">
                                @if(str_starts_with($l->mime_type, 'image/'))
                                    <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" class="img-thumbnail mt-1" style="max-height:80px;">
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>{{ $l->nama_file_asli }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Foto Penerimaan --}}
        @php $fotoTerima = $jaminanKerja->lampiranByJenis('FOTO_PENERIMAAN'); @endphp
        @if($fotoTerima->count())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2 text-success"></i>Foto Bukti Penerimaan Jaminan</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($fotoTerima as $foto)
                    <div class="col-md-4">
                        <a href="{{ route('jaminan-kerja.lampiran.preview', $foto) }}" target="_blank">
                            <img src="{{ route('jaminan-kerja.lampiran.preview', $foto) }}" class="img-fluid rounded border" style="max-height:200px;width:100%;object-fit:cover;">
                        </a>
                        <div class="small text-muted mt-1 text-center">
                            Diterima oleh {{ $jaminanKerja->penerimanya?->nama_lengkap }}<br>
                            {{ $jaminanKerja->tgl_diterima?->format('d M Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Foto Pengembalian --}}
        @php $fotoKembali = $jaminanKerja->lampiranByJenis('FOTO_PENGEMBALIAN'); @endphp
        @if($jaminanKerja->status === 'KEMBALI' && $fotoKembali->count())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-warning bg-opacity-25 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2 text-warning"></i>Foto Bukti Pengembalian Jaminan</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($fotoKembali as $foto)
                    <div class="col-md-4">
                        <a href="{{ route('jaminan-kerja.lampiran.preview', $foto) }}" target="_blank">
                            <img src="{{ route('jaminan-kerja.lampiran.preview', $foto) }}" class="img-fluid rounded border" style="max-height:200px;width:100%;object-fit:cover;">
                        </a>
                        <div class="small text-muted mt-1 text-center">
                            Dikembalikan oleh {{ $jaminanKerja->pengembaliannya?->nama_lengkap }}<br>
                            {{ $jaminanKerja->tgl_dikembalikan?->format('d M Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($jaminanKerja->catatan_pengembalian)
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-chat-left-text me-1"></i><strong>Catatan Pengembalian:</strong> {{ $jaminanKerja->catatan_pengembalian }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Semua Lampiran --}}
        @php
            $semualampiran = $jaminanKerja->lampiran->whereNotIn('jenis_dokumen', ['FOTO_PENERIMAAN','FOTO_PENGEMBALIAN','AKTE_KELAHIRAN','BPKB','IJASAH']);
        @endphp
        @if($semualampiran->count())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2"></i>Dokumen Tambahan</h6>
            </div>
            <div class="card-body">
                @foreach($semualampiran as $lamp)
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if(str_starts_with($lamp->mime_type, 'image/'))
                            <a href="{{ route('jaminan-kerja.lampiran.preview', $lamp) }}" target="_blank">
                                <img src="{{ route('jaminan-kerja.lampiran.preview', $lamp) }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                            </a>
                        @else
                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                        @endif
                        <div>
                            <div class="small fw-semibold">{{ $lamp->nama_file_asli }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $lamp->ukuran_format }}</div>
                        </div>
                    </div>
                    <a href="{{ route('jaminan-kerja.lampiran.download', $lamp) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informasi Pencatatan</h6>
            </div>
            <div class="card-body small">
                <div class="mb-2">
                    <span class="text-muted">Dicatat oleh</span><br>
                    <strong>{{ $jaminanKerja->pembuatnya?->nama_lengkap }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Tanggal Dicatat</span><br>
                    <strong>{{ $jaminanKerja->created_at?->format('d M Y H:i') }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Jaminan Diterima Oleh</span><br>
                    <strong>{{ $jaminanKerja->penerimanya?->nama_lengkap ?? '-' }}</strong>
                </div>
                @if($jaminanKerja->tgl_diterima)
                <div class="mb-2">
                    <span class="text-muted">Tanggal Penerimaan</span><br>
                    <strong>{{ $jaminanKerja->tgl_diterima?->format('d M Y H:i') }}</strong>
                </div>
                @endif
                @if($jaminanKerja->status === 'KEMBALI')
                <hr>
                <div class="mb-2">
                    <span class="text-muted">Dikembalikan Oleh</span><br>
                    <strong>{{ $jaminanKerja->pengembaliannya?->nama_lengkap ?? '-' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Tanggal Pengembalian</span><br>
                    <strong>{{ $jaminanKerja->tgl_dikembalikan?->format('d M Y H:i') }}</strong>
                </div>
                @endif

                {{-- Status Konfirmasi Pusat --}}
                <hr>
                <div class="mb-2">
                    <span class="text-muted">Status Kantor Pusat</span><br>
                    <span class="badge bg-{{ $badgePusat[$jaminanKerja->status_pusat] ?? 'secondary' }}">
                        {{ $labelPusat[$jaminanKerja->status_pusat] ?? $jaminanKerja->status_pusat }}
                    </span>
                </div>
                @if($jaminanKerja->status_pusat !== 'MENUNGGU')
                <div class="mb-2">
                    <span class="text-muted">Dikonfirmasi Oleh</span><br>
                    <strong>{{ $jaminanKerja->pengkonfirmasinya?->nama_lengkap ?? '-' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Tanggal Konfirmasi</span><br>
                    <strong>{{ $jaminanKerja->tgl_dikonfirmasi?->format('d M Y H:i') }}</strong>
                </div>
                @if($jaminanKerja->catatan_pusat)
                <div class="mb-2">
                    <span class="text-muted">Catatan Pusat</span><br>
                    <em>{{ $jaminanKerja->catatan_pusat }}</em>
                </div>
                @endif
                @endif
            </div>
        </div>

        {{-- Approval Pusat (hanya Admin Pusat & Super Admin, status masih MENUNGGU) --}}
        @if(in_array(auth()->user()->role, ['ADMIN_PUSAT','SUPER_ADMIN']) && $jaminanKerja->status_pusat === 'MENUNGGU')
        <div class="card border-0 shadow-sm mb-3 border-start border-primary border-3">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold small"><i class="bi bi-building me-2 text-primary"></i>Konfirmasi Penerimaan Kantor Pusat</h6>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-3">Konfirmasi apakah dokumen jaminan <strong>{{ implode(', ', $jaminanKerja->jaminan_list) }}</strong> atas nama <strong>{{ $jaminanKerja->nama_karyawan }}</strong> sudah diterima di Kantor Pusat.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm flex-fill"
                        data-bs-toggle="modal" data-bs-target="#modalApprovePusat">
                        <i class="bi bi-check-circle me-1"></i>Terima
                    </button>
                    <button type="button" class="btn btn-danger btn-sm flex-fill"
                        data-bs-toggle="modal" data-bs-target="#modalRejectPusat">
                        <i class="bi bi-x-circle me-1"></i>Tolak
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Approve Pusat --}}
@if(in_array(auth()->user()->role, ['ADMIN_PUSAT','SUPER_ADMIN']) && $jaminanKerja->status_pusat === 'MENUNGGU')
<div class="modal fade" id="modalApprovePusat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('jaminan-kerja.approve-pusat', $jaminanKerja) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold text-success"><i class="bi bi-check-circle me-2"></i>Terima di Kantor Pusat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success small">
                        Konfirmasi bahwa dokumen jaminan <strong>{{ implode(', ', $jaminanKerja->jaminan_list) }}</strong>
                        atas nama <strong>{{ $jaminanKerja->nama_karyawan }}</strong> ({{ $jaminanKerja->no_jaminan }})
                        sudah diterima dan tersimpan di Kantor Pusat.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan (opsional)</label>
                        <textarea name="catatan_pusat" class="form-control" rows="2"
                            placeholder="Catatan tambahan dari Kantor Pusat...">{{ old('catatan_pusat') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirm('Konfirmasi penerimaan jaminan di Kantor Pusat?')">
                        <i class="bi bi-check-circle me-1"></i>Ya, Sudah Diterima
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reject Pusat --}}
<div class="modal fade" id="modalRejectPusat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('jaminan-kerja.reject-pusat', $jaminanKerja) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Jaminan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger small">
                        Tandai bahwa dokumen jaminan <strong>{{ implode(', ', $jaminanKerja->jaminan_list) }}</strong>
                        atas nama <strong>{{ $jaminanKerja->nama_karyawan }}</strong> <strong>tidak diterima</strong> / bermasalah di Kantor Pusat.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_pusat" class="form-control @error('catatan_pusat') is-invalid @enderror"
                            rows="3" placeholder="Jelaskan alasan penolakan..." required>{{ old('catatan_pusat') }}</textarea>
                        @error('catatan_pusat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin menolak jaminan ini?')">
                        <i class="bi bi-x-circle me-1"></i>Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Kembalikan Jaminan --}}
@if($jaminanKerja->status === 'AKTIF')
<div class="modal fade" id="modalKembalikan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('jaminan-kerja.kembalikan', $jaminanKerja) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right me-2 text-warning"></i>Pengembalian Jaminan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        Jaminan <strong>{{ implode(', ', $jaminanKerja->jaminan_list) }}</strong> akan dikembalikan kepada
                        <strong>{{ $jaminanKerja->nama_karyawan }}</strong>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Foto Bukti Pengembalian <span class="text-danger">*</span>
                        </label>
                        <div class="form-text mb-2">Foto saat menyerahkan kembali dokumen ke karyawan. Bisa lebih dari 1.</div>
                        @error('foto_pengembalian')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                        @error('foto_pengembalian.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                        <x-file-upload-dual name="foto_pengembalian[]" preview-id="preview_foto_pengembalian_modal" :foto-only="true" :required="true" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Pengembalian</label>
                        <textarea name="catatan_pengembalian" class="form-control" rows="2"
                            placeholder="Alasan karyawan keluar atau catatan lain (opsional)">{{ old('catatan_pengembalian') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i>Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->role === 'SUPER_ADMIN')
<div class="modal fade" id="modalHapusJaminan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Jaminan Kerja</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger small mb-3">
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data dan lampiran akan dihapus permanen.
                </div>
                <p class="mb-1 small">No. Jaminan: <strong>{{ $jaminanKerja->no_jaminan }}</strong></p>
                <p class="mb-0 small">Karyawan: <strong>{{ $jaminanKerja->nama_karyawan }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('jaminan-kerja.destroy', $jaminanKerja) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Buka modal otomatis jika ada error validasi
@error('catatan_pusat')
document.addEventListener('DOMContentLoaded', () => {
    const m = document.getElementById('modalRejectPusat');
    if (m) new bootstrap.Modal(m).show();
});
@enderror
// Buka modal otomatis jika ada error validasi pengembalian
@error('foto_pengembalian')
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalKembalikan'));
    modal.show();
});
@enderror
@error('foto_pengembalian.*')
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalKembalikan'));
    modal.show();
});
@enderror
</script>
@endpush
