<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Group Mega') — Sistem Pengajuan Jaminan & Reimburse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-size: 0.9rem; }

        /* ── Sidebar ── */
        .sidebar { width: 240px; height: 100vh; background: #1a237e; position: fixed; top: 0; left: 0; z-index: 200; display: flex; flex-direction: column; transition: transform 0.25s ease; }
        .sidebar-brand { padding: 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 0.5rem 1rem; border-radius: 6px; margin: 1px 8px; font-size: 0.85rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: #fff; }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .nav-section-btn {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.5); font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            padding: 0.7rem 1rem 0.2rem; transition: color 0.15s;
        }
        .sidebar .nav-section-btn:hover { color: rgba(255,255,255,0.8); }
        .sidebar .nav-section-btn .chevron { font-size: 0.65rem; transition: transform 0.2s; }
        .sidebar .nav-section-btn[aria-expanded="true"] .chevron { transform: rotate(180deg); }
        .sidebar .nav-section-btn[aria-expanded="true"] { color: rgba(255,255,255,0.85); }
        .sidebar .nav-collapse { padding-bottom: 4px; }
        .sidebar-footer { padding: 0.75rem 1rem; border-top: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }

        /* ── Main content ── */
        .main-content { margin-left: 240px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0.6rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; position: sticky; top: 0; z-index: 99; }
        .topbar-title { font-weight: 600; font-size: 0.95rem; flex: 1; }
        .page-content { padding: 1.25rem; }

        /* ── Status badges ── */
        .badge-MENUNGGU  { background: #fff3cd; color: #856404; }
        .badge-DIPROSES  { background: #cfe2ff; color: #0a58ca; }
        .badge-DISETUJUI { background: #d1e7dd; color: #0a3622; }
        .badge-DITOLAK   { background: #f8d7da; color: #58151c; }
        .role-badge { font-size: 0.68rem; padding: 2px 8px; border-radius: 20px; background: rgba(255,255,255,0.2); color: #fff; }

        /* ── Notif bell ── */
        .notif-btn { position: relative; }
        .notif-badge { position: absolute; top: -4px; right: -4px; background: #dc3545; color: #fff; border-radius: 50%; font-size: 0.6rem; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        /* ── Mobile overlay ── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 199; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; }
            .hamburger { display: flex !important; }
            .topbar-title { font-size: 0.85rem; }
        }
        .hamburger { display: none; align-items: center; justify-content: center; border: none; background: none; padding: 4px 8px; font-size: 1.3rem; color: #1a237e; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Mobile Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-brand text-center">
        <div style="font-size:1rem;font-weight:700;color:#fff;letter-spacing:0.03em;margin-bottom:0.35rem;">
            <i class="bi bi-building me-1"></i>GROUP MEGA
        </div>
        <div style="font-size:0.78rem;color:rgba(255,255,255,0.75);line-height:1.4;font-weight:500;">
            Sistem Pengajuan Jaminan<br>& Reimburse
        </div>
    </div>
    <nav class="py-2" style="flex:1 1 0;overflow-y:auto;overflow-x:hidden;min-height:0;">
        @include('layouts.sidebar')
    </nav>
    <div class="sidebar-footer">
        <div class="text-white-50 text-truncate" style="font-size:0.75rem;">{{ auth()->user()->nama_lengkap }}</div>
        <span class="role-badge mt-1 d-inline-block">{{ auth()->user()->role }}</span>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        {{-- Hamburger (mobile) --}}
        <button class="hamburger" id="hamburgerBtn"><i class="bi bi-list"></i></button>

        {{-- Page title --}}
        <span class="topbar-title d-none d-md-block">@yield('page-title', 'Dashboard')</span>

        {{-- Global Search --}}
        <form action="{{ route('search') }}" method="GET" class="d-flex" style="max-width:280px;width:100%;">
            <div class="input-group input-group-sm">
                <input type="text" name="q" class="form-control" placeholder="Cari pengajuan, nasabah..." value="{{ request('q') }}" autocomplete="off">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        {{-- Notifikasi Bell --}}
        @php $unread = \App\Models\Notifikasi::where('user_id', auth()->user()->id)->where('dibaca', false)->count(); @endphp
        <div class="dropdown">
            <button class="btn btn-sm btn-light notif-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-6"></i>
                @if($unread > 0)<span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow" style="width:320px;max-height:380px;overflow-y:auto;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <span class="fw-semibold small">Notifikasi</span>
                    @if($unread > 0)
                    <form method="POST" action="{{ route('notifikasi.baca-semua') }}">@csrf
                        <button class="btn btn-link btn-sm p-0 text-muted" style="font-size:0.75rem;">Tandai semua dibaca</button>
                    </form>
                    @endif
                </div>
                @php $notifs = \App\Models\Notifikasi::where('user_id', auth()->user()->id)->latest()->limit(8)->get(); @endphp
                @forelse($notifs as $n)
                <a href="{{ $n->url ?? '#' }}" class="dropdown-item py-2 px-3 {{ $n->dibaca ? '' : 'bg-light' }}" style="white-space:normal;">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-{{ match($n->tipe) { 'SUCCESS'=>'check-circle-fill text-success', 'DANGER'=>'x-circle-fill text-danger', 'WARNING'=>'exclamation-triangle-fill text-warning', default=>'info-circle-fill text-primary' } }} mt-1 flex-shrink-0"></i>
                        <div>
                            <div class="fw-semibold" style="font-size:0.8rem;">{{ $n->judul }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ Str::limit($n->pesan, 80) }}</div>
                            <div class="text-muted" style="font-size:0.7rem;">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4 small">Tidak ada notifikasi</div>
                @endforelse
                @if($notifs->count() > 0)
                <div class="border-top text-center py-2">
                    <a href="{{ route('notifikasi.index') }}" class="text-decoration-none small">Lihat semua →</a>
                </div>
                @endif
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-light d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-lg-inline small">{{ auth()->user()->nama_lengkap }}</span>
                <i class="bi bi-chevron-down" style="font-size:0.65rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item small" href="{{ route('profile.edit') }}"><i class="bi bi-key me-2"></i>Ganti Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item small text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- Modal Peringatan Logout Otomatis --}}
<div class="modal fade" id="autoLogoutModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4 px-4">
                <i class="bi bi-clock-history text-warning fs-1 mb-3 d-block"></i>
                <h6 class="fw-bold mb-2">Sesi Hampir Berakhir</h6>
                <p class="text-muted small mb-3">
                    Tidak ada aktivitas terdeteksi.<br>
                    Anda akan otomatis logout dalam:
                </p>
                <div class="fs-2 fw-bold text-danger mb-3" id="logoutCountdown">30</div>
                <button type="button" class="btn btn-primary btn-sm px-4" id="stayLoggedIn">
                    <i class="bi bi-hand-index me-1"></i>Tetap Login
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Mobile sidebar toggle
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const hamburger = document.getElementById('hamburgerBtn');
hamburger?.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
});
overlay?.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
});

