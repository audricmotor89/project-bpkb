@extends('layouts.app')
@section('title','Notifikasi')
@section('page-title','Semua Notifikasi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-bell me-2"></i>Notifikasi</h6>
        <form method="POST" action="{{ route('notifikasi.baca-semua') }}">@csrf
            <button class="btn btn-sm btn-outline-secondary">Tandai Semua Dibaca</button>
        </form>
    </div>
    <div class="list-group list-group-flush">
        @forelse($notifikasi as $n)
        <a href="{{ $n->url ?? '#' }}" class="list-group-item list-group-item-action py-3 {{ $n->dibaca ? '' : 'bg-light' }}">
            <div class="d-flex gap-3 align-items-start">
                <i class="bi bi-{{ match($n->tipe) { 'SUCCESS'=>'check-circle-fill text-success', 'DANGER'=>'x-circle-fill text-danger', 'WARNING'=>'exclamation-triangle-fill text-warning', default=>'info-circle-fill text-primary' } }} fs-5 mt-1 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold small">{{ $n->judul }}</span>
                        <span class="text-muted" style="font-size:0.75rem;">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-muted small mt-1">{{ $n->pesan }}</div>
                </div>
                @if(!$n->dibaca)
                <span class="badge bg-primary rounded-pill" style="font-size:0.6rem;">Baru</span>
                @endif
            </div>
        </a>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            Tidak ada notifikasi.
        </div>
        @endforelse
    </div>
    @if($notifikasi->hasPages())
    <div class="card-footer bg-white">{{ $notifikasi->links() }}</div>
    @endif
</div>
@endsection
