@extends('layouts.app')
@section('title','Detail Pengajuan')
@section('page-title','Detail Pengajuan')

@section('content')
@php
    $detail = $pengajuan->jenis_jaminan === 'BPKB' ? $pengajuan->detailBpkb : $pengajuan->detailSertifikat;
    $statusColor = ['MENUNGGU'=>'warning','DIPROSES'=>'primary','DISETUJUI'=>'success','DITOLAK'=>'danger'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'SUPER_ADMIN')
        <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil-square me-1"></i>Edit Data
        </a>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusPengajuan">
            <i class="bi bi-trash me-1"></i>Hapus
        </button>
        @endif
        <a href="{{ route('pengajuan.cetak', $pengajuan) }}" class="btn btn-sm btn-outline-dark" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak Surat
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Info Pengajuan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold font-monospace">{{ $pengajuan->no_pengajuan }}</h6>
                <span class="badge bg-{{ $statusColor[$pengajuan->status] ?? 'secondary' }} fs-6">
                    {{ $pengajuan->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-2 small">
                    <div class="col-md-6"><span class="text-muted">Jenis Jaminan</span><br>
                        <strong>{{ $pengajuan->jenis_jaminan }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Asal Cabang</span><br>
                        <strong>{{ $pengajuan->cabang?->nama_cabang }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Dibuat Oleh</span><br>
                        <strong>{{ $pengajuan->pembuatnya?->nama_lengkap }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Tanggal Pengajuan</span><br>
                        <strong>{{ $pengajuan->tgl_dibuat?->format('d M Y H:i') }}</strong></div>
                    @if($pengajuan->diproses_oleh)
                    <div class="col-md-6"><span class="text-muted">Diproses Oleh</span><br>
                        <strong>{{ $pengajuan->pemrosesnya?->nama_lengkap }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Tanggal Diproses</span><br>
                        <strong>{{ $pengajuan->tgl_diproses?->format('d M Y H:i') }}</strong></div>
                    @endif
                </div>
                @if($pengajuan->catatan_pusat)
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-chat-left-text me-1"></i><strong>Catatan Admin Pusat:</strong><br>
                    {{ $pengajuan->catatan_pusat }}
                </div>
                @endif
            </div>
        </div>

        {{-- Data Nasabah & Jaminan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Data Nasabah & Jaminan</h6>
            </div>
            <div class="card-body small">
                <div class="row g-2">
                    <div class="col-md-6"><span class="text-muted">Nama Nasabah</span><br><strong>{{ $detail?->nama_nasabah }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. KTP</span><br><strong>{{ $detail?->no_ktp }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. Kartu Piutang</span><br><strong>{{ $detail?->no_kartu_piutang }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Total Pinjaman</span><br>
                        <strong>Rp {{ number_format($detail?->total_pinjaman ?? 0, 0, ',', '.') }}</strong></div>

                    @if($pengajuan->jenis_jaminan === 'BPKB')
                    <div class="col-md-4"><span class="text-muted">No. Polisi</span><br><strong>{{ $detail?->no_polisi }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">Merek Motor</span><br><strong>{{ $detail?->merek_motor }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">Tipe Motor</span><br><strong>{{ $detail?->tipe_motor }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">No. BPKB</span><br><strong>{{ $detail?->no_bpkb }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">No. Mesin</span><br><strong>{{ $detail?->no_mesin }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">No. Rangka</span><br><strong>{{ $detail?->no_rangka }}</strong></div>
                    @else
                    <div class="col-md-6"><span class="text-muted">No. Sertifikat</span><br><strong>{{ $detail?->no_sertifikat }}</strong></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lampiran --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Lampiran Dokumen ({{ $pengajuan->lampiran->count() }})</h6>
                @if($pengajuan->lampiran->count() > 1)
                <a href="{{ route('pengajuan.zip', $pengajuan) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-zip me-1"></i>Download Semua
                </a>
                @endif
            </div>
            <div class="card-body">
                @forelse($pengajuan->lampiran as $lamp)
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if(str_starts_with($lamp->mime_type, 'image/'))
                            <a href="{{ route('lampiran.preview', $lamp) }}" target="_blank">
                                <img src="{{ route('lampiran.preview', $lamp) }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                            </a>
                        @else
                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                        @endif
                        <div>
                            <div class="small fw-semibold">{{ $lamp->nama_file_asli }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $lamp->jenis_dokumen }} · {{ $lamp->ukuran_format }}</div>
                        </div>
                    </div>
                    <a href="{{ route('lampiran.download', $lamp) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                @empty
                <p class="text-muted small mb-0">Tidak ada lampiran.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Audit Trail + Komentar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Riwayat Status</h6>
            </div>
            <div class="card-body p-0">
                @forelse($pengajuan->auditLog->sortByDesc('created_at') as $log)
                <div class="border-bottom px-3 py-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">{{ $log->aksi }}</span>
                        <span class="text-muted">{{ $log->created_at?->format('d/m H:i') }}</span>
                    </div>
                    @if($log->status_baru)
                    <div>
                        @if($log->status_lama)<span class="badge badge-{{ $log->status_lama }} me-1">{{ $log->status_lama }}</span><i class="bi bi-arrow-right small"></i>@endif
                        <span class="badge badge-{{ $log->status_baru }} ms-1">{{ $log->status_baru }}</span>
                    </div>
                    @endif
                    <div class="text-muted">{{ $log->user?->nama_lengkap }}</div>
                    @if($log->keterangan)<div class="fst-italic">{{ $log->keterangan }}</div>@endif
                </div>
                @empty
                <p class="text-muted small p-3 mb-0">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>

        {{-- Komentar / Komunikasi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-chat-dots me-2 text-info"></i>Komunikasi ({{ $pengajuan->komentar->count() }})</h6>
            </div>
            <div class="card-body p-0" style="max-height:320px;overflow-y:auto;" id="komentarList">
                @forelse($pengajuan->komentar()->with('user')->latest()->get() as $kom)
                <div class="border-bottom px-3 py-2 small {{ $kom->user_id === auth()->user()->id ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-semibold">{{ $kom->user?->nama_lengkap }}</span>
                            <span class="badge bg-{{ $kom->user?->role === 'ADMIN_PUSAT' ? 'primary' : ($kom->user?->role === 'SUPER_ADMIN' ? 'dark' : 'secondary') }} ms-1" style="font-size:0.65rem;">{{ $kom->user?->role }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted" style="font-size:0.7rem;">{{ $kom->created_at?->format('d/m H:i') }}</span>
                            @if($kom->user_id === auth()->user()->id || auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('komentar.destroy', $kom) }}" onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:0.7rem;" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="mt-1">{{ $kom->komentar }}</div>
                </div>
                @empty
                <p class="text-muted small p-3 mb-0">Belum ada komentar.</p>
                @endforelse
            </div>
            <div class="card-footer bg-white p-2">
                <form method="POST" action="{{ route('komentar.store', $pengajuan) }}">
                    @csrf
                    <div class="d-flex gap-2">
                        <textarea name="komentar" class="form-control form-control-sm" rows="2"
                            placeholder="Tulis pesan atau pertanyaan..." required style="resize:none;"></textarea>
                        <button type="submit" class="btn btn-info btn-sm text-white align-self-end">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    @error('komentar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </form>
            </div>
        </div>
    </div>
</div>
@if(auth()->user()->role === 'SUPER_ADMIN')
<div class="modal fade" id="modalHapusPengajuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Pengajuan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger small mb-3">
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data dan lampiran pengajuan ini akan dihapus permanen.
                </div>
                <p class="mb-1 small">Nomor Pengajuan: <strong>{{ $pengajuan->no_pengajuan }}</strong></p>
                <p class="mb-0 small">Nasabah: <strong>{{ $pengajuan->jenis_jaminan === 'BPKB' ? $pengajuan->detailBpkb?->nama_nasabah : $pengajuan->detailSertifikat?->nama_nasabah }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('pengajuan.destroy', $pengajuan) }}">
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
