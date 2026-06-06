@extends('layouts.app')
@section('title','Kelola Pengguna')
@section('page-title','Kelola Pengguna')

@section('content')
<div class="row g-3">
    {{-- Form Tambah --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Tambah Pengguna</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control form-control-sm @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" required>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control form-control-sm @error('nama_lengkap') is-invalid @enderror"
                            value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select form-select-sm add-role-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="SUPER_ADMIN"  @selected(old('role')=='SUPER_ADMIN')>Super Admin</option>
                            <option value="ADMIN_PUSAT"  @selected(old('role')=='ADMIN_PUSAT')>Admin Pusat</option>
                            <option value="ADMIN_CABANG" @selected(old('role')=='ADMIN_CABANG')>Admin Cabang</option>
                        </select>
                    </div>

                    {{-- Pilih Cabang (multi-checkbox) --}}
                    <div class="mb-2 add-cabang-field" style="display:none">
                        <label class="form-label small fw-semibold">
                            Cabang yang Dapat Diakses <span class="text-danger">*</span>
                        </label>
                        @error('cabang_ids')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                        <div class="border rounded p-2" style="max-height:160px;overflow-y:auto;background:#f8f9fa;">
                            @foreach($cabang as $c)
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox"
                                    name="cabang_ids[]" value="{{ $c->id }}"
                                    id="addCab{{ $c->id }}"
                                    {{ in_array($c->id, old('cabang_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="addCab{{ $c->id }}">
                                    <span class="text-muted" style="font-size:0.7rem;">{{ $c->kode_cabang }}</span>
                                    {{ $c->nama_cabang }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">No. WhatsApp</label>
                        <input type="text" name="no_whatsapp" class="form-control form-control-sm"
                            value="{{ old('no_whatsapp') }}" placeholder="628xxxxxxxxx">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Pengguna --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Daftar Pengguna</h6>
                <form method="GET" class="d-flex gap-2">
                    <select name="role" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        <option value="SUPER_ADMIN"  @selected(request('role')=='SUPER_ADMIN')>Super Admin</option>
                        <option value="ADMIN_PUSAT"  @selected(request('role')=='ADMIN_PUSAT')>Admin Pusat</option>
                        <option value="ADMIN_CABANG" @selected(request('role')=='ADMIN_CABANG')>Admin Cabang</option>
                    </select>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Cabang Akses</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="font-monospace">{{ $u->username }}</td>
                            <td>{{ $u->nama_lengkap }}</td>
                            <td>
                                <span class="badge bg-{{ $u->role === 'SUPER_ADMIN' ? 'dark' : ($u->role === 'ADMIN_PUSAT' ? 'primary' : 'info') }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td>
                                @if($u->cabangs->count())
                                    @foreach($u->cabangs as $c)
                                        <span class="badge bg-secondary me-1 mb-1" style="font-size:0.65rem;">{{ $c->kode_cabang }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $u->aktif ? 'success' : 'danger' }}">
                                    {{ $u->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editUser{{ $u->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($u->id !== auth()->user()->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus pengguna {{ $u->username }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="editUser{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fs-6 fw-semibold">Edit Pengguna: {{ $u->username }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php $assignedIds = $u->cabangs->pluck('id')->toArray(); @endphp
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Username</label>
                                                    <input type="text" name="username" class="form-control form-control-sm"
                                                        value="{{ $u->username }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                                                    <input type="text" name="nama_lengkap" class="form-control form-control-sm"
                                                        value="{{ $u->nama_lengkap }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Role</label>
                                                    <select name="role" class="form-select form-select-sm edit-role-select"
                                                        data-target="editCabang{{ $u->id }}" required>
                                                        <option value="SUPER_ADMIN"  @selected($u->role=='SUPER_ADMIN')>Super Admin</option>
                                                        <option value="ADMIN_PUSAT"  @selected($u->role=='ADMIN_PUSAT')>Admin Pusat</option>
                                                        <option value="ADMIN_CABANG" @selected($u->role=='ADMIN_CABANG')>Admin Cabang</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Status</label>
                                                    <select name="aktif" class="form-select form-select-sm">
                                                        <option value="1" @selected($u->aktif)>Aktif</option>
                                                        <option value="0" @selected(!$u->aktif)>Nonaktif</option>
                                                    </select>
                                                </div>

                                                {{-- Multi-cabang --}}
                                                <div class="col-12" id="editCabang{{ $u->id }}"
                                                    style="{{ in_array($u->role, ['ADMIN_CABANG','ADMIN_PUSAT']) ? '' : 'display:none' }}">
                                                    <label class="form-label small fw-semibold">
                                                        Cabang yang Dapat Diakses <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="border rounded p-2" style="max-height:160px;overflow-y:auto;background:#f8f9fa;">
                                                        <div class="row g-0">
                                                        @foreach($cabang as $c)
                                                        <div class="col-md-6">
                                                            <div class="form-check form-check-sm px-3">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="cabang_ids[]" value="{{ $c->id }}"
                                                                    id="editCab{{ $u->id }}_{{ $c->id }}"
                                                                    {{ in_array($c->id, $assignedIds) ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="editCab{{ $u->id }}_{{ $c->id }}">
                                                                    <span class="text-muted" style="font-size:0.68rem;">{{ $c->kode_cabang }}</span>
                                                                    {{ $c->nama_cabang }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold">Email</label>
                                                    <input type="email" name="email" class="form-control form-control-sm"
                                                        value="{{ $u->email }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">No. WhatsApp</label>
                                                    <input type="text" name="no_whatsapp" class="form-control form-control-sm"
                                                        value="{{ $u->no_whatsapp }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Password Baru</label>
                                                    <input type="password" name="password" class="form-control form-control-sm"
                                                        placeholder="Kosongkan jika tidak diubah">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Konfirmasi</label>
                                                    <input type="password" name="password_confirmation" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="bi bi-check-circle me-1"></i>Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $users->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Form tambah — tampilkan/sembunyikan cabang berdasarkan role
document.querySelectorAll('.add-role-select').forEach(sel => {
    const field = document.querySelector('.add-cabang-field');
    const toggle = () => {
        field.style.display = ['ADMIN_CABANG','ADMIN_PUSAT'].includes(sel.value) ? 'block' : 'none';
    };
    sel.addEventListener('change', toggle);
    toggle();
});

// Form edit — tampilkan/sembunyikan cabang berdasarkan role
document.querySelectorAll('.edit-role-select').forEach(sel => {
    const targetId = sel.dataset.target;
    const field = document.getElementById(targetId);
    const toggle = () => {
        field.style.display = ['ADMIN_CABANG','ADMIN_PUSAT'].includes(sel.value) ? 'block' : 'none';
    };
    sel.addEventListener('change', toggle);
    toggle();
});
</script>
@endpush
