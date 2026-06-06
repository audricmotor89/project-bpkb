@extends('layouts.app')
@section('title','Detail Reimburse')
@section('page-title','Detail Reimburse')

@section('content')
@php
    $badge = ['MENUNGGU'=>'warning','DISETUJUI'=>'success','DITOLAK'=>'danger'];
    $kategoriLabel = \App\Models\Reimburse::labelKategori();
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('reimburse.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-{{ $badge[$reimburse->status] ?? 'secondary' }} fs-6 px-3">{{ $reimburse->status }}</span>
        @if(auth()->user()->role === 'SUPER_ADMIN')
        <a href="{{ route('reimburse.edit', $reimburse) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil-square me-1"></i>Edit Data
        </a>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusReimburse">
            <i class="bi bi-trash me-1"></i>Hapus
        </button>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Header --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold font-monospace">{{ $reimburse->no_reimburse }}</h6>
            </div>
            <div class="card-body small">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted">Nama Pemohon</span><br>
                        <strong class="fs-6">{{ $reimburse->nama_pemohon }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Jabatan</span><br>
                        <strong>{{ $reimburse->jabatan ?: '-' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Asal Cabang</span><br>
                        <strong>{{ $reimburse->cabang?->nama_cabang }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Tanggal Pengeluaran</span><br>
                        <strong>{{ $reimburse->tanggal_pengeluaran?->format('d M Y') }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Kategori Biaya</span><br>
                        <span class="badge bg-secondary fs-6">{{ $kategoriLabel[$reimburse->kategori] ?? $reimburse->kategori }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Dibuat Oleh</span><br>
                        <strong>{{ $reimburse->pembuatnya?->nama_lengkap }}</strong>
                        <span class="text-muted ms-1">{{ $reimburse->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted">Keterangan / Rincian</span><br>
                        <strong>{{ $reimburse->keterangan }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nominal --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-currency-dollar me-2"></i>Informasi Nominal</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Nominal Diajukan</div>
                            <div class="fs-4 fw-bold text-primary">
                                Rp {{ number_format($reimburse->nominal_diajukan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center {{ $reimburse->status === 'DISETUJUI' ? 'border-success bg-success bg-opacity-10' : '' }}">
                            <div class="text-muted small mb-1">Nominal Disetujui</div>
                            <div class="fs-4 fw-bold {{ $reimburse->status === 'DISETUJUI' ? 'text-success' : 'text-muted' }}">
                                {{ $reimburse->nominal_disetujui
                                    ? 'Rp ' . number_format($reimburse->nominal_disetujui, 0, ',', '.')
                                    : '-' }}
                            </div>
                            @if($reimburse->status === 'DISETUJUI' && $reimburse->nominal_disetujui < $reimburse->nominal_diajukan)
                            <div class="text-warning small mt-1">
                                <i class="bi bi-exclamation-triangle me-1"></i>Disesuaikan dari pengajuan
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($reimburse->catatan_pusat)
                <div class="alert alert-{{ $reimburse->status === 'DITOLAK' ? 'danger' : 'info' }} small mt-3 mb-0">
                    <i class="bi bi-chat-left-text me-1"></i>
                    <strong>{{ $reimburse->status === 'DITOLAK' ? 'Alasan Penolakan' : 'Catatan Admin Pusat' }}:</strong><br>
                    {{ $reimburse->catatan_pusat }}
                </div>
                @endif

                @if($reimburse->diproses_oleh)
                <div class="text-muted small mt-2">
                    <i class="bi bi-person-check me-1"></i>
                    Diproses oleh <strong>{{ $reimburse->pemrosesnya?->nama_lengkap }}</strong>
                    pada {{ $reimburse->tgl_diproses?->format('d M Y H:i') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Lampiran --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-images me-2"></i>Lampiran Bukti ({{ $reimburse->lampiran->count() }} file)</h6>
            </div>
            <div class="card-body">
                @forelse($reimburse->lampiran as $lamp)
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <div class="d-flex align-items-center gap-3">
                        @if(str_starts_with($lamp->mime_type, 'image/'))
                            <img src="{{ route('reimburse.lampiran.download', $lamp) }}"
                                 class="rounded border"
                                 style="width:60px;height:60px;object-fit:cover;"
                                 alt="{{ $lamp->nama_file_asli }}"
                                 onerror="this.style.display='none'">
                        @else
                            <div class="text-danger fs-2"><i class="bi bi-file-earmark-pdf"></i></div>
                        @endif
                        <div>
                            <div class="small fw-semibold">{{ $lamp->nama_file_asli }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                <span class="badge bg-secondary me-1">{{ $lamp->jenis_dokumen }}</span>
                                {{ $lamp->ukuran_format }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('reimburse.lampiran.download', $lamp) }}"
                       class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                @empty
                <p class="text-muted small mb-0">Tidak ada lampiran.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Status Pengajuan</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex gap-3 align-items-start mb-3">
                    <div class="text-success"><i class="bi bi-check-circle-fill fs-5"></i></div>
                    <div>
                        <div class="small fw-semibold">Pengajuan Dibuat</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $reimburse->created_at?->format('d M Y H:i') }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">Oleh: {{ $reimburse->pembuatnya?->nama_lengkap }}</div>
                    </div>
                </div>

                <div class="d-flex gap-3 align-items-start">
                    @if($reimburse->status === 'MENUNGGU')
                        <div class="text-warning"><i class="bi bi-hourglass-split fs-5"></i></div>
                        <div>
                            <div class="small fw-semibold">Menunggu Persetujuan</div>
                            <div class="text-muted" style="font-size:0.75rem;">Belum diproses Admin Pusat</div>
                        </div>
                    @elseif($reimburse->status === 'DISETUJUI')
                        <div class="text-success"><i class="bi bi-check-circle-fill fs-5"></i></div>
                        <div>
                            <div class="small fw-semibold text-success">DISETUJUI</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $reimburse->tgl_diproses?->format('d M Y H:i') }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">Oleh: {{ $reimburse->pemrosesnya?->nama_lengkap }}</div>
                            <div class="fw-semibold text-success small">Rp {{ number_format($reimburse->nominal_disetujui, 0, ',', '.') }}</div>
                        </div>
                    @else
                        <div class="text-danger"><i class="bi bi-x-circle-fill fs-5"></i></div>
                        <div>
                            <div class="small fw-semibold text-danger">DITOLAK</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $reimburse->tgl_diproses?->format('d M Y H:i') }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">Oleh: {{ $reimburse->pemrosesnya?->nama_lengkap }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if(auth()->user()->role === 'SUPER_ADMIN')
<div class="modal fade" id="modalHapusReimburse" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Reimburse</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger small mb-3">
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data dan lampiran akan dihapus permanen.
                </div>
                <p class="mb-1 small">No. Reimburse: <strong>{{ $reimburse->no_reimburse }}</strong></p>
                <p class="mb-0 small">Pemohon: <strong>{{ $reimburse->nama_pemohon }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('reimburse.destroy', $reimburse) }}">
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
