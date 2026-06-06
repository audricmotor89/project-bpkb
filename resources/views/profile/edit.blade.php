@extends('layouts.app')
@section('title','Ganti Password')
@section('page-title','Ganti Password')

@section('content')
<div class="row justify-content-center">
<div class="col-md-5">
<div class="card border-0 shadow-sm">
    <div class="card-header py-3" style="background:#1a237e;">
        <h6 class="mb-0 text-white"><i class="bi bi-key me-2"></i>Ganti Password</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info border-0 small mb-3">
            <i class="bi bi-info-circle me-1"></i>
            Login sebagai <strong>{{ $user->nama_lengkap }}</strong> ({{ $user->role }})
        </div>

        @if($errors->any())
        <div class="alert alert-danger small">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Password Lama <span class="text-danger">*</span></label>
                <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" required autocomplete="current-password">
                @error('password_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password_baru" class="form-control @error('password_baru') is-invalid @enderror" required autocomplete="new-password" minlength="6">
                @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Minimal 6 karakter.</div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password_baru_confirmation" class="form-control" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-1"></i>Simpan Password Baru
            </button>
        </form>
    </div>
</div>
</div>
</div>
@endsection
