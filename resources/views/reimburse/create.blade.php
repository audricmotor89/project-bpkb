@extends('layouts.app')
@section('title','Buat Pengajuan Reimburse')
@section('page-title','Buat Pengajuan Reimburse')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-10">
<form method="POST" action="{{ route('reimburse.store') }}" enctype="multipart/form-data" id="formReimburse">
@csrf

{{-- Header Pengajuan --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header py-3" style="background:#1a237e">
        <h6 class="mb-0 text-white"><i class="bi bi-receipt me-2"></i>Pengajuan Penggantian Biaya (Reimburse)</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Asal Cabang <span class="text-danger">*</span></label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}" @selected(old('cabang_id')==$c->id || auth()->user()->cabang_id==$c->id)>
                            {{ $c->kode_cabang }} — {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Pemohon <span class="text-danger">*</span></label>
                <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror"
                    value="{{ old('nama_pemohon', auth()->user()->nama_lengkap) }}" required>
                @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Opsional">
            </div>
        </div>
    </div>
</div>

{{-- Daftar Item Reimburse --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-list-ul me-2"></i>Item Reimburse <span class="text-danger">*</span></h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahItem">
            <i class="bi bi-plus-circle me-1"></i>Tambah Item
        </button>
    </div>
    <div class="card-body p-2" id="itemsContainer">
        {{-- Item pertama --}}
        <div class="item-reimburse border rounded p-3 mb-2 position-relative" data-index="0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-white item-nomor" style="background:#1a237e">Item #1</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-item d-none">
                    <i class="bi bi-trash me-1"></i>Hapus Item
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                    <input type="date" name="items[0][tanggal_pengeluaran]" class="form-control"
                        value="{{ old('items.0.tanggal_pengeluaran', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Kategori Biaya <span class="text-danger">*</span></label>
                    <select name="items[0][kategori]" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $key => $label)
                            <option value="{{ $key }}" @selected(old('items.0.kategori')===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Nominal Diajukan (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="items[0][nominal_diajukan]" class="form-control nominal-input"
                            value="{{ old('items.0.nominal_diajukan') }}" min="1" step="1" placeholder="0" required>
                    </div>
                    <div class="form-text nominal-terbilang"></div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Keterangan / Rincian Biaya <span class="text-danger">*</span></label>
                    <textarea name="items[0][keterangan]" rows="2"
                        class="form-control"
                        placeholder="Contoh: Biaya transport perjalanan dinas ke kantor pusat"
                        required>{{ old('items.0.keterangan') }}</textarea>
                </div>
            </div>
            {{-- Lampiran per item --}}
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold"><i class="bi bi-paperclip me-1"></i>Lampiran Bukti <span class="text-danger">*</span></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-tambah-lampiran">
                        <i class="bi bi-plus me-1"></i>Tambah File
                    </button>
                </div>
                <div class="lampiran-container">
                    <div class="lampiran-row border rounded p-2 mb-1">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <select name="jenis_lampiran_0[]" class="form-select form-select-sm" required>
                                    <option value="KWITANSI">Kwitansi</option>
                                    <option value="STRUK">Struk / Receipt</option>
                                    <option value="FOTO">Foto Bukti</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="file" name="lampiran_0[]" class="form-control form-control-sm file-input"
                                    accept=".jpg,.jpeg,.png,.pdf" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-lampiran d-none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="col-12 preview-area"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Total --}}
<div class="card border-0 shadow-sm mb-3 border-start border-primary border-3">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Total Nominal Diajukan</span>
        <span class="fs-5 fw-bold text-primary" id="totalNominal">Rp 0</span>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki kesalahan berikut:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('reimburse.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn text-white" style="background:#1a237e">
        <i class="bi bi-send me-1"></i>Kirim Pengajuan Reimburse
    </button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
const kategoriOptions = `
    <option value="">-- Pilih Kategori --</option>
    @foreach($kategori as $key => $label)
    <option value="{{ $key }}">{{ $label }}</option>
    @endforeach
`;

let itemCount = 1;

function updateItemNumbers() {
    document.querySelectorAll('.item-reimburse').forEach((el, i) => {
        el.querySelector('.item-nomor').textContent = `Item #${i + 1}`;
        const hapus = el.querySelector('.btn-hapus-item');
        hapus.classList.toggle('d-none', document.querySelectorAll('.item-reimburse').length === 1);
    });
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.nominal-input').forEach(inp => {
        total += parseInt(inp.value) || 0;
    });
    document.getElementById('totalNominal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function showPreview(input, target) {
    target.innerHTML = '';
    const file = input.files[0];
    if (!file) return;
    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'img-thumbnail mt-1';
        img.style.maxHeight = '100px';
        target.appendChild(img);
    } else {
        target.innerHTML = `<span class="badge bg-danger mt-1"><i class="bi bi-file-earmark-pdf me-1"></i>${file.name}</span>`;
    }
}

function bindLampiranEvents(row) {
    const hapus = row.querySelector('.btn-hapus-lampiran');
    if (hapus) {
        hapus.addEventListener('click', () => {
            const container = row.closest('.lampiran-container');
            row.remove();
            container.querySelectorAll('.btn-hapus-lampiran').forEach((b, i) => {
                b.classList.toggle('d-none', container.querySelectorAll('.lampiran-row').length === 1);
            });
        });
    }
    const fileInput = row.querySelector('.file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            showPreview(this, row.querySelector('.preview-area'));
        });
    }
}

