@extends('layouts.app')
@section('title','Proses Pengajuan')
@section('page-title','Proses Pengajuan')

@section('content')
@php
    $detail = $pengajuan->jenis_jaminan === 'BPKB' ? $pengajuan->detailBpkb : $pengajuan->detailSertifikat;
    $statusColor = ['MENUNGGU'=>'warning','DIPROSES'=>'primary','DISETUJUI'=>'success','DITOLAK'=>'danger'];
    $bisa_proses = !in_array($pengajuan->status, ['DISETUJUI','DITOLAK']);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('adminpusat.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2 align-items-center">
        @if(auth()->user()->role === 'SUPER_ADMIN')
        <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil-square me-1"></i>Edit Data
        </a>
        @endif
        <span class="badge bg-{{ $statusColor[$pengajuan->status] ?? 'secondary' }} fs-6 px-3">{{ $pengajuan->status }}</span>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Info --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold font-monospace">{{ $pengajuan->no_pengajuan }}</h6>
            </div>
            <div class="card-body small">
                <div class="row g-2">
                    <div class="col-md-6"><span class="text-muted">Jenis Jaminan</span><br><strong>{{ $pengajuan->jenis_jaminan }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Asal Cabang</span><br><strong>{{ $pengajuan->cabang?->nama_cabang }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Dibuat Oleh</span><br><strong>{{ $pengajuan->pembuatnya?->nama_lengkap }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">Tanggal Pengajuan</span><br><strong>{{ $pengajuan->tgl_dibuat?->format('d M Y H:i') }}</strong></div>
                </div>
                @if($pengajuan->catatan_pusat)
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-chat-left-text me-1"></i><strong>Catatan sebelumnya:</strong> {{ $pengajuan->catatan_pusat }}
                </div>
                @endif
            </div>
        </div>

        {{-- Data Nasabah --}}
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
                    <div class="col-md-4"><span class="text-muted">Merek/Tipe</span><br><strong>{{ $detail?->merek_motor }} {{ $detail?->tipe_motor }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">No. BPKB</span><br><strong>{{ $detail?->no_bpkb }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. Mesin</span><br><strong>{{ $detail?->no_mesin }}</strong></div>
                    <div class="col-md-6"><span class="text-muted">No. Rangka</span><br><strong>{{ $detail?->no_rangka }}</strong></div>
                    @else
                    <div class="col-md-6"><span class="text-muted">No. Sertifikat</span><br><strong>{{ $detail?->no_sertifikat }}</strong></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lampiran --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Lampiran Dokumen</h6>
            </div>
            <div class="card-body">
                @forelse($pengajuan->lampiran as $lamp)
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-{{ str_contains($lamp->mime_type,'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-image text-primary' }} fs-4"></i>
                        <div>
                            <div class="small fw-semibold">{{ $lamp->nama_file_asli }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $lamp->jenis_dokumen }} · {{ $lamp->ukuran_format }}</div>
                        </div>
                    </div>
                    <a href="{{ route('adminpusat.lampiran.download', $lamp) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                @empty
                <p class="text-muted small mb-0">Tidak ada lampiran.</p>
                @endforelse
            </div>
        </div>

        {{-- Form Update Status --}}
        @if($bisa_proses)
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-pencil-square me-2"></i>Update Status Pengajuan</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('adminpusat.update-status', $pengajuan) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status Baru <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            @foreach(['DIPROSES'=>'primary','DISETUJUI'=>'success','DITOLAK'=>'danger'] as $s=>$c)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_{{ $s }}" value="{{ $s }}" required>
                                <label class="form-check-label" for="status_{{ $s }}">
                                    <span class="badge bg-{{ $c }}">{{ $s }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Catatan / Keterangan
                            <span id="catatanWajib" class="text-danger d-none">*</span>
                        </label>
                        <textarea name="catatan" id="catatanInput" class="form-control @error('catatan') is-invalid @enderror" rows="3"
                            placeholder="Tambahkan catatan atau alasan penolakan...">{{ old('catatan') }}</textarea>
                        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="catatanHint" class="form-text text-danger d-none">Wajib diisi saat menolak pengajuan.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Simpan Perubahan Status
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-secondary">
            <i class="bi bi-lock me-1"></i>Pengajuan ini sudah berstatus final (<strong>{{ $pengajuan->status }}</strong>) dan tidak dapat diubah.
        </div>
        @endif
    </div>

    {{-- Audit Trail + WA + Komentar --}}
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
                    <div class="my-1">
                        @if($log->status_lama)<span class="badge badge-{{ $log->status_lama }}">{{ $log->status_lama }}</span> → @endif
                        <span class="badge badge-{{ $log->status_baru }}">{{ $log->status_baru }}</span>
                    </div>
                    @endif
                    <div class="text-muted">{{ $log->user?->nama_lengkap }}</div>
                    @if($log->keterangan)<div class="fst-italic text-muted">{{ $log->keterangan }}</div>@endif
                </div>
                @empty
                <p class="text-muted small p-3 mb-0">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>

        {{-- Notifikasi WhatsApp --}}
        @php $pembuat = $pengajuan->pembuatnya; @endphp
        @if($pembuat?->no_whatsapp)
        <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
            <div class="card-body py-2">
                <div class="small fw-semibold mb-1"><i class="bi bi-whatsapp text-success me-1"></i>Kirim Notif WhatsApp</div>
                @php
                    $waMsg = urlencode("Halo {$pembuat->nama_lengkap},\nPengajuan Anda *{$pengajuan->no_pengajuan}* telah berstatus *{$pengajuan->status}*." . ($pengajuan->catatan_pusat ? "\n\nCatatan: {$pengajuan->catatan_pusat}" : '') . "\n\nTerima kasih.\n— Admin Pusat Group Mega");
                    $waPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pembuat->no_whatsapp));
                    $waUrl = "https://wa.me/{$waPhone}?text={$waMsg}";
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-whatsapp me-1"></i>Kirim via WA ke {{ $pembuat->nama_lengkap }}
                </a>
            </div>
        </div>
        @endif

        {{-- Komentar / Komunikasi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-chat-dots me-2 text-info"></i>Komunikasi ({{ $pengajuan->komentar->count() }})</h6>
            </div>
            <div class="card-body p-0" style="max-height:280px;overflow-y:auto;">
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
                            <form method="POST" action="{{ route('komentar.destroy', $kom) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:0.7rem;"><i class="bi bi-trash"></i></button>
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
                            placeholder="Tulis pesan atau instruksi..." required style="resize:none;"></textarea>
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
@endsection

@push('scripts')
<script>
// Catatan wajib saat DITOLAK
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const ditolak = document.querySelector('input[name="status"]:checked')?.value === 'DITOLAK';
        document.getElementById('catatanWajib').classList.toggle('d-none', !ditolak);
        document.getElementById('catatanHint').classList.toggle('d-none', !ditolak);
        document.getElementById('catatanInput').required = ditolak;
    });
});
</script>
@endpush
