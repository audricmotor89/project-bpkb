@extends('layouts.app')
@section('title','Catat Penyerahan Jaminan Kerja')
@section('page-title','Catat Penyerahan Jaminan Kerja')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">
<form method="POST" action="{{ route('jaminan-kerja.store') }}" enctype="multipart/form-data" id="formJaminanKerja">
@csrf

{{-- Data Karyawan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Karyawan</h6>
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
                <label class="form-label fw-semibold small">Tanggal Masuk Kerja <span class="text-danger">*</span></label>
                <input type="date" name="tgl_masuk_kerja" class="form-control @error('tgl_masuk_kerja') is-invalid @enderror"
                    value="{{ old('tgl_masuk_kerja') }}" required>
                @error('tgl_masuk_kerja')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Karyawan <span class="text-danger">*</span></label>
                <input type="text" name="nama_karyawan" class="form-control @error('nama_karyawan') is-invalid @enderror"
                    value="{{ old('nama_karyawan') }}" required>
                @error('nama_karyawan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. KTP (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror"
                    value="{{ old('no_ktp') }}" maxlength="16" pattern="\d{16}" required>
                @error('no_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                    value="{{ old('jabatan') }}" required>
                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. HP / WhatsApp</label>
                <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                    value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Catatan</label>
                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2"
                    placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Jenis Jaminan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2"></i>Jenis Jaminan yang Diserahkan</h6>
    </div>
    <div class="card-body">
        @error('has_akte')
        <div class="alert alert-danger small py-2">{{ $message }}</div>
        @enderror
        <div class="row g-3">

            {{-- Akte Kelahiran --}}
            <div class="col-md-4">
                <div class="card border rounded h-100" id="card-akte">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_akte" value="1" id="chkAkte"
                                {{ old('has_akte') ? 'checked' : '' }} onchange="toggleJaminan('akte', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkAkte">
                                <i class="bi bi-file-earmark-person me-1 text-info"></i>Akte Kelahiran
                            </label>
                        </div>
                        <div id="upload-akte" style="{{ old('has_akte') ? '' : 'display:none;' }}">
                            @error('file_akte')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            @error('file_akte.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_akte[]" preview-id="preview_akte" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- BPKB --}}
            <div class="col-md-4">
                <div class="card border rounded h-100" id="card-bpkb">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_bpkb" value="1" id="chkBpkb"
                                {{ old('has_bpkb') ? 'checked' : '' }} onchange="toggleJaminan('bpkb', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkBpkb">
                                <i class="bi bi-car-front me-1 text-primary"></i>BPKB
                            </label>
                        </div>
                        <div id="upload-bpkb" style="{{ old('has_bpkb') ? '' : 'display:none;' }}">
                            @error('file_bpkb')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            @error('file_bpkb.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_bpkb[]" preview-id="preview_bpkb" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ijasah --}}
            <div class="col-md-4">
                <div class="card border rounded h-100" id="card-ijasah">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_ijasah" value="1" id="chkIjasah"
                                {{ old('has_ijasah') ? 'checked' : '' }} onchange="toggleJaminan('ijasah', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkIjasah">
                                <i class="bi bi-mortarboard me-1 text-success"></i>Ijasah
                            </label>
                        </div>
                        <div id="upload-ijasah" style="{{ old('has_ijasah') ? '' : 'display:none;' }}">
                            @error('file_ijasah')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            @error('file_ijasah.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_ijasah[]" preview-id="preview_ijasah" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Foto Penerimaan + Dokumen Tambahan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2"></i>Bukti Penerimaan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">
                    Foto Penerimaan Jaminan <span class="text-danger">*</span>
                </label>
                <div class="form-text mb-2">Foto saat menerima dokumen dari karyawan. Bisa lebih dari 1 foto.</div>
                @error('foto_penerimaan')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                @error('foto_penerimaan.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                <x-file-upload-dual name="foto_penerimaan[]" preview-id="preview_foto_penerimaan" :foto-only="true" :required="true" />
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Dokumen Tambahan (opsional)</label>
                <div class="form-text mb-2">File pendukung lainnya. JPG/PNG/PDF, maks. 5MB per file.</div>
                <x-file-upload-dual name="file_lainnya[]" preview-id="preview_lainnya" />
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
    <a href="{{ route('jaminan-kerja.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i>Simpan Penyerahan Jaminan
    </button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleJaminan(type, checked) {
    const uploadDiv = document.getElementById('upload-' + type);
    const card = document.getElementById('card-' + type);
    uploadDiv.style.display = checked ? '' : 'none';
    card.classList.toggle('border-primary', checked);
    card.classList.toggle('bg-light', checked);
}

// Init state dari old() jika ada validasi gagal
['akte','bpkb','ijasah'].forEach(type => {
    const chk = document.getElementById('chk' + type.charAt(0).toUpperCase() + type.slice(1));
    if (chk && chk.checked) {
        const card = document.getElementById('card-' + type);
        card.classList.add('border-primary', 'bg-light');
    }
});
</script>
@endpush