function bindItemEvents(itemEl) {
    const idx = itemEl.dataset.index;

    itemEl.querySelector('.btn-hapus-item').addEventListener('click', () => {
        itemEl.remove();
        updateItemNumbers();
        updateTotal();
    });

    itemEl.querySelector('.nominal-input').addEventListener('input', function() {
        const val = parseInt(this.value) || 0;
        itemEl.querySelector('.nominal-terbilang').textContent = val > 0 ? 'Rp ' + val.toLocaleString('id-ID') : '';
        updateTotal();
    });

    itemEl.querySelector('.btn-tambah-lampiran').addEventListener('click', () => {
        const container = itemEl.querySelector('.lampiran-container');
        const firstRow = container.querySelector('.lampiran-row');
        const clone = firstRow.cloneNode(true);
        clone.querySelector('input[type="file"]').value = '';
        clone.querySelector('.preview-area').innerHTML = '';
        clone.querySelector('.btn-hapus-lampiran').classList.remove('d-none');
        container.appendChild(clone);
        bindLampiranEvents(clone);
        container.querySelectorAll('.btn-hapus-lampiran').forEach(b => b.classList.remove('d-none'));
    });

    itemEl.querySelectorAll('.lampiran-row').forEach(row => bindLampiranEvents(row));
}

document.getElementById('btnTambahItem').addEventListener('click', () => {
    const idx = itemCount++;
    const tmpl = `
    <div class="item-reimburse border rounded p-3 mb-2 position-relative" data-index="${idx}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge text-white item-nomor" style="background:#1a237e">Item #?</span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-item">
                <i class="bi bi-trash me-1"></i>Hapus Item
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                <input type="date" name="items[${idx}][tanggal_pengeluaran]" class="form-control"
                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Kategori Biaya <span class="text-danger">*</span></label>
                <select name="items[${idx}][kategori]" class="form-select" required>
                    ${kategoriOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Nominal Diajukan (Rp) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="items[${idx}][nominal_diajukan]" class="form-control nominal-input"
                        min="1" step="1" placeholder="0" required>
                </div>
                <div class="form-text nominal-terbilang"></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Keterangan / Rincian Biaya <span class="text-danger">*</span></label>
                <textarea name="items[${idx}][keterangan]" rows="2"
                    class="form-control"
                    placeholder="Contoh: Biaya transport perjalanan dinas ke kantor pusat"
                    required></textarea>
            </div>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold"><i class="bi bi-paperclip me-1"></i>Lampiran Bukti <span class="text-danger">*</span></span>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-tambah-lampiran">
                    <i class="bi bi-plus me-1"></i>Tambah File
                </button>
            </div>
            <div class="lampiran-container">
                <div class="lampiran-row border rounded p-2 mb-1">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <select name="jenis_lampiran_${idx}[]" class="form-select form-select-sm" required>
                                <option value="KWITANSI">Kwitansi</option>
                                <option value="STRUK">Struk / Receipt</option>
                                <option value="FOTO">Foto Bukti</option>
                                <option value="LAINNYA">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="file" name="lampiran_${idx}[]" class="form-control form-control-sm file-input"
                                accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-lampiran d-none">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="col-12 preview-area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', tmpl);
    const newItem = document.getElementById('itemsContainer').lastElementChild;
    bindItemEvents(newItem);
    updateItemNumbers();
});

// Init first item
document.querySelectorAll('.item-reimburse').forEach(el => bindItemEvents(el));
updateItemNumbers();
</script>
@endpush
