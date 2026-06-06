@extends('layouts.app')
@section('title','Edit Jaminan Kerja')
@section('page-title','Edit Jaminan Kerja')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">

<div class="alert alert-warning d-flex gap-2 align-items-start mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Mode Edit — Super Admin</strong><br>
        <span class="small">Perubahan pada data ini akan berlaku langsung. Gunakan fitur ini hanya untuk memperbaiki kesalahan input seperti cabang yang salah, nama, atau nomor KTP.</span>
    </div>
</div>

<form method="POST" action="{{ route('jaminan-kerja.update', $jaminanKerja) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- Data Karyawan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Karyawan</h6>
        <span class="badge bg-white text-primary font-monospace">{{ $jaminanKerja->no_jaminan }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Asal Cabang <span class="text-danger">*</span></label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}" @selected(old('cabang_id', $jaminanKerja->cabang_id) == $c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Tanggal Masuk Kerja <span class="text-danger">*</span></label>
                <input type="date" name="tgl_masuk_kerja" class="form-control @error('tgl_masuk_kerja') is-invalid @enderror"
                    value="{{ old('tgl_masuk_kerja', $jaminanKerja->tgl_masuk_kerja?->format('Y-m-d')) }}" required>
                @error('tgl_masuk_kerja')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Karyawan <span class="text-danger">*</span></label>
                <input type="text" name="nama_karyawan" class="form-control @error('nama_karyawan') is-invalid @enderror"
                    value="{{ old('nama_karyawan', $jaminanKerja->nama_karyawan) }}" required>
                @error('nama_karyawan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. KTP (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror"
                    value="{{ old('no_ktp', $jaminanKerja->no_ktp) }}" maxlength="16" pattern="\d{16}" required>
                @error('no_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                    value="{{ old('jabatan', $jaminanKerja->jabatan) }}" required>
                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">No. HP / WhatsApp</label>
                <input type="text" name="no_hp" class="form-control"
                    value="{{ old('no_hp', $jaminanKerja->no_hp) }}" placeholder="08xxxxxxxxxx">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $jaminanKerja->catatan) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Jenis Jaminan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2"></i>Jenis Jaminan & Dokumen</h6>
    </div>
    <div class="card-body">
        @error('has_akte')
        <div class="alert alert-danger small py-2">{{ $message }}</div>
        @enderror
        <div class="row g-3">

            {{-- Akte Kelahiran --}}
            <div class="col-md-4">
                <div class="card border rounded h-100 {{ old('has_akte', $jaminanKerja->has_akte) ? 'border-primary bg-light' : '' }}" id="card-akte">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_akte" value="1" id="chkAkte"
                                {{ old('has_akte', $jaminanKerja->has_akte) ? 'checked' : '' }}
                                onchange="toggleJaminan('akte', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkAkte">
                                <i class="bi bi-file-earmark-person me-1 text-info"></i>Akte Kelahiran
                            </label>
                        </div>
                        <div id="upload-akte" style="{{ old('has_akte', $jaminanKerja->has_akte) ? '' : 'display:none;' }}">
                            {{-- Existing files --}}
                            @php $akteLampiran = $jaminanKerja->lampiranByJenis('AKTE_KELAHIRAN'); @endphp
                            @if($akteLampiran->count())
                            <div class="mb-2">
                                <div class="small fw-semibold text-muted mb-1">File yang sudah ada:</div>
                                @foreach($akteLampiran as $l)
                                <div class="d-flex align-items-center gap-2 border rounded p-1 mb-1 bg-white">
                                    @if(str_starts_with($l->mime_type, 'image/'))
                                        <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" style="width:36px;height:36px;object-fit:cover;border-radius:4px;">
                                    @else
                                        <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                    @endif
                                    <div class="flex-fill small text-truncate" style="max-width:100px;">{{ $l->nama_file_asli }}</div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" id="hapus_{{ $l->id }}">
                                        <label class="form-check-label small text-danger" for="hapus_{{ $l->id }}">Hapus</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="small fw-semibold text-muted mb-1">Tambah file baru:</div>
                            @error('file_akte.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_akte[]" preview-id="preview_akte_edit" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- BPKB --}}
            <div class="col-md-4">
                <div class="card border rounded h-100 {{ old('has_bpkb', $jaminanKerja->has_bpkb) ? 'border-primary bg-light' : '' }}" id="card-bpkb">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_bpkb" value="1" id="chkBpkb"
                                {{ old('has_bpkb', $jaminanKerja->has_bpkb) ? 'checked' : '' }}
                                onchange="toggleJaminan('bpkb', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkBpkb">
                                <i class="bi bi-car-front me-1 text-primary"></i>BPKB
                            </label>
                        </div>
                        <div id="upload-bpkb" style="{{ old('has_bpkb', $jaminanKerja->has_bpkb) ? '' : 'display:none;' }}">
                            @php $bpkbLampiran = $jaminanKerja->lampiranByJenis('BPKB'); @endphp
                            @if($bpkbLampiran->count())
                            <div class="mb-2">
                                <div class="small fw-semibold text-muted mb-1">File yang sudah ada:</div>
                                @foreach($bpkbLampiran as $l)
                                <div class="d-flex align-items-center gap-2 border rounded p-1 mb-1 bg-white">
                                    @if(str_starts_with($l->mime_type, 'image/'))
                                        <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" style="width:36px;height:36px;object-fit:cover;border-radius:4px;">
                                    @else
                                        <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                    @endif
                                    <div class="flex-fill small text-truncate" style="max-width:100px;">{{ $l->nama_file_asli }}</div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" id="hapus_{{ $l->id }}">
                                        <label class="form-check-label small text-danger" for="hapus_{{ $l->id }}">Hapus</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="small fw-semibold text-muted mb-1">Tambah file baru:</div>
                            @error('file_bpkb.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_bpkb[]" preview-id="preview_bpkb_edit" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ijasah --}}
            <div class="col-md-4">
                <div class="card border rounded h-100 {{ old('has_ijasah', $jaminanKerja->has_ijasah) ? 'border-primary bg-light' : '' }}" id="card-ijasah">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_ijasah" value="1" id="chkIjasah"
                                {{ old('has_ijasah', $jaminanKerja->has_ijasah) ? 'checked' : '' }}
                                onchange="toggleJaminan('ijasah', this.checked)">
                            <label class="form-check-label fw-semibold" for="chkIjasah">
                                <i class="bi bi-mortarboard me-1 text-success"></i>Ijasah
                            </label>
                        </div>
                        <div id="upload-ijasah" style="{{ old('has_ijasah', $jaminanKerja->has_ijasah) ? '' : 'display:none;' }}">
                            @php $ijasahLampiran = $jaminanKerja->lampiranByJenis('IJASAH'); @endphp
                            @if($ijasahLampiran->count())
                            <div class="mb-2">
                                <div class="small fw-semibold text-muted mb-1">File yang sudah ada:</div>
                                @foreach($ijasahLampiran as $l)
                                <div class="d-flex align-items-center gap-2 border rounded p-1 mb-1 bg-white">
                                    @if(str_starts_with($l->mime_type, 'image/'))
                                        <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" style="width:36px;height:36px;object-fit:cover;border-radius:4px;">
                                    @else
                                        <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                    @endif
                                    <div class="flex-fill small text-truncate" style="max-width:100px;">{{ $l->nama_file_asli }}</div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" id="hapus_{{ $l->id }}">
                                        <label class="form-check-label small text-danger" for="hapus_{{ $l->id }}">Hapus</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="small fw-semibold text-muted mb-1">Tambah file baru:</div>
                            @error('file_ijasah.*')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <x-file-upload-dual name="file_ijasah[]" preview-id="preview_ijasah_edit" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Foto Penerimaan & Lainnya --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2"></i>Bukti & Dokumen Tambahan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Foto Penerimaan Jaminan</label>
                <div class="form-text mb-2">Foto yang sudah ada. Centang untuk menghapus, atau upload foto baru.</div>
                @php $fotoTerima = $jaminanKerja->lampiranByJenis('FOTO_PENERIMAAN'); @endphp
                @if($fotoTerima->count())
                <div class="mb-2">
                    @foreach($fotoTerima as $l)
                    <div class="d-flex align-items-center gap-2 border rounded p-1 mb-1">
                        <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                        <div class="flex-fill small text-truncate">{{ $l->nama_file_asli }}</div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" id="hapus_{{ $l->id }}">
                            <label class="form-check-label small text-danger" for="hapus_{{ $l->id }}">Hapus</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="small fw-semibold text-muted mb-1">Tambah foto baru (opsional):</div>
                <x-file-upload-dual name="foto_penerimaan[]" preview-id="preview_foto_edit" :foto-only="true" />
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Dokumen Tambahan</label>
                <div class="form-text mb-2">Dokumen lainnya yang sudah ada atau tambah baru.</div>
                @php $lainnya = $jaminanKerja->lampiranByJenis('LAINNYA'); @endphp
                @if($lainnya->count())
                <div class="mb-2">
                    @foreach($lainnya as $l)
                    <div class="d-flex align-items-center gap-2 border rounded p-1 mb-1">
                        @if(str_starts_with($l->mime_type, 'image/'))
                            <img src="{{ route('jaminan-kerja.lampiran.preview', $l) }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                        @else
                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                        @endif
                        <div class="flex-fill small text-truncate">{{ $l->nama_file_asli }}</div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" id="hapus_{{ $l->id }}">
                            <label class="form-check-label small text-danger" for="hapus_{{ $l->id }}">Hapus</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="small fw-semibold text-muted mb-1">Tambah file baru (opsional):</div>
                <x-file-upload-dual name="file_lainnya[]" preview-id="preview_lainnya_edit" />
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
    <a href="{{ route('jaminan-kerja.show', $jaminanKerja) }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-warning">
        <i class="bi bi-save me-1"></i>Simpan Perubahan
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
</script>
@endpush
