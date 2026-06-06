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
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Nama Pemohon <span class="text-danger">*</span></label>
                <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror"
                    value="{{ old('nama_pemohon', auth()->user()->nama_lengkap) }}" required>
                @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
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
                <div class="col-md-5">
                    <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                    <input type="date" name="items[0][tanggal_pengeluaran]" class="form-control"
                        value="{{ old('items.0.tanggal_pengeluaran', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-7">
                    <label class="form-label fw-semibold small">Keterangan / Rincian Biaya <span class="text-danger">*</span></label>
                    <input type="text" name="items[0][keterangan]" class="form-control"
                        placeholder="Contoh: Biaya transport perjalanan dinas ke kantor pusat"
                        value="{{ old('items.0.keterangan') }}" required>
                </div>
            </div>

            {{-- Lampiran per item --}}
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold">
                        <i class="bi bi-paperclip me-1"></i>Lampiran Bukti <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(maks. 5 baris)</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-tambah-lampiran">
                        <i class="bi bi-plus me-1"></i>Tambah Baris
                    </button>
                </div>
                <div class="alert alert-light border py-2 mb-2 small">
                    <i class="bi bi-camera me-1 text-primary"></i>
                    Bisa upload dari <strong>galeri</strong> atau langsung <strong>ambil foto</strong> menggunakan kamera HP.
                    Format: JPG/PNG/PDF, maks. 5MB per file.
                </div>
                {{-- Header tabel --}}
                <div class="row g-1 mb-1 d-none d-md-flex px-1">
                    <div class="col-md-3"><span class="small fw-semibold text-muted">Kategori Biaya</span></div>
                    <div class="col-md-2"><span class="small fw-semibold text-muted">Jenis Dokumen</span></div>
                    <div class="col-md-4"><span class="small fw-semibold text-muted">File Bukti</span></div>
                    <div class="col-md-2"><span class="small fw-semibold text-muted">Nominal (Rp)</span></div>
                    <div class="col-md-1"></div>
                </div>
                <div class="lampiran-container">
                    @for($r = 0; $r < 3; $r++)
                    <div class="lampiran-row border rounded p-2 mb-1">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <select name="kategori_lampiran_0[]" class="form-select form-select-sm" {{ $r===0?'required':'' }}>
                                    <option value="">-- Kategori --</option>
                                    @foreach($kategori as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="jenis_lampiran_0[]" class="form-select form-select-sm">
                                    <option value="KWITANSI">Kwitansi</option>
                                    <option value="STRUK">Struk / Receipt</option>
                                    <option value="FOTO">Foto Bukti</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="file" name="lampiran_0[]"
                                    class="form-control form-control-sm file-input"
                                    accept="image/*,.pdf"
                                    {{ $r===0?'required':'' }}>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal_lampiran_0[]"
                                        class="form-control form-control-sm nominal-lampiran-input"
                                        placeholder="0" min="0" step="1">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-lampiran {{ $r===0?'d-none':'' }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="col-12 preview-area"></div>
                        </div>
                    </div>
                    @endfor
                </div>
                {{-- Subtotal per item --}}
                <div class="d-flex justify-content-end mt-2 pe-1">
                    <span class="small text-muted me-2">Subtotal Item:</span>
                    <span class="small fw-bold text-primary subtotal-item">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grand Total --}}
<div class="card border-0 shadow-sm mb-3" style="border-left:4px solid #1a237e !important;">
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
    <option value="">-- Kategori --</option>
    @foreach($kategori as $key => $label)
    <option value="{{ $key }}">{{ $label }}</option>
    @endforeach
`;

let itemCount = 1;

function updateItemNumbers() {
    document.querySelectorAll('.item-reimburse').forEach((el, i) => {
        el.querySelector('.item-nomor').textContent = `Item #${i + 1}`;
        el.querySelector('.btn-hapus-item').classList.toggle('d-none',
            document.querySelectorAll('.item-reimburse').length === 1);
    });
}

function updateSubtotal(itemEl) {
    let sub = 0;
    itemEl.querySelectorAll('.nominal-lampiran-input').forEach(inp => {
        sub += parseFloat(inp.value) || 0;
    });
    itemEl.querySelector('.subtotal-item').textContent = 'Rp ' + sub.toLocaleString('id-ID');
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-item').forEach(el => {
        const val = el.textContent.replace(/[^0-9]/g, '');
        total += parseInt(val) || 0;
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
        img.style.maxHeight = '80px';
        target.appendChild(img);
    } else {
        target.innerHTML = `<span class="badge bg-danger mt-1"><i class="bi bi-file-earmark-pdf me-1"></i>${file.name}</span>`;
    }
}

function bindLampiranEvents(row, itemEl) {
    const hapus = row.querySelector('.btn-hapus-lampiran');
    if (hapus) {
        hapus.addEventListener('click', () => {
            const container = row.closest('.lampiran-container');
            row.remove();
            container.querySelectorAll('.btn-hapus-lampiran').forEach(b => {
                b.classList.toggle('d-none', container.querySelectorAll('.lampiran-row').length === 1);
            });
            // Tampilkan tombol Tambah kalau sudah < 5
            itemEl.querySelector('.btn-tambah-lampiran').classList.remove('d-none');
            updateSubtotal(itemEl);
        });
    }
    const fileInput = row.querySelector('.file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            showPreview(this, row.querySelector('.preview-area'));
        });
    }
    const nominalInput = row.querySelector('.nominal-lampiran-input');
    if (nominalInput) {
        nominalInput.addEventListener('input', () => updateSubtotal(itemEl));
    }
}

