@extends('layouts.app')
@section('title','Edit Reimburse')
@section('page-title','Edit Reimburse')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">

<div class="alert alert-warning d-flex gap-2 align-items-start mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Mode Edit — Super Admin</strong><br>
        <span class="small">Gunakan fitur ini untuk memperbaiki data yang salah input, seperti cabang, nominal, atau lampiran. Perubahan berlaku langsung.</span>
    </div>
</div>

<form method="POST" action="{{ route('reimburse.update', $reimburse) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:#1a237e">
        <h6 class="mb-0 text-white"><i class="bi bi-receipt me-2"></i>Edit Pengajuan Reimburse</h6>
        <span class="badge bg-white font-monospace" style="color:#1a237e;">{{ $reimburse->no_reimburse }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Asal Cabang <span class="text-danger">*</span></label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}" @selected(old('cabang_id', $reimburse->cabang_id) == $c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_pengeluaran" class="form-control @error('tanggal_pengeluaran') is-invalid @enderror"
                    value="{{ old('tanggal_pengeluaran', $reimburse->tanggal_pengeluaran?->format('Y-m-d')) }}"
                    max="{{ date('Y-m-d') }}" required>
                @error('tanggal_pengeluaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Pemohon <span class="text-danger">*</span></label>
                <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror"
                    value="{{ old('nama_pemohon', $reimburse->nama_pemohon) }}" required>
                @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Jabatan</label>
                <input type="text" name="jabatan" class="form-control"
                    value="{{ old('jabatan', $reimburse->jabatan) }}" placeholder="Opsional">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Kategori Biaya <span class="text-danger">*</span></label>
                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $key => $label)
                        <option value="{{ $key }}" @selected(old('kategori', $reimburse->kategori) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nominal Diajukan (Rp) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal_diajukan" id="nominalInput"
                        class="form-control @error('nominal_diajukan') is-invalid @enderror"
                        value="{{ old('nominal_diajukan', $reimburse->nominal_diajukan) }}" min="1" step="1" required>
                    @error('nominal_diajukan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-text" id="nominalTerbilang"></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Keterangan / Rincian Biaya <span class="text-danger">*</span></label>
                <textarea name="keterangan" rows="3"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    required>{{ old('keterangan', $reimburse->keterangan) }}</textarea>
                @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Lampiran Existing --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2"></i>Lampiran Saat Ini ({{ $reimburse->lampiran->count() }} file)</h6>
    </div>
    <div class="card-body">
        @forelse($reimburse->lampiran as $lamp)
        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
            <div class="d-flex align-items-center gap-3">
                @if(str_starts_with($lamp->mime_type, 'image/'))
                    <img src="{{ route('reimburse.lampiran.preview', $lamp) }}"
                         class="rounded border" style="width:50px;height:50px;object-fit:cover;">
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
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="hapus_lampiran[]"
                    value="{{ $lamp->id }}" id="hapus_{{ $lamp->id }}">
                <label class="form-check-label small text-danger fw-semibold" for="hapus_{{ $lamp->id }}">
                    <i class="bi bi-trash me-1"></i>Hapus
                </label>
            </div>
        </div>
        @empty
        <p class="text-muted small mb-0">Tidak ada lampiran.</p>
        @endforelse

        @if($reimburse->lampiran->count())
        <div class="alert alert-warning small py-2 mt-2 mb-0">
            <i class="bi bi-info-circle me-1"></i>Centang "Hapus" pada lampiran yang ingin dihapus, lalu klik Simpan Perubahan.
        </div>
        @endif
    </div>
</div>

{{-- Lampiran Baru --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Lampiran Baru (opsional)</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahLampiran">
            <i class="bi bi-plus-circle me-1"></i>Tambah File
        </button>
    </div>
    <div class="card-body">
        <div id="lampiranBaruContainer"></div>
        <div class="text-muted small">Klik "Tambah File" untuk menambahkan lampiran baru.</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki kesalahan berikut:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('reimburse.show', $reimburse) }}" class="btn btn-secondary">Batal</a>
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
document.getElementById('nominalInput').addEventListener('input', function() {
    const val = parseInt(this.value) || 0;
    document.getElementById('nominalTerbilang').textContent = val > 0
        ? 'Rp ' + val.toLocaleString('id-ID') : '';
});

// Trigger once for existing value
(function() {
    const val = parseInt(document.getElementById('nominalInput').value) || 0;
    if (val > 0) document.getElementById('nominalTerbilang').textContent = 'Rp ' + val.toLocaleString('id-ID');
})();

const rowTemplate = `
<div class="lampiran-baru-row border rounded p-3 mb-2">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Jenis Dokumen</label>
            <select name="jenis_lampiran_baru[]" class="form-select form-select-sm">
                <option value="KWITANSI">Kwitansi</option>
                <option value="STRUK">Struk / Receipt</option>
                <option value="FOTO">Foto Bukti</option>
                <option value="LAINNYA">Lainnya</option>
            </select>
        </div>
        <div class="col-md-7">
            <label class="form-label small fw-semibold">File</label>
            <input type="file" name="lampiran_baru[]" class="form-control form-control-sm file-input"
                accept=".jpg,.jpeg,.png,.pdf">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-row">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="col-12 preview-area"></div>
    </div>
</div>`;

document.getElementById('btnTambahLampiran').addEventListener('click', function() {
    const container = document.getElementById('lampiranBaruContainer');
    container.querySelector('.text-muted')?.remove();
    const tmp = document.createElement('div');
    tmp.innerHTML = rowTemplate;
    const row = tmp.firstElementChild;
    row.querySelector('.btn-hapus-row').addEventListener('click', () => row.remove());
    row.querySelector('.file-input').addEventListener('change', function() {
        showPreview(this, row.querySelector('.preview-area'));
    });
    container.appendChild(row);
});

function showPreview(input, target) {
    target.innerHTML = '';
    const file = input.files[0];
    if (!file) return;
    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'img-thumbnail mt-2';
        img.style.maxHeight = '100px';
        target.appendChild(img);
    } else {
        target.innerHTML = `<span class="badge bg-danger mt-1"><i class="bi bi-file-earmark-pdf me-1"></i>${file.name}</span>`;
    }
}
</script>
@endpush
