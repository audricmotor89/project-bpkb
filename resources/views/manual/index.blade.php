@extends('layouts.app')
@section('title','Panduan Penggunaan Sistem')
@section('page-title','Panduan Penggunaan Sistem')

@push('styles')
<style>
    .manual-nav { position: sticky; top: 80px; }
    .manual-nav .nav-link { color: #495057; font-size: 0.82rem; padding: 0.3rem 0.75rem; border-radius: 4px; }
    .manual-nav .nav-link:hover { background: #e9ecef; color: #1a237e; }
    .manual-nav .nav-link.active { background: #e8eaf6; color: #1a237e; font-weight: 600; }
    .manual-nav .nav-link.sub { padding-left: 1.5rem; font-size: 0.78rem; }
    .manual-nav .nav-link.sub2 { padding-left: 2.25rem; font-size: 0.75rem; }
    .section-title { color: #1a237e; border-bottom: 3px solid #1a237e; padding-bottom: 0.5rem; margin-bottom: 1.25rem; }
    .step-box { background: #f8f9fa; border-left: 4px solid #1a237e; border-radius: 0 6px 6px 0; padding: 1rem 1.25rem; margin-bottom: 0.75rem; }
    .step-box.success { border-left-color: #198754; }
    .step-box.warning { border-left-color: #ffc107; }
    .step-box.danger  { border-left-color: #dc3545; }
    .step-box.info    { border-left-color: #0dcaf0; }
    .step-num { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #1a237e; color: #fff; border-radius: 50%; font-size: 0.72rem; font-weight: 700; flex-shrink: 0; }
    .badge-MENUNGGU  { background: #fff3cd; color: #856404; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-DIPROSES  { background: #cfe2ff; color: #0a58ca; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-DISETUJUI { background: #d1e7dd; color: #0a3622; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-DITOLAK   { background: #f8d7da; color: #58151c; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .tip-box  { background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 8px; padding: 0.75rem 1rem; margin: 0.75rem 0; font-size: 0.85rem; }
    .warn-box { background: #fff8e1; border: 1px solid #ffe082; border-radius: 8px; padding: 0.75rem 1rem; margin: 0.75rem 0; font-size: 0.85rem; }
    .info-box { background: #e3f2fd; border: 1px solid #90caf9; border-radius: 8px; padding: 0.75rem 1rem; margin: 0.75rem 0; font-size: 0.85rem; }
    .new-box  { background: #fce4ec; border: 1px solid #f48fb1; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.4rem; }
    .role-header { background: linear-gradient(135deg, #1a237e, #283593); color: white; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
    .new-badge { background: #e91e63; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 1px 5px; border-radius: 3px; vertical-align: middle; }
    @media print {
        .sidebar, .topbar, .manual-nav, .no-print { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .page-content { padding: 0 !important; }
        .manual-body { max-width: 100% !important; }
        h2 { page-break-before: always; }
        h2:first-of-type { page-break-before: avoid; }
    }
</style>
@endpush

@section('content')
<div class="row g-4">

    {{-- Navigasi Kiri --}}
    <div class="col-lg-3 no-print">
        <div class="manual-nav card border-0 shadow-sm p-3" style="max-height:88vh;overflow-y:auto;">
            <div class="fw-bold small text-muted mb-2 text-uppercase" style="letter-spacing:0.05em;">Daftar Isi</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link" href="#pengenalan">Pengenalan Sistem</a>
                <a class="nav-link" href="#login">Login & Keamanan</a>

                <a class="nav-link fw-semibold mt-1" href="#admin-cabang" style="color:#1a237e;">─ ADMIN CABANG ─</a>
                <a class="nav-link sub" href="#ac-dashboard">Dashboard</a>
                <a class="nav-link sub" href="#ac-buat-bpkb">Buat Pengajuan BPKB</a>
                <a class="nav-link sub" href="#ac-buat-sertifikat">Buat Pengajuan Sertifikat</a>
                <a class="nav-link sub" href="#ac-pantau-status">Pantau Status</a>
                <a class="nav-link sub" href="#ac-komentar">Komunikasi / Komentar <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ac-reimburse">Reimburse</a>
                <a class="nav-link sub" href="#ac-jaminan-kerja">Jaminan Kerja <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ac-laporan">Laporan & Galeri</a>

                <a class="nav-link fw-semibold mt-1" href="#admin-pusat" style="color:#1a237e;">─ ADMIN PUSAT ─</a>
                <a class="nav-link sub" href="#ap-dashboard">Dashboard & SLA Alert <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ap-proses">Proses Pengajuan</a>
                <a class="nav-link sub" href="#ap-bulk">Bulk Approval <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ap-wa">Notif WhatsApp <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ap-komentar">Komunikasi / Komentar <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#ap-stock">Stock List & QR</a>
                <a class="nav-link sub" href="#ap-aging">Aging BPKB</a>
                <a class="nav-link sub" href="#ap-reimburse">Approval Reimburse</a>
                <a class="nav-link sub" href="#ap-laporan">Laporan</a>
                <a class="nav-link sub" href="#ap-kpi">KPI Per Cabang <span class="new-badge">BARU</span></a>

                <a class="nav-link fw-semibold mt-1" href="#super-admin" style="color:#1a237e;">─ SUPER ADMIN ─</a>
                <a class="nav-link sub" href="#sa-audit">Audit Log Sistem <span class="new-badge">BARU</span></a>
                <a class="nav-link sub" href="#sa-master">Kelola Cabang & Pengguna</a>
                <a class="nav-link sub" href="#sa-edit">Edit & Hapus Data <span class="new-badge">BARU</span></a>

                <a class="nav-link fw-semibold mt-1" href="#fitur-umum" style="color:#4a148c;">─ FITUR UMUM ─</a>
                <a class="nav-link sub" href="#notifikasi">Notifikasi</a>
                <a class="nav-link sub" href="#pencarian">Pencarian Global</a>
                <a class="nav-link sub" href="#ganti-password">Ganti Password</a>

                <a class="nav-link mt-1" href="#status-alur">Alur Status Pengajuan</a>
                <a class="nav-link" href="#faq">FAQ & Troubleshooting</a>
            </nav>
            <hr>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-printer me-1"></i>Cetak Panduan
            </button>
        </div>
    </div>

    {{-- Konten Utama --}}
    <div class="col-lg-9 manual-body">

        {{-- Header --}}
        <div class="role-header mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-book-half fs-1"></i>
                <div>
                    <h4 class="mb-1 fw-bold">Panduan Penggunaan Sistem</h4>
                    <div style="opacity:0.85;">GROUP MEGA — Sistem Pengajuan Jaminan & Reimburse</div>
                    <div class="mt-1" style="font-size:0.78rem;opacity:0.7;">Versi 3.0 · {{ now()->format('F Y') }} · Termasuk fitur terbaru</div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════ --}}
        {{-- PENGENALAN --}}
        {{-- ══════════════════════════════════════════ --}}
        <section id="pengenalan" class="mb-5">
            <h2 class="section-title fs-4">📋 Pengenalan Sistem</h2>
            <p>Sistem ini digunakan untuk mengelola proses pengajuan pengambilan <strong>Jaminan BPKB dan Sertifikat</strong> dari cabang ke kantor pusat, pengajuan <strong>Reimburse</strong> biaya operasional, dan pencatatan <strong>Jaminan Kerja</strong> karyawan.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#e8eaf6;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-building fs-2 text-primary mb-2 d-block"></i>
                            <div class="fw-bold">Admin Cabang</div>
                            <div class="text-muted small mt-1">Membuat & memantau pengajuan, berkomunikasi dengan pusat</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#e8f5e9;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-person-check fs-2 text-success mb-2 d-block"></i>
                            <div class="fw-bold">Admin Pusat</div>
                            <div class="text-muted small mt-1">Memproses, menyetujui/menolak pengajuan, monitoring SLA & KPI</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#fce4ec;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-shield-check fs-2 text-danger mb-2 d-block"></i>
                            <div class="fw-bold">Super Admin</div>
                            <div class="text-muted small mt-1">Akses penuh: semua fitur + kelola cabang, pengguna, audit log</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                <strong>Jenis Jaminan yang Dikelola:</strong> BPKB (Bukti Pemilikan Kendaraan Bermotor) dan Sertifikat (tanah/bangunan).
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- LOGIN --}}
        {{-- ══════════════════════════════════════════ --}}
        <section id="login" class="mb-5">
            <h2 class="section-title fs-4">🔐 Login & Keamanan Akun</h2>

            <h6 class="fw-bold mb-3">Cara Login:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka browser dan akses alamat sistem (contoh: <code>http://localhost:8000</code>)</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Masukkan <strong>Username</strong> dan <strong>Password</strong> yang diberikan oleh Super Admin</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Klik tombol <strong>Masuk</strong> — sistem akan mengarahkan ke Dashboard sesuai role Anda</div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-shield-exclamation me-2"></i>
                <strong>Batas Percobaan Login — 3 Kali! <span class="new-badge">BARU</span></strong><br>
                Sistem membatasi login hanya <strong>3 kali percobaan</strong> untuk setiap username. Jika gagal 3 kali berturut-turut,
                akun akan <strong>dikunci otomatis selama 5 menit</strong>. Pesan error akan menampilkan sisa percobaan yang tersedia.<br>
                <span class="text-muted small">Setelah 5 menit, Anda bisa mencoba login kembali. Jika masih tidak bisa, hubungi Super Admin.</span>
            </div>

            <h6 class="fw-bold mb-3 mt-3">Cara Logout:</h6>
            <div class="step-box d-flex gap-3 align-items-start">
                <span class="step-num mt-1">1</span>
                <div>Klik ikon nama pengguna di pojok kanan atas topbar → pilih <strong>Logout</strong></div>
            </div>

            <div class="warn-box">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                <strong>Penting:</strong> Jangan berbagi password dengan orang lain. Segera ganti password jika merasa akun tidak aman (lihat bagian <a href="#ganti-password">Ganti Password</a>).
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- ADMIN CABANG --}}
        {{-- ══════════════════════════════════════════ --}}
        <div class="role-header mb-4" id="admin-cabang">
            <i class="bi bi-building me-2 fs-5"></i>
            <strong class="fs-5">PANDUAN ADMIN CABANG</strong>
        </div>

        {{-- Dashboard Cabang --}}
        <section id="ac-dashboard" class="mb-5">
            <h2 class="section-title fs-4">📊 Dashboard Admin Cabang</h2>
            <p>Setelah login, Anda akan melihat Dashboard yang menampilkan ringkasan pengajuan dari cabang Anda.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Ringkasan Kartu:</div>
                            <ul class="small mb-0">
                                <li><strong>Total Pengajuan</strong> — seluruh pengajuan dari cabang Anda</li>
                                <li><strong>Menunggu</strong> — belum diproses admin pusat</li>
                                <li><strong>Disetujui</strong> — sudah approved</li>
                                <li><strong>Ditolak</strong> — pengajuan ditolak</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Grafik Dashboard:</div>
                            <ul class="small mb-0">
                                <li><strong>Tren 6 Bulan</strong> — grafik pengajuan per bulan</li>
                                <li><strong>Distribusi Status</strong> — diagram proporsi status pengajuan</li>
                                <li><strong>Pengajuan Terbaru</strong> — 5 pengajuan terakhir + shortcut buat baru</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Buat BPKB --}}
        <section id="ac-buat-bpkb" class="mb-5">
            <h2 class="section-title fs-4">🚗 Membuat Pengajuan BPKB</h2>
            <p>Digunakan saat nasabah ingin mengambil kembali BPKB kendaraan yang dijaminkan.</p>

            <h6 class="fw-bold mb-3">Langkah-langkah:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Di menu kiri, klik <strong>Pengajuan Jaminan</strong> → <strong>Buat Pengajuan BPKB</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>
                        <strong>Isi Data Pengajuan:</strong>
                        <ul class="mt-1 mb-0 small">
                            <li><strong>Asal Cabang</strong> — pilih cabang Anda</li>
                            <li><strong>No. Kartu Piutang</strong> — nomor kartu piutang nasabah</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>
                        <strong>Isi Data Nasabah:</strong>
                        <ul class="mt-1 mb-0 small">
                            <li><strong>Nama Nasabah</strong> — sesuai KTP</li>
                            <li><strong>No. KTP</strong> — 16 digit</li>
                            <li><strong>Total Pinjaman</strong> — dalam Rupiah</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">4</span>
                    <div>
                        <strong>Isi Data Kendaraan & BPKB:</strong>
                        <ul class="mt-1 mb-0 small">
                            <li><strong>No. Polisi</strong> — contoh: B 1234 ABC</li>
                            <li><strong>Merek Motor</strong> — contoh: Honda</li>
                            <li><strong>Tipe Motor</strong> — contoh: Vario 150</li>
                            <li><strong>No. BPKB</strong> — nomor dokumen BPKB</li>
                            <li><strong>No. Mesin</strong> dan <strong>No. Rangka</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">5</span>
                    <div>
                        <strong>Upload Lampiran</strong> — klik area upload, pilih file foto/PDF:
                        <ul class="mt-1 mb-0 small">
                            <li>Format: JPG, PNG, atau PDF</li>
                            <li>Maksimal 5 MB per file</li>
                            <li>Bisa upload lebih dari 1 file</li>
                            <li>Contoh: foto KTP, surat kuasa, dll.</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">6</span>
                    <div>Klik <strong>Kirim Pengajuan</strong> → konfirmasi → pengajuan berhasil dibuat dengan status <span class="badge-MENUNGGU">MENUNGGU</span></div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Nomor pengajuan dibuat otomatis (contoh: <code>BPKB/2026/05/0001</code>). Simpan nomor ini untuk referensi.
            </div>
        </section>

        {{-- Buat Sertifikat --}}
        <section id="ac-buat-sertifikat" class="mb-5">
            <h2 class="section-title fs-4">📜 Membuat Pengajuan Sertifikat</h2>
            <p>Digunakan saat nasabah ingin mengambil kembali Sertifikat tanah/bangunan yang dijaminkan.</p>
            <p>Caranya hampir sama dengan Pengajuan BPKB, bedanya pada bagian <strong>Data Jaminan</strong>:</p>
            <div class="step-box">
                <ul class="mb-0 small">
                    <li>Tidak ada data kendaraan</li>
                    <li>Hanya perlu mengisi <strong>No. Sertifikat</strong></li>
                    <li>Semua field lainnya (nasabah, KTP, pinjaman, lampiran) sama seperti BPKB</li>
                </ul>
            </div>
            <div class="mt-2 small text-muted">Menu: <strong>Pengajuan Jaminan → Buat Pengajuan Sertifikat</strong></div>
        </section>

        {{-- Pantau Status --}}
        <section id="ac-pantau-status" class="mb-5">
            <h2 class="section-title fs-4">🔍 Memantau Status Pengajuan</h2>

            <h6 class="fw-bold mb-3">Cara melihat daftar pengajuan:</h6>
            <div class="step-box d-flex gap-3 align-items-start mb-2">
                <span class="step-num mt-1">1</span>
                <div>Di menu kiri klik <strong>Pengajuan Jaminan</strong> → pilih salah satu:
                    <ul class="mt-1 mb-0 small">
                        <li><strong>Menunggu Approval</strong> — pengajuan belum diproses</li>
                        <li><strong>Sedang Diproses</strong> — sedang ditangani pusat</li>
                        <li><strong>Disetujui</strong> — pengajuan diterima</li>
                        <li><strong>Ditolak</strong> — lihat alasan di detail</li>
                        <li><strong>Semua Pengajuan</strong> — tampilkan semua</li>
                    </ul>
                </div>
            </div>

            <h6 class="fw-bold mb-2 mt-3">Arti Status:</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 border rounded p-2">
                        <span class="badge-MENUNGGU">MENUNGGU</span>
                        <span class="small">Pengajuan sudah dikirim, menunggu diproses admin pusat</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 border rounded p-2">
                        <span class="badge-DIPROSES">DIPROSES</span>
                        <span class="small">Sedang diperiksa oleh admin pusat</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 border rounded p-2">
                        <span class="badge-DISETUJUI">DISETUJUI</span>
                        <span class="small">Disetujui, jaminan siap diambil di pusat</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 border rounded p-2">
                        <span class="badge-DITOLAK">DITOLAK</span>
                        <span class="small">Ditolak — buka detail untuk melihat alasan</span>
                    </div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-bell-fill text-success me-2"></i>
                <strong>Notifikasi Otomatis:</strong> Saat status berubah, Anda mendapat notifikasi di ikon lonceng <i class="bi bi-bell"></i> di pojok kanan atas secara otomatis.
            </div>
        </section>

        {{-- Komentar Cabang --}}
        <section id="ac-komentar" class="mb-5">
            <h2 class="section-title fs-4">💬 Komunikasi / Komentar pada Pengajuan <span class="new-badge">BARU</span></h2>
            <p>Fitur ini memungkinkan Admin Cabang berkomunikasi langsung dengan Admin Pusat terkait pengajuan tertentu — tanpa perlu telepon atau chat di luar sistem.</p>

            <h6 class="fw-bold mb-3">Cara mengirim komentar:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka detail pengajuan yang ingin dikomunikasikan (klik nomor pengajuan di daftar)</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Di panel kanan bawah, temukan kotak <strong>"Komunikasi"</strong> dengan ikon <i class="bi bi-chat-dots text-info"></i></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Ketik pesan, pertanyaan, atau keterangan tambahan di kolom teks</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik ikon kirim <i class="bi bi-send"></i> → pesan terkirim, Admin Pusat mendapat notifikasi otomatis</div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Semua komentar tersimpan permanen di dalam pengajuan dan bisa dilihat oleh Admin Pusat maupun Super Admin.
                Anda juga bisa menghapus komentar Anda sendiri jika perlu.
            </div>
        </section>

        {{-- Reimburse Cabang --}}
        <section id="ac-reimburse" class="mb-5">
            <h2 class="section-title fs-4">💰 Pengajuan Reimburse</h2>
            <p>Digunakan untuk mengajukan penggantian biaya operasional (transport, makan, akomodasi, dll.).</p>

            <h6 class="fw-bold mb-3">Cara membuat reimburse:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Menu kiri → <strong>Pengajuan Reimburse</strong> → <strong>Buat Reimburse</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>
                        Isi formulir:
                        <ul class="mt-1 mb-0 small">
                            <li><strong>Cabang</strong> — pilih cabang Anda</li>
                            <li><strong>Nama Pemohon</strong> — nama yang mengajukan</li>
                            <li><strong>Jabatan</strong> — jabatan pemohon</li>
                            <li><strong>Tanggal Pengeluaran</strong> — tanggal biaya dikeluarkan</li>
                            <li><strong>Kategori</strong> — Transport / Makan / Akomodasi / Operasional / Lainnya</li>
                            <li><strong>Keterangan</strong> — penjelasan singkat biaya</li>
                            <li><strong>Nominal Diajukan</strong> — jumlah yang diminta (Rp)</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Upload <strong>bukti pengeluaran</strong> (struk, nota, foto) — format JPG/PNG/PDF, max 5MB</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik <strong>Kirim Reimburse</strong> → pengajuan terkirim ke Admin Pusat untuk disetujui</div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Status reimburse bisa dipantau di menu <strong>Pengajuan Reimburse → Daftar Reimburse</strong>.
            </div>
        </section>

        {{-- Jaminan Kerja Cabang --}}
        <section id="ac-jaminan-kerja" class="mb-5">
            <h2 class="section-title fs-4">🪪 Jaminan Kerja Karyawan <span class="new-badge">BARU</span></h2>
            <p>Modul ini digunakan untuk mencatat dokumen jaminan yang diserahkan karyawan saat masuk kerja (Akte Kelahiran, BPKB, atau Ijasah), serta mencatat pengembaliannya saat karyawan keluar.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#e3f2fd;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-file-earmark-person fs-2 text-info mb-2 d-block"></i>
                            <div class="fw-bold small">Akte Kelahiran</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#e8eaf6;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-car-front fs-2 text-primary mb-2 d-block"></i>
                            <div class="fw-bold small">BPKB</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100" style="background:#e8f5e9;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-mortarboard fs-2 text-success mb-2 d-block"></i>
                            <div class="fw-bold small">Ijasah</div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">A. Mencatat Penyerahan Jaminan (saat karyawan masuk kerja):</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Menu kiri → <strong>Jaminan Kerja</strong> → <strong>Catat Penyerahan Baru</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>
                        Isi <strong>Data Karyawan</strong>:
                        <ul class="mt-1 mb-0 small">
                            <li><strong>Asal Cabang</strong> — pilih cabang</li>
                            <li><strong>Nama Karyawan</strong> — sesuai identitas</li>
                            <li><strong>No. KTP</strong> — 16 digit</li>
                            <li><strong>Jabatan</strong> dan <strong>No. HP</strong></li>
                            <li><strong>Tanggal Masuk Kerja</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>
                        Centang <strong>Jenis Jaminan</strong> yang diserahkan (boleh lebih dari satu):
                        <ul class="mt-1 mb-0 small">
                            <li>Setelah centang, area upload file muncul untuk jenis tersebut</li>
                            <li>Upload file scan/foto dokumen (JPG/PNG/PDF, maks. 5MB)</li>
                            <li>Bisa upload lebih dari 1 file per jenis</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">4</span>
                    <div>
                        Upload <strong>Foto Penerimaan Jaminan</strong> — wajib minimal 1 foto sebagai bukti:
                        <ul class="mt-1 mb-0 small">
                            <li>Klik <strong>Pilih File</strong> untuk upload dari komputer</li>
                            <li>Klik <strong>Ambil Foto</strong> untuk mengambil foto langsung dari kamera HP</li>
                            <li>Bisa upload lebih dari 1 foto sekaligus</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">5</span>
                    <div>Klik <strong>Simpan Penyerahan Jaminan</strong> → data tersimpan dengan status <span style="background:#d1e7dd;color:#0a3622;padding:2px 8px;border-radius:4px;font-size:0.8rem;font-weight:600;">AKTIF</span></div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">B. Mencatat Pengembalian Jaminan (saat karyawan keluar):</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka detail jaminan karyawan yang bersangkutan (dari menu Daftar Jaminan Kerja)</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Klik tombol kuning <strong>Kembalikan Jaminan</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Upload <strong>Foto Bukti Pengembalian</strong> (wajib) + isi catatan opsional</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik <strong>Konfirmasi Pengembalian</strong> → status berubah menjadi <span style="background:#fff3cd;color:#856404;padding:2px 8px;border-radius:4px;font-size:0.8rem;font-weight:600;">KEMBALI</span></div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">C. Laporan Ijasah Karyawan:</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="step-box">
                        <div class="fw-semibold small mb-1">Ijasah Masih Tersimpan (Aktif)</div>
                        <div class="text-muted small">Karyawan masih bekerja, ijasah masih di kantor. Ditampilkan di bagian A.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-box">
                        <div class="fw-semibold small mb-1">Ijasah Sudah Dikembalikan</div>
                        <div class="text-muted small">Karyawan sudah keluar, ijasah sudah diserahkan kembali. Ditampilkan di bagian B.</div>
                    </div>
                </div>
            </div>
            <div class="small text-muted">
                Menu: <strong>Laporan → Laporan Ijasah Karyawan</strong> — tersedia export PDF lengkap dengan foto bukti penerimaan dan pengembalian.
            </div>

            <div class="tip-box mt-3">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                Filter di halaman Daftar Jaminan Kerja: cari berdasarkan nama karyawan / No. KTP, jenis jaminan (Akte/BPKB/Ijasah), dan status (Aktif / Sudah Kembali).
            </div>
        </section>

        {{-- Laporan Cabang --}}
        <section id="ac-laporan" class="mb-5">
            <h2 class="section-title fs-4">📈 Laporan & Galeri Foto</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-bar-chart me-1 text-primary"></i>Laporan Jaminan</div>
                            <p class="small text-muted mb-2">Rekap seluruh pengajuan jaminan dari cabang Anda.</p>
                            <ul class="small mb-2">
                                <li>Filter: jenis, status, tanggal</li>
                                <li>Export ke Excel atau PDF</li>
                            </ul>
                            <div class="small text-muted">Menu: <strong>Laporan → Laporan Jaminan</strong></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-bar-chart-line me-1 text-success"></i>Laporan Reimburse</div>
                            <p class="small text-muted mb-2">Rekap pengajuan reimburse beserta nominal diajukan dan disetujui.</p>
                            <ul class="small mb-2">
                                <li>Filter: kategori, status, tanggal</li>
                                <li>Export ke Excel atau PDF</li>
                            </ul>
                            <div class="small text-muted">Menu: <strong>Laporan → Laporan Reimburse</strong></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-images me-1 text-warning"></i>Galeri Foto Jaminan</div>
                            <p class="small text-muted mb-2">Semua foto lampiran dari pengajuan jaminan.</p>
                            <ul class="small mb-2">
                                <li>Klik foto untuk lihat ukuran penuh</li>
                                <li>Bisa download per file</li>
                            </ul>
                            <div class="small text-muted">Menu: <strong>Laporan → Galeri Foto Jaminan</strong></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-images me-1 text-info"></i>Galeri Foto Reimburse</div>
                            <p class="small text-muted mb-2">Semua foto bukti pengeluaran dari pengajuan reimburse.</p>
                            <ul class="small mb-2">
                                <li>Filter berdasarkan kategori</li>
                                <li>Klik foto untuk lihat ukuran penuh</li>
                            </ul>
                            <div class="small text-muted">Menu: <strong>Laporan → Galeri Foto Reimburse</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- ADMIN PUSAT --}}
        {{-- ══════════════════════════════════════════ --}}
        <div class="role-header mb-4" id="admin-pusat" style="background: linear-gradient(135deg, #1b5e20, #2e7d32);">
            <i class="bi bi-person-check me-2 fs-5"></i>
            <strong class="fs-5">PANDUAN ADMIN PUSAT</strong>
        </div>

        {{-- Dashboard Pusat + SLA --}}
        <section id="ap-dashboard" class="mb-5">
            <h2 class="section-title fs-4">📊 Dashboard Admin Pusat & SLA Alert <span class="new-badge">BARU</span></h2>
            <p>Dashboard Admin Pusat menampilkan overview semua pengajuan dari seluruh cabang, dilengkapi peringatan SLA otomatis.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Kartu Ringkasan (klik untuk filter):</div>
                            <ul class="small mb-0">
                                <li><strong>Menunggu</strong> — perlu segera ditangani</li>
                                <li><strong>Diproses</strong> — sedang diverifikasi</li>
                                <li><strong>Disetujui / Ditolak</strong> — sudah final</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-danger border-3">
                        <div class="card-body">
                            <div class="fw-semibold mb-2 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>SLA Alert Otomatis</div>
                            <p class="small mb-1">Jika ada pengajuan berstatus <span class="badge-MENUNGGU">MENUNGGU</span> lebih dari <strong>3 hari</strong>, banner <span style="background:#f8d7da;padding:2px 6px;border-radius:3px;font-size:0.8rem;">merah</span> akan muncul di bagian atas halaman.</p>
                            <p class="small mb-0">Di tabel, baris overdue ditandai merah dengan badge <span style="background:#dc3545;color:#fff;font-size:0.7rem;padding:1px 5px;border-radius:3px;">SLA</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <strong>Target SLA:</strong> Setiap pengajuan yang masuk harus diproses dalam maksimal <strong>3 hari kerja</strong>.
                Pantau banner SLA setiap kali membuka halaman Pemrosesan Jaminan.
            </div>
        </section>

        {{-- Proses Pengajuan --}}
        <section id="ap-proses" class="mb-5">
            <h2 class="section-title fs-4">✅ Memproses Pengajuan (Satu per Satu)</h2>
            <p>Admin Pusat bertugas memeriksa dan memutuskan status setiap pengajuan dari cabang.</p>

            <h6 class="fw-bold mb-3">Langkah memproses pengajuan:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Menu kiri → <strong>Pemrosesan Jaminan → Pengajuan Menunggu</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Klik ikon mata <i class="bi bi-eye"></i> (<strong>Proses</strong>) untuk membuka detail pengajuan</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>
                        Periksa semua data:
                        <ul class="mt-1 mb-0 small">
                            <li>Data nasabah (nama, KTP, pinjaman)</li>
                            <li>Data kendaraan/sertifikat</li>
                            <li>Lampiran dokumen — klik untuk preview foto</li>
                            <li>Riwayat status di panel kanan</li>
                            <li>Komentar dari cabang (jika ada)</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">4</span>
                    <div>
                        Di panel <strong>"Ubah Status Pengajuan"</strong>, pilih keputusan:
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <span class="badge-DIPROSES">DIPROSES</span> <span class="small">→ Sedang diperiksa / masih ada dokumen kurang</span>
                        </div>
                        <div class="d-flex gap-2 mt-1 flex-wrap">
                            <span class="badge-DISETUJUI">DISETUJUI</span> <span class="small">→ Pengajuan diterima, jaminan siap diambil</span>
                        </div>
                        <div class="d-flex gap-2 mt-1 flex-wrap">
                            <span class="badge-DITOLAK">DITOLAK</span> <span class="small">→ <strong>Wajib isi catatan alasan penolakan</strong></span>
                        </div>
                    </div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">5</span>
                    <div>Klik <strong>Simpan Perubahan Status</strong> → Admin Cabang otomatis mendapat notifikasi</div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                Status <span class="badge-DISETUJUI">DISETUJUI</span> dan <span class="badge-DITOLAK">DITOLAK</span> bersifat <strong>final</strong> — tidak bisa diubah lagi. Pastikan data sudah benar sebelum memutuskan.
            </div>
        </section>

        {{-- Bulk Approval --}}
        <section id="ap-bulk" class="mb-5">
            <h2 class="section-title fs-4">☑️ Bulk Approval — Proses Banyak Pengajuan Sekaligus <span class="new-badge">BARU</span></h2>
            <p>Jika ada banyak pengajuan masuk sekaligus, gunakan fitur Bulk Approval untuk memproses beberapa sekaligus — lebih hemat waktu daripada satu per satu.</p>

            <h6 class="fw-bold mb-3">Cara menggunakan Bulk Approval:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka halaman <strong>Pemrosesan Jaminan → Semua Pengajuan</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>
                        Pilih pengajuan yang ingin diproses:
                        <ul class="mt-1 mb-0 small">
                            <li>Centang <i class="bi bi-check-square"></i> pada kotak di kiri setiap baris</li>
                            <li>Atau centang kotak di header tabel untuk <strong>Pilih Semua</strong> yang tampil</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Toolbar biru akan muncul di bagian atas tabel menampilkan jumlah yang dipilih</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">4</span>
                    <div>
                        Dari toolbar:
                        <ul class="mt-1 mb-0 small">
                            <li>Pilih <strong>Status</strong> yang ingin diterapkan (DIPROSES / DISETUJUI / DITOLAK)</li>
                            <li>Isi <strong>Catatan</strong> — wajib jika status DITOLAK</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">5</span>
                    <div>Klik <strong>Terapkan</strong> → konfirmasi → semua pengajuan yang dipilih berubah status sekaligus, masing-masing pembuat mendapat notifikasi</div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                Pengajuan yang sudah <span class="badge-DISETUJUI">DISETUJUI</span> atau <span class="badge-DITOLAK">DITOLAK</span> (status final) tidak dapat dipilih untuk bulk approval — checkbox-nya tidak tersedia.
            </div>

            <div class="tip-box">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                Gunakan filter <strong>Status = MENUNGGU</strong> terlebih dahulu, lalu "Pilih Semua", untuk memproses semua pengajuan yang menunggu sekaligus.
            </div>
        </section>

        {{-- WhatsApp Notification --}}
        <section id="ap-wa" class="mb-5">
            <h2 class="section-title fs-4">📱 Kirim Notifikasi WhatsApp <span class="new-badge">BARU</span></h2>
            <p>Selain notifikasi dalam sistem, Admin Pusat dapat langsung mengirim pesan WhatsApp ke Admin Cabang setelah memproses pengajuan.</p>

            <div class="step-box info d-flex gap-3 align-items-start mb-3">
                <i class="bi bi-whatsapp text-success fs-4 mt-1"></i>
                <div>
                    Di halaman detail pengajuan (panel kanan), jika Admin Cabang memiliki <strong>nomor WhatsApp terdaftar</strong> di sistem, akan muncul tombol hijau:<br>
                    <div class="mt-2"><strong>Kirim via WA ke [Nama Admin]</strong></div>
                    Klik tombol ini → browser akan membuka WhatsApp Web / aplikasi WA dengan pesan yang sudah terisi otomatis berisi:
                    <ul class="mt-1 mb-0 small">
                        <li>Nama penerima</li>
                        <li>Nomor pengajuan</li>
                        <li>Status terbaru</li>
                        <li>Catatan (jika ada)</li>
                    </ul>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Tombol WA hanya muncul jika nomor WhatsApp Admin Cabang sudah diisi oleh Super Admin di menu <strong>Kelola Pengguna</strong>.
            </div>
        </section>

        {{-- Komentar Pusat --}}
        <section id="ap-komentar" class="mb-5">
            <h2 class="section-title fs-4">💬 Komunikasi / Komentar dengan Cabang <span class="new-badge">BARU</span></h2>
            <p>Admin Pusat dapat berkomunikasi langsung dengan Admin Cabang melalui kolom komentar di setiap pengajuan, tanpa perlu keluar dari sistem.</p>

            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka detail pengajuan → di panel kanan temukan kotak <strong>"Komunikasi"</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Ketik pesan (instruksi, permintaan dokumen tambahan, klarifikasi data) di kolom teks</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">3</span>
                    <div>Klik tombol kirim <i class="bi bi-send"></i> → Admin Cabang mendapat notifikasi otomatis</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body small">
                            <div class="fw-semibold mb-1">Contoh penggunaan:</div>
                            <ul class="mb-0">
                                <li>"Mohon upload ulang foto KTP yang lebih jelas"</li>
                                <li>"No. BPKB tidak sesuai, harap dicek kembali"</li>
                                <li>"Pengajuan ini akan kami setujui besok setelah verifikasi"</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body small">
                            <div class="fw-semibold mb-1">Badge peran di komentar:</div>
                            <ul class="mb-0">
                                <li><span style="background:#0a58ca;color:#fff;font-size:0.65rem;padding:1px 5px;border-radius:3px;">ADMIN_PUSAT</span> — pesan dari Admin Pusat</li>
                                <li><span style="background:#6c757d;color:#fff;font-size:0.65rem;padding:1px 5px;border-radius:3px;">ADMIN_CABANG</span> — pesan dari cabang</li>
                                <li><span style="background:#212529;color:#fff;font-size:0.65rem;padding:1px 5px;border-radius:3px;">SUPER_ADMIN</span> — pesan dari Super Admin</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stock List & QR --}}
        <section id="ap-stock" class="mb-5">
            <h2 class="section-title fs-4">📦 Stock List & QR Code</h2>
            <p>Menampilkan semua jaminan yang sudah <span class="badge-DISETUJUI">DISETUJUI</span> dan <strong>belum diambil</strong> oleh cabang.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">📋 Melihat Stock List</div>
                            <ol class="small mb-0">
                                <li>Menu kiri → <strong>Stock & QR → Stock List Jaminan</strong></li>
                                <li>Filter jenis/cabang sesuai kebutuhan</li>
                                <li>Kolom <strong>Aging</strong> menunjukkan berapa hari sudah di pusat</li>
                                <li>Warna baris merah = overdue lebih dari 30 hari</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">🖨️ Print QR Code</div>
                            <ol class="small mb-0">
                                <li>Klik <strong>Print QR</strong> pada baris yang diinginkan</li>
                                <li>Atau <strong>Print QR Overdue</strong> untuk cetak semua overdue sekaligus</li>
                                <li>Label QR berisi info jaminan dan aging</li>
                                <li>Tempelkan label pada fisik BPKB/Sertifikat</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-2">Konfirmasi Pengambilan via QR Scan:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Cabang datang ke pusat untuk mengambil jaminan</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Scan QR Code pada label menggunakan kamera HP</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Browser HP membuka halaman konfirmasi — periksa data yang tampil</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik <strong>OK — SUDAH DIAMBIL</strong> → sistem mencatat waktu pengambilan</div>
                </div>
            </div>
        </section>

        {{-- Aging BPKB --}}
        <section id="ap-aging" class="mb-5">
            <h2 class="section-title fs-4">⏱️ Laporan Aging BPKB</h2>
            <p>Menampilkan BPKB yang sudah disetujui tetapi <strong>belum diambil</strong> oleh cabang, diurutkan dari yang paling lama.</p>
            <div class="row g-2 mb-3">
                <div class="col-md-3"><div class="text-center border rounded p-2" style="background:#d1e7dd;"><strong class="d-block">0 Hari</strong><small>Baru disetujui</small></div></div>
                <div class="col-md-3"><div class="text-center border rounded p-2" style="background:#fff3cd;"><strong class="d-block">1–7 Hari</strong><small>Perlu perhatian</small></div></div>
                <div class="col-md-3"><div class="text-center border rounded p-2" style="background:#ffe5d0;"><strong class="d-block">8–14 Hari</strong><small>Segera hubungi cabang</small></div></div>
                <div class="col-md-3"><div class="text-center border rounded p-2" style="background:#f8d7da;"><strong class="d-block">&gt;30 Hari</strong><small class="text-danger fw-bold">Kritis!</small></div></div>
            </div>
            <p class="small">Export ke Excel atau PDF untuk dilaporkan ke manajemen.</p>
        </section>

        {{-- Approval Reimburse --}}
        <section id="ap-reimburse" class="mb-5">
            <h2 class="section-title fs-4">💳 Approval Reimburse</h2>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Menu kiri → <strong>Reimburse Approval → Reimburse Menunggu</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Klik nama reimburse → periksa nominal, kategori, dan bukti yang diupload</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>
                        Pilih keputusan:
                        <ul class="mt-1 mb-0 small">
                            <li><strong>Setujui</strong> — isi nominal yang disetujui + catatan opsional</li>
                            <li><strong>Tolak</strong> — wajib isi alasan penolakan</li>
                        </ul>
                    </div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik konfirmasi → cabang mendapat notifikasi otomatis</div>
                </div>
            </div>
        </section>

        {{-- Laporan Pusat --}}
        <section id="ap-laporan" class="mb-5">
            <h2 class="section-title fs-4">📊 Laporan (Admin Pusat)</h2>
            <p>Admin Pusat dapat melihat laporan dari <strong>semua cabang</strong>.</p>
            <div class="row g-2">
                <div class="col-md-6"><div class="step-box"><div class="fw-semibold small">Laporan Jaminan</div><div class="text-muted small">Filter + ringkasan + export Excel/PDF dari semua cabang</div></div></div>
                <div class="col-md-6"><div class="step-box"><div class="fw-semibold small">Laporan Reimburse</div><div class="text-muted small">Total diajukan vs disetujui per cabang</div></div></div>
                <div class="col-md-6"><div class="step-box"><div class="fw-semibold small">Aging BPKB</div><div class="text-muted small">Monitoring BPKB yang belum diambil</div></div></div>
                <div class="col-md-6"><div class="step-box"><div class="fw-semibold small">Galeri Foto Jaminan & Reimburse</div><div class="text-muted small">Semua lampiran foto dari seluruh cabang</div></div></div>
            </div>
        </section>

        {{-- KPI Per Cabang --}}
        <section id="ap-kpi" class="mb-5">
            <h2 class="section-title fs-4">📈 KPI Per Cabang <span class="new-badge">BARU</span></h2>
            <p>Halaman KPI membantu manajemen membandingkan kinerja antar cabang secara visual dan terukur.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-primary border-3">
                        <div class="card-body">
                            <div class="fw-semibold small mb-1">Pengajuan Terbanyak</div>
                            <div class="text-muted small">Cabang dengan volume pengajuan tertinggi dalam periode yang dipilih</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-success border-3">
                        <div class="card-body">
                            <div class="fw-semibold small mb-1">Approval Rate Tertinggi</div>
                            <div class="text-muted small">% pengajuan yang disetujui dari total pengajuan per cabang</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
                        <div class="card-body">
                            <div class="fw-semibold small mb-1">Proses Tercepat</div>
                            <div class="text-muted small">Rata-rata hari dari pengajuan masuk hingga diproses</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-box info mb-3">
                <div class="fw-semibold mb-1">Cara membaca tabel KPI:</div>
                <ul class="small mb-0">
                    <li><strong>Approval Rate</strong> — <span style="background:#198754;color:#fff;font-size:0.7rem;padding:1px 5px;border-radius:3px;">≥80%</span> bagus, <span style="background:#ffc107;color:#000;font-size:0.7rem;padding:1px 5px;border-radius:3px;">50–79%</span> cukup, <span style="background:#dc3545;color:#fff;font-size:0.7rem;padding:1px 5px;border-radius:3px;">&lt;50%</span> perlu perhatian</li>
                    <li><strong>Avg Proses</strong> — <span style="background:#198754;color:#fff;font-size:0.7rem;padding:1px 5px;border-radius:3px;">≤1 hari</span> bagus, <span style="background:#ffc107;color:#000;font-size:0.7rem;padding:1px 5px;border-radius:3px;">≤3 hari</span> normal, <span style="background:#dc3545;color:#fff;font-size:0.7rem;padding:1px 5px;border-radius:3px;">&gt;3 hari</span> lambat</li>
                </ul>
            </div>

            <div class="tip-box">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                Filter berdasarkan <strong>Tahun</strong> atau <strong>Bulan</strong> tertentu untuk analisis periodik. Dilengkapi grafik bar chart trend bulanan dan donut chart top 5 cabang.
            </div>
            <div class="small text-muted">Menu: <strong>Laporan → KPI Per Cabang</strong></div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- SUPER ADMIN --}}
        {{-- ══════════════════════════════════════════ --}}
        <div class="role-header mb-4" id="super-admin" style="background: linear-gradient(135deg, #b71c1c, #c62828);">
            <i class="bi bi-shield-check me-2 fs-5"></i>
            <strong class="fs-5">PANDUAN SUPER ADMIN</strong>
        </div>

        {{-- Audit Log --}}
        <section id="sa-audit" class="mb-5">
            <h2 class="section-title fs-4">🛡️ Audit Log Sistem <span class="new-badge">BARU</span></h2>
            <p>Super Admin dapat melihat <strong>seluruh aktivitas</strong> yang terjadi di dalam sistem — siapa melakukan apa, kapan, dan pada pengajuan mana. Berguna sebagai bukti dan kontrol.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Data yang Tercatat:</div>
                            <ul class="small mb-0">
                                <li><strong>Waktu</strong> — tanggal dan jam (detail hingga detik)</li>
                                <li><strong>Pengguna</strong> — nama + role yang melakukan aksi</li>
                                <li><strong>Aksi</strong> — BUAT_PENGAJUAN, UBAH_STATUS, EDIT_DATA</li>
                                <li><strong>No. Pengajuan</strong> — pengajuan yang terpengaruh</li>
                                <li><strong>Perubahan Status</strong> — dari → ke</li>
                                <li><strong>Keterangan</strong> — catatan tambahan</li>
                                <li><strong>IP Address</strong> — lokasi jaringan pengguna</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Filter tersedia:</div>
                            <ul class="small mb-0">
                                <li>Filter berdasarkan <strong>Pengguna</strong> tertentu</li>
                                <li>Filter berdasarkan <strong>Jenis Aksi</strong></li>
                                <li>Filter berdasarkan <strong>Status Baru</strong></li>
                                <li>Filter berdasarkan <strong>Rentang Tanggal</strong></li>
                                <li>Klik No. Pengajuan untuk langsung ke detail</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                Gunakan audit log untuk investigasi jika ada perbedaan data, atau untuk membuktikan bahwa suatu pengajuan sudah diproses pada waktu tertentu.
            </div>
            <div class="small text-muted">Menu: <strong>Laporan → Audit Log Sistem</strong></div>
        </section>

        {{-- Master Data --}}
        <section id="sa-master" class="mb-5">
            <h2 class="section-title fs-4">🏢 Kelola Cabang & Pengguna</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-building me-1 text-primary"></i>Kelola Cabang</div>
                            <ul class="small mb-0">
                                <li>Tambah cabang baru</li>
                                <li>Edit nama / kode cabang</li>
                                <li>Aktifkan / nonaktifkan cabang</li>
                                <li>Hapus cabang (hanya jika tidak ada data)</li>
                            </ul>
                            <div class="text-muted small mt-2">Menu: <strong>Master Data → Kelola Cabang</strong></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="fw-semibold mb-2"><i class="bi bi-people me-1 text-success"></i>Kelola Pengguna</div>
                            <ul class="small mb-0">
                                <li>Tambah pengguna baru (admin cabang/pusat)</li>
                                <li>Set role dan cabang yang bisa diakses</li>
                                <li>Isi <strong>No. WhatsApp</strong> agar bisa menerima notif WA</li>
                                <li>Reset password pengguna</li>
                                <li>Aktifkan / nonaktifkan akun</li>
                            </ul>
                            <div class="text-muted small mt-2">Menu: <strong>Master Data → Kelola Pengguna</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="warn-box mt-3">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                Isi kolom <strong>No. WhatsApp</strong> pengguna dengan format: <code>08xxxxxxxxxx</code> atau <code>628xxxxxxxxxx</code> agar tombol notifikasi WA berfungsi di halaman detail pengajuan.
            </div>
        </section>

        {{-- Edit & Delete Data --}}
        <section id="sa-edit" class="mb-5">
            <h2 class="section-title fs-4">✏️ Edit & Hapus Data <span class="new-badge">BARU</span></h2>
            <p>Super Admin dapat <strong>mengedit</strong> atau <strong>menghapus</strong> data yang sudah masuk jika terjadi kesalahan input seperti cabang yang salah, nama yang typo, atau data perlu dikoreksi. Fitur ini tersedia untuk tiga modul: <strong>Pengajuan Jaminan</strong>, <strong>Jaminan Kerja</strong>, dan <strong>Reimburse</strong>.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
                        <div class="card-body">
                            <div class="fw-semibold mb-1"><i class="bi bi-file-earmark-text text-warning me-1"></i>Pengajuan Jaminan</div>
                            <ul class="small mb-0">
                                <li>Edit data nasabah & kendaraan</li>
                                <li>Koreksi cabang & No. Kartu Piutang</li>
                                <li>Tambah/hapus lampiran</li>
                                <li><span class="text-danger fw-semibold">Hapus permanen</span> + lampiran</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
                        <div class="card-body">
                            <div class="fw-semibold mb-1"><i class="bi bi-person-badge text-warning me-1"></i>Jaminan Kerja</div>
                            <ul class="small mb-0">
                                <li>Edit data karyawan & cabang</li>
                                <li>Koreksi jenis jaminan</li>
                                <li>Tambah/hapus file dokumen</li>
                                <li><span class="text-danger fw-semibold">Hapus permanen</span> + semua lampiran</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
                        <div class="card-body">
                            <div class="fw-semibold mb-1"><i class="bi bi-receipt text-warning me-1"></i>Reimburse</div>
                            <ul class="small mb-0">
                                <li>Edit cabang, nominal, kategori</li>
                                <li>Koreksi tanggal & keterangan</li>
                                <li>Tambah/hapus lampiran bukti</li>
                                <li><span class="text-danger fw-semibold">Hapus permanen</span> + lampiran</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Cara Edit Data:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka halaman detail data yang ingin diedit</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Klik tombol kuning <strong>Edit Data</strong> di pojok kanan atas</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Ubah data yang perlu diperbaiki. Untuk lampiran: centang <strong>Hapus</strong> pada file yang ingin dihapus, atau upload file baru</div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">4</span>
                    <div>Klik <strong>Simpan Perubahan</strong> — data diperbarui langsung</div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Cara Hapus Data:</h6>
            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Buka halaman detail data yang ingin dihapus</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Klik tombol merah <strong>Hapus</strong> di pojok kanan atas</div>
                </div>
                <div class="step-box danger d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#dc3545;">3</span>
                    <div>Baca peringatan di modal konfirmasi → klik <strong>Ya, Hapus Permanen</strong></div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                <strong>Peringatan:</strong> Tombol Edit dan Hapus hanya tampil untuk <strong>Super Admin</strong>. Penghapusan bersifat <strong>permanen dan tidak dapat dibatalkan</strong> — semua data beserta file lampiran akan terhapus selamanya. Pastikan betul-betul yakin sebelum menghapus.
            </div>

            <div class="info-box">
                <i class="bi bi-info-circle me-2"></i>
                Perubahan data Pengajuan Jaminan akan dicatat di <strong>Audit Log</strong>. Untuk Jaminan Kerja dan Reimburse, perubahan langsung berlaku tanpa log terpisah — gunakan dengan bijak.
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- FITUR UMUM --}}
        {{-- ══════════════════════════════════════════ --}}
        <div class="role-header mb-4" id="fitur-umum" style="background: linear-gradient(135deg, #4a148c, #6a1b9a);">
            <i class="bi bi-stars me-2 fs-5"></i>
            <strong class="fs-5">FITUR UMUM (Semua Pengguna)</strong>
        </div>

        {{-- Notifikasi --}}
        <section id="notifikasi" class="mb-5">
            <h2 class="section-title fs-4">🔔 Notifikasi</h2>
            <p>Sistem mengirim notifikasi otomatis setiap kali ada perubahan status pengajuan atau komentar baru.</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Cara melihat notifikasi:</div>
                            <ol class="small mb-0">
                                <li>Lihat ikon <i class="bi bi-bell"></i> di pojok kanan atas</li>
                                <li>Angka merah = notifikasi belum dibaca</li>
                                <li>Klik ikon untuk melihat daftar terbaru</li>
                                <li>Klik notifikasi untuk langsung ke halaman terkait</li>
                                <li>Klik "Lihat semua →" untuk riwayat lengkap</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Jenis notifikasi:</div>
                            <ul class="small mb-0">
                                <li><span style="color:#198754;">●</span> <strong>Hijau</strong> — Pengajuan Disetujui</li>
                                <li><span style="color:#dc3545;">●</span> <strong>Merah</strong> — Pengajuan Ditolak</li>
                                <li><span style="color:#0d6efd;">●</span> <strong>Biru</strong> — Status berubah / Komentar baru</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pencarian --}}
        <section id="pencarian" class="mb-5">
            <h2 class="section-title fs-4">🔎 Pencarian Global</h2>
            <p>Cari pengajuan atau reimburse dari mana saja tanpa perlu masuk ke menu tertentu.</p>

            <div class="d-flex flex-column gap-2 mb-2">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Ketik kata kunci di kotak pencarian di bagian atas (topbar)</div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Tekan Enter atau klik ikon kaca pembesar <i class="bi bi-search"></i></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">3</span>
                    <div>Hasil pencarian menampilkan pengajuan jaminan dan reimburse yang cocok</div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-lightbulb-fill text-success me-2"></i>
                Kata kunci yang bisa dicari: Nama nasabah, No. Pengajuan, No. BPKB, No. Polisi, atau No. Reimburse
            </div>
        </section>

        {{-- Ganti Password --}}
        <section id="ganti-password" class="mb-5">
            <h2 class="section-title fs-4">🔑 Ganti Password</h2>

            <div class="d-flex flex-column gap-2 mb-3">
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">1</span>
                    <div>Klik nama pengguna di pojok kanan atas → pilih <strong>Ganti Password</strong></div>
                </div>
                <div class="step-box d-flex gap-3 align-items-start">
                    <span class="step-num mt-1">2</span>
                    <div>Isi <strong>Password Lama</strong>, <strong>Password Baru</strong> (min. 6 karakter), dan <strong>Konfirmasi Password Baru</strong></div>
                </div>
                <div class="step-box success d-flex gap-3 align-items-start">
                    <span class="step-num mt-1" style="background:#198754;">3</span>
                    <div>Klik <strong>Simpan Password Baru</strong></div>
                </div>
            </div>

            <div class="warn-box">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                Gunakan password yang kuat: kombinasi huruf besar/kecil, angka, dan simbol. Minimal 8 karakter disarankan.
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- ALUR STATUS --}}
        {{-- ══════════════════════════════════════════ --}}
        <section id="status-alur" class="mb-5">
            <h2 class="section-title fs-4">🔄 Alur Status Pengajuan</h2>
            <p>Berikut alur perubahan status dari awal pengajuan hingga selesai:</p>

            <div class="d-flex align-items-center gap-2 flex-wrap mb-4">
                <div class="text-center">
                    <div class="badge-MENUNGGU d-block mb-1 px-3 py-2">MENUNGGU</div>
                    <div class="text-muted" style="font-size:0.7rem;">Pengajuan dibuat<br>oleh cabang</div>
                </div>
                <i class="bi bi-arrow-right fs-5 text-muted"></i>
                <div class="text-center">
                    <div class="badge-DIPROSES d-block mb-1 px-3 py-2">DIPROSES</div>
                    <div class="text-muted" style="font-size:0.7rem;">Admin pusat<br>sedang memeriksa</div>
                </div>
                <i class="bi bi-arrow-right fs-5 text-muted"></i>
                <div class="d-flex flex-column gap-2">
                    <div class="text-center">
                        <div class="badge-DISETUJUI d-block mb-1 px-3 py-2">DISETUJUI</div>
                        <div class="text-muted" style="font-size:0.7rem;">Jaminan siap diambil</div>
                    </div>
                    <div class="text-center">
                        <div class="badge-DITOLAK d-block mb-1 px-3 py-2">DITOLAK</div>
                        <div class="text-muted" style="font-size:0.7rem;">Ada kesalahan data</div>
                    </div>
                </div>
            </div>

            <div class="tip-box">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Setelah <span class="badge-DISETUJUI">DISETUJUI</span>, cabang mengambil fisik jaminan di kantor pusat. Pengambilan dikonfirmasi dengan scan QR Code atau klik "Tandai Diambil" di Stock List.
            </div>

            <div class="warn-box mt-3">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                <strong>Jika pengajuan DITOLAK:</strong> Lihat catatan alasan di detail pengajuan. Perbaiki data yang salah, lalu buat pengajuan baru. Pengajuan yang ditolak tidak bisa diubah — harus dibuat ulang.
            </div>
        </section>

        {{-- ══════════════════════════════════════════ --}}
        {{-- FAQ --}}
        {{-- ══════════════════════════════════════════ --}}
        <section id="faq" class="mb-5">
            <h2 class="section-title fs-4">❓ FAQ & Troubleshooting</h2>

            <div class="accordion" id="faqAccordion">
                @foreach([
                    ['Akun saya dikunci, apa yang harus dilakukan?', 'Tunggu 5 menit lalu coba login kembali. Sistem akan membuka kunci otomatis setelah 5 menit. Jika sudah 5 menit tapi masih tidak bisa masuk, hubungi Super Admin untuk reset percobaan login.'],
                    ['Pengajuan saya ditolak, bagaimana cara melanjutkan?', 'Buka detail pengajuan yang ditolak → baca catatan alasan penolakan dari Admin Pusat → perbaiki data yang salah → buat pengajuan BARU dengan data yang sudah diperbaiki. Pengajuan yang ditolak tidak bisa diedit langsung.'],
                    ['Saya tidak menerima notifikasi padahal status berubah?', 'Pastikan Anda membuka halaman sistem dan tidak di-logout. Notifikasi tampil di ikon lonceng di pojok kanan atas. Klik ikon lonceng → klik "Lihat semua" untuk melihat seluruh riwayat notifikasi.'],
                    ['Bagaimana cara menggunakan Bulk Approval?', 'Di halaman Pemrosesan Jaminan, filter dengan Status = MENUNGGU. Centang kotak di kiri baris yang ingin diproses (atau "Pilih Semua"). Toolbar biru akan muncul — pilih status, isi catatan jika perlu, klik Terapkan.'],
                    ['Tombol WhatsApp tidak muncul di halaman detail pengajuan?', 'Nomor WhatsApp Admin Cabang belum diisi di sistem. Super Admin perlu masuk ke menu Master Data → Kelola Pengguna → edit pengguna tersebut → isi kolom No. WhatsApp.'],
                    ['Saya tidak bisa upload lampiran, bagaimana?', 'Pastikan file berformat JPG, PNG, atau PDF dan ukurannya tidak melebihi 5 MB. Jika file terlalu besar, kompres dulu menggunakan tools online seperti ilovepdf.com atau tinypng.com.'],
                    ['Bagaimana cara melihat foto lampiran yang diupload cabang?', 'Ada 2 cara: (1) Buka detail pengajuan → klik thumbnail foto di bagian Lampiran Dokumen. (2) Buka menu Laporan → Galeri Foto Jaminan untuk melihat semua foto dari seluruh pengajuan.'],
                    ['Saya lupa password, bagaimana?', 'Hubungi Super Admin sistem Anda. Super Admin dapat mereset password melalui menu Master Data → Kelola Pengguna.'],
                    ['Apa itu Jaminan Kerja dan siapa yang bisa mengaksesnya?', 'Jaminan Kerja adalah modul untuk mencatat dokumen jaminan karyawan (Akte Kelahiran, BPKB, Ijasah) yang diserahkan saat masuk kerja. Fitur ini tersedia untuk Admin Cabang dan Super Admin. Admin Cabang hanya dapat mengakses data dari cabangnya sendiri.'],
                    ['Bagaimana cara upload foto dari HP di formulir Jaminan Kerja?', 'Di setiap field upload foto, terdapat 2 tombol: "Pilih File" untuk upload dari galeri/komputer, dan "Ambil Foto" untuk mengaktifkan kamera HP secara langsung. Tombol Ambil Foto hanya berfungsi jika Anda mengakses sistem dari browser HP.'],
                    ['Salah input cabang pada data yang sudah masuk, bagaimana memperbaikinya?', 'Super Admin dapat memperbaiki data tersebut melalui tombol Edit Data di halaman detail pengajuan/jaminan/reimburse. Buka data yang salah → klik Edit Data → ganti cabang yang benar → klik Simpan Perubahan.'],
                    ['Apakah data yang dihapus bisa dikembalikan?', 'Tidak. Penghapusan data bersifat permanen — semua data beserta lampiran file dihapus selamanya dari sistem. Selalu pastikan yakin sebelum menghapus, dan hanya gunakan fitur ini untuk data yang memang salah diinput.'],
                ] as [$q, $a])
                <div class="accordion-item border-0 shadow-sm mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3 small fw-semibold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq{{ $loop->index }}">
                            {{ $q }}
                        </button>
                    </h2>
                    <div id="faq{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small text-muted">{{ $a }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- Footer --}}
        <div class="alert alert-light border text-center small text-muted mt-4">
            <i class="bi bi-building me-1"></i>
            <strong>GROUP MEGA</strong> — Sistem Pengajuan Jaminan & Reimburse<br>
            Untuk bantuan teknis, hubungi Super Admin sistem Anda.<br>
            <span style="font-size:0.75rem;">Panduan ini mencakup semua fitur per versi 3.0 ({{ now()->format('F Y') }})</span>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Highlight active nav item on scroll
const sections = document.querySelectorAll('section[id], div[id]');
const navLinks  = document.querySelectorAll('.manual-nav .nav-link');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 120) current = s.id;
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) link.classList.add('active');
    });
}, { passive: true });

// Smooth scroll
navLinks.forEach(link => {
    link.addEventListener('click', e => {
        const target = document.querySelector(link.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
</script>
@endpush