function bindItemEvents(itemEl) {
    const idx = itemEl.dataset.index;

    itemEl.querySelector('.btn-hapus-item').addEventListener('click', () => {
        itemEl.remove();
        updateItemNumbers();
        updateGrandTotal();
    });

    itemEl.querySelector('.btn-tambah-lampiran').addEventListener('click', () => {
        const container = itemEl.querySelector('.lampiran-container');
        const rows = container.querySelectorAll('.lampiran-row');
        if (rows.length >= 5) {
            alert('Maksimal 5 baris lampiran per item.');
            return;
        }
        const newRow = document.createElement('div');
        newRow.className = 'lampiran-row border rounded p-2 mb-1';
        newRow.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="kategori_lampiran_${idx}[]" class="form-select form-select-sm">
                    ${kategoriOptions}
                </select>
            </div>
            <div class="col-md-2">
                <select name="jenis_lampiran_${idx}[]" class="form-select form-select-sm">
                    <option value="KWITANSI">Kwitansi</option>
                    <option value="STRUK">Struk / Receipt</option>
                    <option value="FOTO">Foto Bukti</option>
                    <option value="LAINNYA">Lainnya</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="file" name="lampiran_${idx}[]" class="form-control form-control-sm file-input"
                    accept="image/*,.pdf">
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal_lampiran_${idx}[]"
                        class="form-control form-control-sm nominal-lampiran-input"
                        placeholder="0" min="0" step="1">
                </div>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-lampiran">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="col-12 preview-area"></div>
        </div>`;
        container.appendChild(newRow);
        bindLampiranEvents(newRow, itemEl);
        container.querySelectorAll('.btn-hapus-lampiran').forEach(b => b.classList.remove('d-none'));
        if (container.querySelectorAll('.lampiran-row').length >= 5) {
            itemEl.querySelector('.btn-tambah-lampiran').classList.add('d-none');
        }
    });

    itemEl.querySelectorAll('.lampiran-row').forEach(row => bindLampiranEvents(row, itemEl));
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
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                <input type="date" name="items[${idx}][tanggal_pengeluaran]" class="form-control"
                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-7">
                <label class="form-label fw-semibold small">Keterangan / Rincian Biaya <span class="text-danger">*</span></label>
                <input type="text" name="items[${idx}][keterangan]" class="form-control"
                    placeholder="Contoh: Biaya transport perjalanan dinas ke kantor pusat" required>
            </div>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold"><i class="bi bi-paperclip me-1"></i>Lampiran Bukti <span class="text-danger">*</span> <span class="text-muted fw-normal">(maks. 5 baris)</span></span>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-tambah-lampiran">
                    <i class="bi bi-plus me-1"></i>Tambah Baris
                </button>
            </div>
            <div class="alert alert-light border py-2 mb-2 small">
                <i class="bi bi-camera me-1 text-primary"></i>Bisa dari <strong>galeri</strong> atau langsung <strong>ambil foto</strong> kamera HP.
            </div>
            <div class="row g-1 mb-1 d-none d-md-flex px-1">
                <div class="col-md-3"><span class="small fw-semibold text-muted">Kategori Biaya</span></div>
                <div class="col-md-2"><span class="small fw-semibold text-muted">Jenis Dokumen</span></div>
                <div class="col-md-4"><span class="small fw-semibold text-muted">File Bukti</span></div>
                <div class="col-md-2"><span class="small fw-semibold text-muted">Nominal (Rp)</span></div>
                <div class="col-md-1"></div>
            </div>
            <div class="lampiran-container">
                ${[0,1,2].map(r => `
                <div class="lampiran-row border rounded p-2 mb-1">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select name="kategori_lampiran_${idx}[]" class="form-select form-select-sm" ${r===0?'required':''}>
                                ${kategoriOptions}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="jenis_lampiran_${idx}[]" class="form-select form-select-sm">
                                <option value="KWITANSI">Kwitansi</option>
                                <option value="STRUK">Struk / Receipt</option>
                                <option value="FOTO">Foto Bukti</option>
                                <option value="LAINNYA">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="lampiran_${idx}[]" class="form-control form-control-sm file-input"
                                accept="image/*,.pdf" ${r===0?'required':''}>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="nominal_lampiran_${idx}[]"
                                    class="form-control form-control-sm nominal-lampiran-input"
                                    placeholder="0" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-lampiran ${r===0?'d-none':''}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="col-12 preview-area"></div>
                    </div>
                </div>`).join('')}
            </div>
            <div class="d-flex justify-content-end mt-2 pe-1">
                <span class="small text-muted me-2">Subtotal Item:</span>
                <span class="small fw-bold text-primary subtotal-item">Rp 0</span>
            </div>
        </div>
    </div>`;

    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', tmpl);
    const newItem = document.getElementById('itemsContainer').lastElementChild;
    bindItemEvents(newItem);
    updateItemNumbers();
});

// Init
document.querySelectorAll('.item-reimburse').forEach(el => bindItemEvents(el));
updateItemNumbers();
</script>
@endpush