// ── Auto Logout setelah 3 menit tidak aktif ──────────────────────────────
(function () {
    const IDLE_LIMIT    = 3 * 60 * 1000; // 3 menit (ms)
    const WARN_BEFORE   = 30 * 1000;     // tampilkan peringatan 30 detik sebelum logout
    const LOGOUT_URL    = '{{ route("logout") }}';
    const CSRF          = document.querySelector('meta[name="csrf-token"]').content;

    let idleTimer   = null;
    let warnTimer   = null;
    let countdownInterval = null;
    const modal     = new bootstrap.Modal(document.getElementById('autoLogoutModal'), { backdrop: 'static' });
    const countdown = document.getElementById('logoutCountdown');

    function doLogout() {
        clearAll();
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = LOGOUT_URL;
        const csrf  = document.createElement('input');
        csrf.type   = 'hidden'; csrf.name = '_token'; csrf.value = CSRF;
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    function showWarning() {
        let sisa = 30;
        countdown.textContent = sisa;
        modal.show();
        countdownInterval = setInterval(() => {
            sisa--;
            countdown.textContent = sisa;
            if (sisa <= 0) {
                clearInterval(countdownInterval);
                doLogout();
            }
        }, 1000);
    }

    function resetTimers() {
        clearAll();
        warnTimer = setTimeout(showWarning, IDLE_LIMIT - WARN_BEFORE);
        idleTimer = setTimeout(doLogout,    IDLE_LIMIT);
    }

    function clearAll() {
        clearTimeout(idleTimer);
        clearTimeout(warnTimer);
        clearInterval(countdownInterval);
    }

    // Setiap aktivitas user → reset timer
    const EVENTS = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
    EVENTS.forEach(ev => document.addEventListener(ev, resetTimers, { passive: true }));

    // Tombol "Tetap Login" → tutup modal + reset timer
    document.getElementById('stayLoggedIn').addEventListener('click', () => {
        modal.hide();
        resetTimers();
    });

    // Mulai timer saat halaman dimuat
    resetTimers();
})();
</script>
@stack('scripts')
</body>
</html>
