@extends('layouts.app')
@section('title','Stock List Jaminan')
@section('page-title','Stock List Jaminan di Pusat')

@section('content')

{{-- Summary --}}
<div class="row g-2 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <i class="bi bi-archive text-secondary fs-4"></i>
                <div class="fw-bold fs-4">{{ $summary['total'] }}</div>
                <div class="text-muted small">Total Stock</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center border-top border-3 border-primary">
            <div class="card-body py-2">
                <i class="bi bi-car-front text-primary fs-4"></i>
                <div class="fw-bold fs-4 text-primary">{{ $summary['bpkb'] }}</div>
                <div class="text-muted small">BPKB</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center border-top border-3 border-info">
            <div class="card-body py-2">
                <i class="bi bi-file-earmark-text text-info fs-4"></i>
                <div class="fw-bold fs-4 text-info">{{ $summary['sertif'] }}</div>
                <div class="text-muted small">Sertifikat</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center border-top border-3 border-danger">
            <div class="card-body py-2">
                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                <div class="fw-bold fs-4 text-danger">{{ $summary['overdue'] }}</div>
                <div class="text-muted small">Overdue &gt;1 Hari</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-archive me-2"></i>Daftar Stock Belum Diambil</h6>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('stock.print-qr-batch', request()->query()) }}"
               target="_blank" class="btn btn-dark btn-sm">
                <i class="bi bi-qr-code me-1"></i>Print QR Overdue
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card-body border-bottom bg-light py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cabang</label>
                <select name="cabang_id" class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $c)
                        <option value="{{ $c->id }}" @selected(request('cabang_id')==$c->id)>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="BPKB"      @selected(request('jenis')==='BPKB')>BPKB</option>
                    <option value="SERTIFIKAT" @selected(request('jenis')==='SERTIFIKAT')>Sertifikat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Aging Min (hari)</label>
                <input type="number" name="aging_min" class="form-control form-control-sm"
                    value="{{ request('aging_min') }}" min="0" placeholder="0">
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Jenis</th>
                    <th>Cabang</th>
                    <th>Tgl Disetujui</th>
                    <th>Nasabah</th>
                    <th>No. BPKB / Sertifikat</th>
                    <th>No. Polisi / No. KTP</th>
                    <th>No. Kartu Piutang</th>
                    <th class="text-center">Aging</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock as $p)
                @php
                    $isBpkb = $p->jenis_jaminan === 'BPKB';
                    $d      = $isBpkb ? $p->detailBpkb : $p->detailSertifikat;
                    $aging  = $p->aging_hari;
                    $rowClass = $aging > 30 ? 'table-danger' : ($aging > 7 ? 'table-warning' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="font-monospace fw-semibold">{{ $p->no_pengajuan }}</td>
                    <td>
                        <span class="badge bg-{{ $isBpkb ? 'primary' : 'info' }}">
                            {{ $p->jenis_jaminan }}
                        </span>
                    </td>
                    <td>{{ $p->cabang?->nama_cabang }}</td>
                    <td>{{ $p->tgl_diproses?->format('d/m/Y') }}</td>
                    <td>{{ $d?->nama_nasabah }}</td>
                    <td class="font-monospace">
                        {{ $isBpkb ? $d?->no_bpkb : $d?->no_sertifikat }}
                    </td>
                    <td>
                        {{ $isBpkb ? $d?->no_polisi : $d?->no_ktp }}
                    </td>
                    <td class="font-monospace">{{ $d?->no_kartu_piutang }}</td>
                    <td class="text-center">
                        @php
                            $color = $p->aging_color;
                            $style = match($color) {
                                'success' => 'background:#198754;color:#fff',
                                'warning' => 'background:#ffc107;color:#000',
                                'orange'  => 'background:#fd7e14;color:#fff',
                                'danger'  => 'background:#dc3545;color:#fff',
                                default   => 'background:#212529;color:#fff',
                            };
                        @endphp
                        <span class="badge fw-bold px-2 py-1" style="{{ $style }};font-size:0.8rem;">
                            {{ $aging }} hari
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            {{-- Print QR single --}}
                            <a href="{{ route('stock.print-qr', $p) }}" target="_blank"
                               class="btn btn-sm btn-outline-dark" title="Print QR">
                                <i class="bi bi-qr-code"></i>
                            </a>
                            {{-- Tandai diambil manual --}}
                            <button class="btn btn-sm btn-outline-success" title="Tandai diambil"
                                onclick="bukaModalDiambil('{{ route('stock.tandai', $p) }}','{{ $p->no_pengajuan }}')">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-5">
                        <i class="bi bi-check-circle fs-2 text-success d-block mb-2"></i>
                        Semua jaminan sudah diambil. Stock kosong.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex align-items-center justify-content-between">
        {{ $stock->links() }}
        <small class="text-muted">
            <span class="badge bg-success">0 hari</span>
            <span class="badge bg-warning text-dark ms-1">1–7</span>
            <span class="badge ms-1" style="background:#fd7e14">8–14</span>
            <span class="badge bg-danger ms-1">15–30</span>
            <span class="badge bg-dark ms-1">&gt;30 hari</span>
        </small>
    </div>
</div>
{{-- ── Modal Tandai Diambil (upload foto) ───────────────────────────── --}}
<div class="modal fade" id="modalDiambil" tabindex="-1" aria-labelledby="modalDiambilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDiambil" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title" id="modalDiambilLabel">
                        <i class="bi bi-check2-circle me-2"></i>Tandai Jaminan Diambil
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Pengajuan: <strong id="labelNoPengajuan" class="text-success"></strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-camera me-1"></i>Foto Konsumen Menerima Jaminan
                            <span class="text-danger">*</span>
                        </label>
                        <div class="alert alert-info py-2 small mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Ambil / upload foto konsumen saat menerima dokumen jaminan sebagai bukti pengambilan.
                        </div>
                        <input type="file" name="foto_pengambilan" id="inputFotoDiambil"
                            class="form-control" accept=".jpg,.jpeg,.png" required>
                        <div class="form-text">JPG/PNG, maks. 5MB</div>
                    </div>

                    {{-- Preview foto --}}
                    <div id="previewFotoDiambil" class="text-center"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check2-circle me-1"></i>Konfirmasi Diambil
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bukaModalDiambil(actionUrl, noPengajuan) {
    document.getElementById('formDiambil').action = actionUrl;
    document.getElementById('labelNoPengajuan').textContent = noPengajuan;
    document.getElementById('inputFotoDiambil').value = '';
    document.getElementById('previewFotoDiambil').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalDiambil')).show();
}

document.getElementById('inputFotoDiambil').addEventListener('change', function () {
    const preview = document.getElementById('previewFotoDiambil');
    preview.innerHTML = '';
    const file = this.files[0];
    if (!file) return;
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.className = 'img-thumbnail mt-2';
    img.style.maxHeight = '220px';
    preview.appendChild(img);
});
</script>
@endpush
