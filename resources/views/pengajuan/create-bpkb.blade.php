@extends('layouts.app')
@section('title','Buat Pengajuan BPKB')
@section('page-title','Buat Pengajuan BPKB')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">
<form method="POST" action="{{ route('pengajuan.store-bpkb') }}" enctype="multipart/form-data" id="formBpkb">
@csrf
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="mb-0"><i class="bi bi-card-text me-2"></i>Data Pengajuan BPKB</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Asal Cabang <span class="text-danger">*</span></label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}" @selected(old('cabang_id') == $c->id || auth()->user()->cabang_id == $c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. Kartu Piutang <span class="text-danger">*</span></label>
                <input type="text" name="no_kartu_piutang" class="form-control @error('no_kartu_piutang') is-invalid @enderror"
                    value="{{ old('no_kartu_piutang') }}" placeholder="Nomor dari sistem existing" required>
                @error('no_kartu_piutang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Data Nasabah</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Nasabah <span class="text-danger">*</span></label>
                <input type="text" name="nama_nasabah" class="form-control @error('nama_nasabah') is-invalid @enderror"
                    value="{{ old('nama_nasabah') }}" required>
                @error('nama_nasabah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. KTP (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror"
                    value="{{ old('no_ktp') }}" maxlength="16" pattern="\d{16}" required>
                @error('no_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Total Pinjaman (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="total_pinjaman" class="form-control @error('total_pinjaman') is-invalid @enderror"
                    value="{{ old('total_pinjaman') }}" min="1" required>
                @error('total_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-car-front me-2"></i>Data Kendaraan & BPKB</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Polisi <span class="text-danger">*</span></label>
                <input type="text" name="no_polisi" class="form-control text-uppercase @error('no_polisi') is-invalid @enderror"
                    value="{{ old('no_polisi') }}" required>
                @error('no_polisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Merek Motor <span class="text-danger">*</span></label>
                <input type="text" name="merek_motor" class="form-control @error('merek_motor') is-invalid @enderror"
                    value="{{ old('merek_motor') }}" required>
                @error('merek_motor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tipe Motor <span class="text-danger">*</span></label>
                <input type="text" name="tipe_motor" class="form-control @error('tipe_motor') is-invalid @enderror"
                    value="{{ old('tipe_motor') }}" required>
                @error('tipe_motor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. BPKB <span class="text-danger">*</span></label>
                <input type="text" name="no_bpkb" class="form-control @error('no_bpkb') is-invalid @enderror"
                    value="{{ old('no_bpkb') }}" required>
                @error('no_bpkb')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Mesin <span class="text-danger">*</span></label>
                <input type="text" name="no_mesin" class="form-control @error('no_mesin') is-invalid @enderror"
                    value="{{ old('no_mesin') }}" required>
                @error('no_mesin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No. Rangka <span class="text-danger">*</span></label>
                <input type="text" name="no_rangka" class="form-control @error('no_rangka') is-invalid @enderror"
                    value="{{ old('no_rangka') }}" required>
                @error('no_rangka')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2"></i>Lampiran Dokumen</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">File BPKB <span class="text-danger">*</span></label>
                <input type="file" name="file_bpkb" class="form-control @error('file_bpkb') is-invalid @enderror"
                    accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="form-text">JPG/PNG/PDF, maks. 5MB</div>
                @error('file_bpkb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div id="preview_bpkb" class="mt-2"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">File KTP Nasabah <span class="text-danger">*</span></label>
                <input type="file" name="file_ktp" class="form-control @error('file_ktp') is-invalid @enderror"
                    accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="form-text">JPG/PNG/PDF, maks. 5MB</div>
                @error('file_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div id="preview_ktp" class="mt-2"></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Dokumen Tambahan (opsional)</label>
                <input type="file" name="file_lainnya[]" class="form-control" accept=".jpg,.jpeg,.png,.pdf" multiple>
                <div class="form-text">Bisa pilih lebih dari 1 file. JPG/PNG/PDF, maks. 5MB per file.</div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki kesalahan berikut:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-send me-1"></i>Kirim Pengajuan
    </button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
function previewFile(input, targetId) {
    input.addEventListener('change', function() {
        const target = document.getElementById(targetId);
        target.innerHTML = '';
        const file = this.files[0];
        if (!file) return;
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'img-thumbnail';
            img.style.maxHeight = '150px';
            target.appendChild(img);
        } else {
            target.innerHTML = `<span class="badge bg-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>${file.name}</span>`;
        }
    });
}
previewFile(document.querySelector('[name="file_bpkb"]'), 'preview_bpkb');
previewFile(document.querySelector('[name="file_ktp"]'), 'preview_ktp');
</script>
@endpush
