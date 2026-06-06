@php
    $role = auth()->user()->role;
    // Helper: returns 'show'/'true' if current route matches any of the given patterns
    $open = fn(array $routes) => request()->routeIs($routes) ? 'show' : '';
    $exp  = fn(array $routes) => request()->routeIs($routes) ? 'true' : 'false';
@endphp

{{-- Dashboard semua role --}}
<a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2 me-2"></i>Dashboard
</a>
<a href="{{ route('manual') }}" class="nav-link {{ request()->routeIs('manual') ? 'active' : '' }}">
    <i class="bi bi-book me-2"></i>Panduan Penggunaan
</a>

@if($role === 'ADMIN_CABANG')

    {{-- Pengajuan Jaminan --}}
    @php $sec = ['pengajuan.create-bpkb','pengajuan.create-sertifikat','pengajuan.index','pengajuan.show','pengajuan.edit']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-pengajuan-cabang" aria-expanded="{{ $exp($sec) }}">
        <span>Pengajuan Jaminan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-pengajuan-cabang">
        <a href="{{ route('pengajuan.create-bpkb') }}" class="nav-link {{ request()->routeIs('pengajuan.create-bpkb') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan BPKB
        </a>
        <a href="{{ route('pengajuan.create-sertifikat') }}" class="nav-link {{ request()->routeIs('pengajuan.create-sertifikat') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan Sertifikat
        </a>
        <a href="{{ route('pengajuan.index', ['status'=>'MENUNGGU']) }}" class="nav-link">
            <i class="bi bi-hourglass-split me-2"></i>Menunggu Approval
        </a>
        <a href="{{ route('pengajuan.index', ['status'=>'DIPROSES']) }}" class="nav-link">
            <i class="bi bi-arrow-repeat me-2"></i>Sedang Diproses
        </a>
        <a href="{{ route('pengajuan.index', ['status'=>'DISETUJUI']) }}" class="nav-link">
            <i class="bi bi-check-circle me-2"></i>Disetujui
        </a>
        <a href="{{ route('pengajuan.index', ['status'=>'DITOLAK']) }}" class="nav-link">
            <i class="bi bi-x-circle me-2"></i>Ditolak
        </a>
        <a href="{{ route('pengajuan.index') }}" class="nav-link {{ request()->routeIs('pengajuan.index') && !request('status') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>Semua Pengajuan
        </a>
    </div>

    {{-- Jaminan Kerja --}}
    @php $sec = ['jaminan-kerja.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-jaminan-kerja-cabang" aria-expanded="{{ $exp($sec) }}">
        <span>Jaminan Kerja</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-jaminan-kerja-cabang">
        <a href="{{ route('jaminan-kerja.create') }}" class="nav-link {{ request()->routeIs('jaminan-kerja.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Catat Penyerahan Jaminan
        </a>
        <a href="{{ route('jaminan-kerja.index', ['status'=>'AKTIF']) }}" class="nav-link">
            <i class="bi bi-shield-check me-2"></i>Jaminan Aktif
        </a>
        <a href="{{ route('jaminan-kerja.index', ['status'=>'KEMBALI']) }}" class="nav-link">
            <i class="bi bi-box-arrow-right me-2"></i>Sudah Dikembalikan
        </a>
        <a href="{{ route('jaminan-kerja.index') }}" class="nav-link {{ request()->routeIs('jaminan-kerja.index') && !request('status') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>Semua Jaminan Kerja
        </a>
    </div>

    {{-- Reimburse --}}
    @php $sec = ['reimburse.create','reimburse.index','reimburse.show']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-reimburse-cabang" aria-expanded="{{ $exp($sec) }}">
        <span>Reimburse</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-reimburse-cabang">
        <a href="{{ route('reimburse.create') }}" class="nav-link {{ request()->routeIs('reimburse.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Buat Reimburse
        </a>
        <a href="{{ route('reimburse.index') }}" class="nav-link {{ request()->routeIs('reimburse.index','reimburse.show') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i>Daftar Reimburse
        </a>
    </div>

    {{-- Laporan --}}
    @php $sec = ['laporan.index','laporan.excel','laporan.pdf','laporan.foto','laporan.reimburse.*','laporan.jaminan-kerja.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-laporan-cabang" aria-expanded="{{ $exp($sec) }}">
        <span>Laporan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-laporan-cabang">
        <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index','laporan.excel','laporan.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-2"></i>Laporan Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.index') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.index','laporan.reimburse.excel','laporan.reimburse.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line me-2"></i>Laporan Reimburse
        </a>
        <a href="{{ route('laporan.jaminan-kerja.index') }}" class="nav-link {{ request()->routeIs('laporan.jaminan-kerja.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard me-2"></i>Laporan Ijasah Karyawan
        </a>
        <a href="{{ route('laporan.foto') }}" class="nav-link {{ request()->routeIs('laporan.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.foto') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Reimburse
        </a>
    </div>

@endif

@if($role === 'ADMIN_PUSAT')

    {{-- Stock & QR --}}
    @php $sec = ['stock.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-stock-pusat" aria-expanded="{{ $exp($sec) }}">
        <span>Stock & QR</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-stock-pusat">
        <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <i class="bi bi-archive me-2"></i>Stock List Jaminan
        </a>
    </div>

    {{-- Pemrosesan Jaminan --}}
    @php $sec = ['adminpusat.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-proses-pusat" aria-expanded="{{ $exp($sec) }}">
        <span>Pemrosesan Jaminan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-proses-pusat">
        <a href="{{ route('adminpusat.index') }}?status=MENUNGGU" class="nav-link">
            <i class="bi bi-inbox me-2"></i>Pengajuan Menunggu
        </a>
        <a href="{{ route('adminpusat.index') }}" class="nav-link {{ request()->routeIs('adminpusat.*') ? 'active' : '' }}">
            <i class="bi bi-list-check me-2"></i>Semua Pengajuan
        </a>
    </div>

    {{-- Pemrosesan Reimburse --}}
    @php $sec = ['reimburse.approval.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-approval-pusat" aria-expanded="{{ $exp($sec) }}">
        <span>Pemrosesan Reimburse</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-approval-pusat">
        <a href="{{ route('reimburse.approval.index') }}?status=MENUNGGU" class="nav-link">
            <i class="bi bi-inbox me-2"></i>Reimburse Menunggu
        </a>
        <a href="{{ route('reimburse.approval.index') }}" class="nav-link {{ request()->routeIs('reimburse.approval.*') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i>Semua Reimburse
        </a>
    </div>

    {{-- Laporan --}}
    @php $sec = ['laporan.index','laporan.excel','laporan.pdf','laporan.foto','laporan.reimburse.*','laporan.jaminan-kerja.*','aging.*','kpi.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-laporan-pusat" aria-expanded="{{ $exp($sec) }}">
        <span>Laporan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-laporan-pusat">
        <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index','laporan.excel','laporan.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-2"></i>Laporan Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.index') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.index','laporan.reimburse.excel','laporan.reimburse.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line me-2"></i>Laporan Reimburse
        </a>
        <a href="{{ route('laporan.jaminan-kerja.index') }}" class="nav-link {{ request()->routeIs('laporan.jaminan-kerja.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard me-2"></i>Laporan Ijasah Karyawan
        </a>
        <a href="{{ route('aging.index') }}" class="nav-link {{ request()->routeIs('aging.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history me-2"></i>Aging BPKB
        </a>
        <a href="{{ route('laporan.foto') }}" class="nav-link {{ request()->routeIs('laporan.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.foto') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Reimburse
        </a>
        <a href="{{ route('kpi.index') }}" class="nav-link {{ request()->routeIs('kpi.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow me-2"></i>KPI Per Cabang
        </a>
    </div>

@endif

@if($role === 'SUPER_ADMIN')

    {{-- Pengajuan Jaminan --}}
    @php $sec = ['pengajuan.*','adminpusat.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-pengajuan-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Pengajuan Jaminan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-pengajuan-sa">
        <a href="{{ route('pengajuan.index') }}" class="nav-link {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>Semua Pengajuan
        </a>
        <a href="{{ route('adminpusat.index') }}" class="nav-link {{ request()->routeIs('adminpusat.*') ? 'active' : '' }}">
            <i class="bi bi-gear me-2"></i>Proses Pengajuan
        </a>
    </div>

    {{-- Jaminan Kerja --}}
    @php $sec = ['jaminan-kerja.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-jaminan-kerja-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Jaminan Kerja</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-jaminan-kerja-sa">
        <a href="{{ route('jaminan-kerja.create') }}" class="nav-link {{ request()->routeIs('jaminan-kerja.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Catat Penyerahan Jaminan
        </a>
        <a href="{{ route('jaminan-kerja.index', ['status'=>'AKTIF']) }}" class="nav-link">
            <i class="bi bi-shield-check me-2"></i>Jaminan Aktif
        </a>
        <a href="{{ route('jaminan-kerja.index', ['status'=>'KEMBALI']) }}" class="nav-link">
            <i class="bi bi-box-arrow-right me-2"></i>Sudah Dikembalikan
        </a>
        <a href="{{ route('jaminan-kerja.index') }}" class="nav-link {{ request()->routeIs('jaminan-kerja.index') && !request('status') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>Semua Jaminan Kerja
        </a>
    </div>

    {{-- Pengajuan Reimburse --}}
    @php $sec = ['reimburse.create','reimburse.index','reimburse.show']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-reimburse-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Pengajuan Reimburse</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-reimburse-sa">
        <a href="{{ route('reimburse.create') }}" class="nav-link {{ request()->routeIs('reimburse.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i>Buat Reimburse Baru
        </a>
        <a href="{{ route('reimburse.index') }}" class="nav-link {{ request()->routeIs('reimburse.index','reimburse.show') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>Daftar Reimburse
        </a>
    </div>

    {{-- Reimburse Approval --}}
    @php $sec = ['reimburse.approval.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-approval-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Reimburse Approval</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-approval-sa">
        <a href="{{ route('reimburse.approval.index') }}?status=MENUNGGU" class="nav-link">
            <i class="bi bi-inbox me-2"></i>Reimburse Menunggu
        </a>
        <a href="{{ route('reimburse.approval.index') }}" class="nav-link {{ request()->routeIs('reimburse.approval.*') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i>Semua Reimburse
        </a>
    </div>

    {{-- Laporan --}}
    @php $sec = ['laporan.index','laporan.excel','laporan.pdf','laporan.foto','laporan.reimburse.*','laporan.jaminan-kerja.*','aging.*','audit.*','kpi.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-laporan-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Laporan</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-laporan-sa">
        <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index','laporan.excel','laporan.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-2"></i>Laporan Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.index') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.index','laporan.reimburse.excel','laporan.reimburse.pdf') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line me-2"></i>Laporan Reimburse
        </a>
        <a href="{{ route('laporan.jaminan-kerja.index') }}" class="nav-link {{ request()->routeIs('laporan.jaminan-kerja.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard me-2"></i>Laporan Ijasah Karyawan
        </a>
        <a href="{{ route('aging.index') }}" class="nav-link {{ request()->routeIs('aging.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history me-2"></i>Aging BPKB
        </a>
        <a href="{{ route('laporan.foto') }}" class="nav-link {{ request()->routeIs('laporan.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Jaminan
        </a>
        <a href="{{ route('laporan.reimburse.foto') }}" class="nav-link {{ request()->routeIs('laporan.reimburse.foto') ? 'active' : '' }}">
            <i class="bi bi-images me-2"></i>Galeri Foto Reimburse
        </a>
        <a href="{{ route('kpi.index') }}" class="nav-link {{ request()->routeIs('kpi.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow me-2"></i>KPI Per Cabang
        </a>
        <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check me-2"></i>Audit Log Sistem
        </a>
    </div>

    {{-- Stock & QR --}}
    @php $sec = ['stock.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-stock-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Stock & QR</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-stock-sa">
        <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <i class="bi bi-archive me-2"></i>Stock List Jaminan
        </a>
    </div>

    {{-- Master Data --}}
    @php $sec = ['admin.cabang.*','admin.users.*']; @endphp
    <button class="nav-section-btn" data-bs-toggle="collapse" data-bs-target="#sec-master-sa" aria-expanded="{{ $exp($sec) }}">
        <span>Master Data</span><i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="collapse nav-collapse {{ $open($sec) }}" id="sec-master-sa">
        <a href="{{ route('admin.cabang.index') }}" class="nav-link {{ request()->routeIs('admin.cabang.*') ? 'active' : '' }}">
            <i class="bi bi-building me-2"></i>Kelola Cabang
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i>Kelola Pengguna
        </a>
    </div>

@endif
