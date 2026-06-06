@extends('layouts.app')
@section('title','Kelola Cabang')
@section('page-title','Kelola Cabang')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Tambah Cabang Baru</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cabang.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_cabang" class="form-control form-control-sm text-uppercase @error('kode_cabang') is-invalid @enderror"
                            value="{{ old('kode_cabang') }}" placeholder="Contoh: JKT01" maxlength="10" required>
                        @error('kode_cabang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_cabang" class="form-control form-control-sm @error('nama_cabang') is-invalid @enderror"
                            value="{{ old('nama_cabang') }}" placeholder="Nama lengkap cabang" required>
                        @error('nama_cabang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ old('alamat') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Cabang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-semibold">Daftar Cabang</h6>
                <span class="badge bg-secondary">{{ $cabang->total() }} cabang</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama Cabang</th><th>Alamat</th><th>Status</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($cabang as $c)
                        <tr>
                            <td><span class="badge bg-secondary font-monospace">{{ $c->kode_cabang }}</span></td>
                            <td>{{ $c->nama_cabang }}</td>
                            <td class="text-muted">{{ Str::limit($c->alamat, 40) }}</td>
                            <td>
                                <span class="badge bg-{{ $c->aktif ? 'success' : 'danger' }}">
                                    {{ $c->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCabang{{ $c->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.cabang.destroy', $c) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus cabang {{ $c->nama_cabang }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="editCabang{{ $c->id }}" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form method="POST" action="{{ route('admin.cabang.update', $c) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fs-6">Edit Cabang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Kode Cabang</label>
                                            <input type="text" name="kode_cabang" class="form-control form-control-sm text-uppercase" value="{{ $c->kode_cabang }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Nama Cabang</label>
                                            <input type="text" name="nama_cabang" class="form-control form-control-sm" value="{{ $c->nama_cabang }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Alamat</label>
                                            <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ $c->alamat }}</textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Status</label>
                                            <select name="aktif" class="form-select form-select-sm">
                                                <option value="1" @selected($c->aktif)>Aktif</option>
                                                <option value="0" @selected(!$c->aktif)>Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div></div>
                        </div>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada cabang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $cabang->links() }}</div>
        </div>
    </div>
</div>
@endsection
