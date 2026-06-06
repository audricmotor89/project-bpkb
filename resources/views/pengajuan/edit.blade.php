@extends('layouts.app')
@section('title','Edit Pengajuan')
@section('page-title','Edit Data Pengajuan — Super Admin')

@section('content')
@php $isBpkb = $pengajuan->jenis_jaminan === 'BPKB'; @endphp

<div class="row justify-content-center">
<div class="col-xl-9">

{{-- Warning --}}
<div class="alert alert-warning d-flex gap-2 align-items-start mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
    <div>
        <strong>Mode Koreksi Data — Super Admin</strong><br>
        <small>Perubahan ini akan dicatat di audit log. No. pengajuan dan status tidak berubah.</small>
    </div>
</div>

<form method="POST" action="{{ route('pengajuan.update', $pengajuan) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- Header info --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header py-3" style="background:#1a237e;">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white font-monospace">{{ $pengajuan->no_pengajuan }}</h6>
            <span class="badge bg-{{ $isBpkb ? 'light text-primary' : 'light text-info' }} fs-6">
                {{ $pengajuan->jenis_jaminan }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Asal Cabang <span class="text-danger">*</span></label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}"
                            @selected(old('cabang_id', $pengajuan->cabang_id) == $c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. Kartu Piutang <span class="text-danger">*</span></label>
                <input type="text" name="no_kartu_piutang" class="form-control @error('no_kartu_piutang') is-invalid @enderror"
                    value="{{ old('no_kartu_piutang', $isBpkb ? $pengajuan->detailBpkb?->no_kartu_piutang : $pengajuan->detailSertifikat?->no_kartu_piutang) }}" required>
                @error('no_kartu_piutang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Data Nasabah --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Data Nasabah</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Nasabah <span class="text-danger">*</span></label>
                <input type="text" name="nama_nasabah" class="form-control @error('nama_nasabah') is-invalid @enderror"
                    value="{{ old('nama_nasabah', $isBpkb ? $pengajuan->detailBpkb?->nama_nasabah : $pengajuan->detailSertifikat?->nama_nasabah) }}" required>
                @error('nama_nasabah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. KTP (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror"
                    value="{{ old('no_ktp', $isBpkb ? $pengajuan->detailBpkb?->no_ktp : $pengajuan->detailSertifikat?->no_ktp) }}"
                    maxlength="16" required>
                @error('no_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Total Pinjaman (Rp) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="total_pinjaman" class="form-control @error('total_pinjaman') is-invalid @enderror"
                        value="{{ old('total_pinjaman', $isBpkb ? $pengajuan->detailBpkb?->total_pinjaman : $pengajuan->detailSertifikat?->total_pinjaman) }}"
                        min="1" required>
                    @error('total_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @if(!$isBpkb)
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. Sertifikat <span class="text-danger">*</span></label>
                <input type="text" name="no_sertifikat" class="form-control @error('no_sertifikat') is-invalid @enderror"
                    value="{{ old('no_sertifikat', $pengajuan->detailSertifikat?->no_sertifikat) }}" required>
                @error('no_sertifikat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endif
        </div>
    </div>
</div>

@if($isBpkb)
{{-- Data Kendaraan & BPKB --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-car-front me-2"></i>Data Kendaraan & BPKB</h6>
    </div>
    <div class="card-body">
        @php $db = $pengajuan->detailBpkb; @endphp
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Polisi <span class="text-danger">*</span></label>
                <input type="text" name="no_polisi" class="form-control text-uppercase @error('no_polisi') is-invalid @enderror"
                    value="{{ old('no_polisi', $db?->no_polisi) }}" required>
                @error('no_polisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Merek Motor <span class="text-danger">*</span></label>
                <input type="text" name="merek_motor" class="form-control @error('merek_motor') is-invalid @enderror"
                    value="{{ old('merek_motor', $db?->merek_motor) }}" required>
                @error('merek_motor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tipe Motor <span class="text-danger">*</span></label>
                <input type="text" name="tipe_motor" class="form-control @error('tipe_motor') is-invalid @enderror"
                    value="{{ old('tipe_motor', $db?->tipe_motor) }}" required>
                @error('tipe_motor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. BPKB <span class="text-danger">*</span></label>
                <input type="text" name="no_bpkb" class="form-control @error('no_bpkb') is-invalid @enderror"
                    value="{{ old('no_bpkb', $db?->no_bpkb) }}" required>
                @error('no_bpkb')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Mesin <span class="text-danger">*</span></label>
                <input type="text" name="no_mesin" class="form-control @error('no_mesin') is-invalid @enderror"
                    value="{{ old('no_mesin', $db?->no_mesin) }}" required>
                @error('no_mesin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Rangka <span class="text-danger">*</span></label>
                <input type="text" name="no_rangka" class="form-control @error('no_rangka') is-invalid @enderror"
                    value="{{ old('no_rangka', $db?->no_rangka) }}" required>
                @error('no_rangka')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
@endif

{{-- Lampiran yang ada --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2"></i>Lampiran ({{ $pengajuan->lampiran->count() }} file)</h6>
    </div>
    <div class="card-body">
        @forelse($pengajuan->lampiran as $lamp)
        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
            <div class="d-flex align-items-center gap-3">
                @if(str_starts_with($lamp->mime_type, 'image/'))
                    <img src="{{ route('lampiran.download', $lamp) }}"
                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;"
                         onerror="this.style.display='none'">
                @else
                    <div class="text-danger fs-3"><i class="bi bi-file-earmark-pdf"></i></div>
                @endif
                <div>
                    <div class="small fw-semibold">{{ $lamp->nama_file_asli }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">
                        <span class="badge bg-secondary me-1">{{ $lamp->jenis_dokumen }}</span>
                        {{ $lamp->ukuran_format }}
                    </div>
                </div>
            </div>
            <div class="form-check form-check-danger">
                <input class="form-check-input border-danger" type="checkbox"
                    name="hapus_lampiran[]" value="{{ $lamp->id }}"
                    id="hapus{{ $lamp->id }}">
                <label class="form-check-label small text-danger" for="hapus{{ $lamp->id }}">
                    <i class="bi bi-trash me-1"></i>Hapus
                </label>
            </div>
        </div>
        @empty
        <p class="text-muted small mb-0">Tidak ada lampiran.</p>
        @endforelse

        {{-- Upload file tambahan --}}
        <div class="mt-3 pt-3 border-top">
            <label class="form-label fw-semibold small"><i class="bi bi-upload me-1"></i>Tambah File Baru (opsional)</label>
            <input type="file" name="file_tambahan[]" class="form-control form-control-sm"
                accept=".jpg,.jpeg,.png,.pdf" multiple>
            <div class="form-text">Bisa pilih lebih dari 1 file. JPG/PNG/PDF, maks. 5MB per file.</div>
        </div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('pengajuan.show', $pengajuan) }}" class="btn btn-secondary">
        <i class="bi bi-x-circle me-1"></i>Batal
    </a>
    <button type="submit" class="btn btn-warning fw-semibold"
        onclick="return confirm('Simpan perubahan data pengajuan {{ $pengajuan->no_pengajuan }}?')">
        <i class="bi bi-pencil-square me-1"></i>Simpan Koreksi
    </button>
</div>

</form>
</div>
</div>
@endsection
