<?php

use App\Http\Controllers\AdminPusatController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AgingBpkbController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\JaminanKerjaController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LaporanJaminanKerjaController;
use App\Http\Controllers\StockListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanReimburseController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReimburseApprovalController;
use App\Http\Controllers\ReimburseController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',    [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search',     [SearchController::class, 'index'])->name('search');
    Route::get('/panduan',    fn() => view('manual.index'))->name('manual');
    Route::get('/profile/edit',     [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/password',[ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifikasi',        [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

    // ── Pengajuan (Admin Cabang & Super Admin) ─────────────────────────────
    Route::middleware('role:ADMIN_CABANG,SUPER_ADMIN')->group(function () {
        Route::get('/pengajuan',                  [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/bpkb/buat',        [PengajuanController::class, 'createBpkb'])->name('pengajuan.create-bpkb');
        Route::post('/pengajuan/bpkb',            [PengajuanController::class, 'storeBpkb'])->name('pengajuan.store-bpkb');
        Route::get('/pengajuan/sertifikat/buat',  [PengajuanController::class, 'createSertifikat'])->name('pengajuan.create-sertifikat');
        Route::post('/pengajuan/sertifikat',      [PengajuanController::class, 'storeSertifikat'])->name('pengajuan.store-sertifikat');
        Route::get('/pengajuan/{pengajuan}',      [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/lampiran/{lampiran}/download',  [PengajuanController::class, 'downloadLampiran'])->name('lampiran.download');
        Route::get('/lampiran/{lampiran}/preview',   [PengajuanController::class, 'previewLampiran'])->name('lampiran.preview');
        Route::get('/pengajuan/{pengajuan}/zip',     [PengajuanController::class, 'downloadZip'])->name('pengajuan.zip');
        Route::get('/pengajuan/{pengajuan}/cetak', [LaporanController::class, 'cetakSurat'])->name('pengajuan.cetak');
    });

    // ── Edit / Delete Pengajuan (Super Admin only) ────────────────────────
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/pengajuan/{pengajuan}/edit',  [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{pengajuan}',       [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::delete('/pengajuan/{pengajuan}',    [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
    });

    // ── Admin Pusat ────────────────────────────────────────────────────────
    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::get('/admin-pusat',                              [AdminPusatController::class, 'index'])->name('adminpusat.index');
        Route::post('/admin-pusat/bulk',                        [AdminPusatController::class, 'bulkUpdateStatus'])->name('adminpusat.bulk');
        Route::get('/admin-pusat/{pengajuan}',                  [AdminPusatController::class, 'show'])->name('adminpusat.show');
        Route::post('/admin-pusat/{pengajuan}/status',          [AdminPusatController::class, 'updateStatus'])->name('adminpusat.update-status');
        Route::get('/admin-pusat/lampiran/{lampiran}/download', [AdminPusatController::class, 'downloadLampiran'])->name('adminpusat.lampiran.download');
    });

    // ── Laporan (semua role, data dibatasi by role di controller) ──────────
    Route::get('/laporan',              [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/excel',        [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/pdf',          [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/foto',         [LaporanController::class, 'fotoIndex'])->name('laporan.foto');

    // ── Reimburse (Admin Cabang buat, Admin Pusat/Super approve) ──────────
    Route::middleware('role:ADMIN_CABANG,SUPER_ADMIN')->group(function () {
        Route::get('/reimburse',             [ReimburseController::class, 'index'])->name('reimburse.index');
        Route::get('/reimburse/buat',        [ReimburseController::class, 'create'])->name('reimburse.create');
        Route::post('/reimburse',            [ReimburseController::class, 'store'])->name('reimburse.store');
        Route::get('/reimburse/{reimburse}', [ReimburseController::class, 'show'])->name('reimburse.show');
        Route::get('/reimburse-lampiran/{lampiran}/download', [ReimburseController::class, 'downloadLampiran'])->name('reimburse.lampiran.download');
        Route::get('/reimburse-lampiran/{lampiran}/preview',  [ReimburseController::class, 'previewLampiran'])->name('reimburse.lampiran.preview');
    });

    // ── Edit / Delete Reimburse (Super Admin only) ────────────────────────
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/reimburse/{reimburse}/edit', [ReimburseController::class, 'edit'])->name('reimburse.edit');
        Route::put('/reimburse/{reimburse}',      [ReimburseController::class, 'update'])->name('reimburse.update');
        Route::delete('/reimburse/{reimburse}',   [ReimburseController::class, 'destroy'])->name('reimburse.destroy');
    });

    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::get('/reimburse-approval',                      [ReimburseApprovalController::class, 'index'])->name('reimburse.approval.index');
        Route::get('/reimburse-approval/{reimburse}',          [ReimburseApprovalController::class, 'show'])->name('reimburse.approval.show');
        Route::post('/reimburse-approval/{reimburse}/approve', [ReimburseApprovalController::class, 'approve'])->name('reimburse.approval.approve');
        Route::post('/reimburse-approval/{reimburse}/reject',  [ReimburseApprovalController::class, 'reject'])->name('reimburse.approval.reject');
        Route::get('/reimburse-approval-lampiran/{lampiran}/download', [ReimburseApprovalController::class, 'downloadLampiran'])->name('reimburse.approval.lampiran.download');
    });

    // ── Laporan Reimburse (semua role, data dibatasi di controller) ────────
    Route::get('/laporan-reimburse',       [LaporanReimburseController::class, 'index'])->name('laporan.reimburse.index');
    Route::get('/laporan-reimburse/excel', [LaporanReimburseController::class, 'exportExcel'])->name('laporan.reimburse.excel');
    Route::get('/laporan-reimburse/pdf',   [LaporanReimburseController::class, 'exportPdf'])->name('laporan.reimburse.pdf');
    Route::get('/laporan-reimburse/foto',  [LaporanReimburseController::class, 'fotoIndex'])->name('laporan.reimburse.foto');

    // ── Aging BPKB (Admin Pusat & Super Admin) ────────────────────────────
    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::get('/aging-bpkb',                          [AgingBpkbController::class, 'index'])->name('aging.index');
        Route::post('/aging-bpkb/{pengajuan}/tandai',      [AgingBpkbController::class, 'tandaiDiambil'])->name('aging.tandai');
        Route::get('/aging-bpkb/excel',                    [AgingBpkbController::class, 'exportExcel'])->name('aging.excel');
        Route::get('/aging-bpkb/pdf',                      [AgingBpkbController::class, 'exportPdf'])->name('aging.pdf');
    });

    // ── Stock List & QR Code ──────────────────────────────────────────────
    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::get('/stock-list',                            [StockListController::class, 'index'])->name('stock.index');
        Route::get('/stock-list/print-qr-batch',            [StockListController::class, 'printQrBatch'])->name('stock.print-qr-batch');
        Route::get('/stock-list/{pengajuan}/print-qr',      [StockListController::class, 'printQr'])->name('stock.print-qr');
        Route::post('/stock-list/{pengajuan}/tandai',        [StockListController::class, 'tandaiDiambil'])->name('stock.tandai');
    });

    // ── QR Scan Confirm (semua role yang login) ───────────────────────────
    Route::get('/qr/{token}',         [StockListController::class, 'scan'])->name('stock.scan');
    Route::post('/qr/{token}/ok',     [StockListController::class, 'confirmAmbil'])->name('stock.confirm');

    // ── Jaminan Kerja (Admin Cabang & Super Admin) ────────────────────────
    Route::middleware('role:ADMIN_CABANG,SUPER_ADMIN')->group(function () {
        Route::get('/jaminan-kerja',                         [JaminanKerjaController::class, 'index'])->name('jaminan-kerja.index');
        Route::get('/jaminan-kerja/buat',                    [JaminanKerjaController::class, 'create'])->name('jaminan-kerja.create');
        Route::post('/jaminan-kerja',                        [JaminanKerjaController::class, 'store'])->name('jaminan-kerja.store');
        Route::get('/jaminan-kerja/{jaminanKerja}',          [JaminanKerjaController::class, 'show'])->name('jaminan-kerja.show');
        Route::post('/jaminan-kerja/{jaminanKerja}/kembalikan',       [JaminanKerjaController::class, 'kembalikan'])->name('jaminan-kerja.kembalikan');
        Route::post('/jaminan-kerja/{jaminanKerja}/terima-karyawan',  [JaminanKerjaController::class, 'terimaKaryawan'])->name('jaminan-kerja.terima-karyawan');
        Route::get('/jaminan-kerja-lampiran/{lampiran}/download', [JaminanKerjaController::class, 'downloadLampiran'])->name('jaminan-kerja.lampiran.download');
        Route::get('/jaminan-kerja-lampiran/{lampiran}/preview',  [JaminanKerjaController::class, 'previewLampiran'])->name('jaminan-kerja.lampiran.preview');
    });

    // ── Approval & Proses Pengembalian oleh Admin Pusat ──────────────────
    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::post('/jaminan-kerja/{jaminanKerja}/approve-pusat',     [JaminanKerjaController::class, 'approvePusat'])->name('jaminan-kerja.approve-pusat');
        Route::post('/jaminan-kerja/{jaminanKerja}/reject-pusat',      [JaminanKerjaController::class, 'rejectPusat'])->name('jaminan-kerja.reject-pusat');
        Route::post('/jaminan-kerja/{jaminanKerja}/serah-kurir',       [JaminanKerjaController::class, 'serahKurir'])->name('jaminan-kerja.serah-kurir');
        Route::post('/jaminan-kerja/{jaminanKerja}/konfirmasi-selesai',[JaminanKerjaController::class, 'konfirmasiSelesai'])->name('jaminan-kerja.konfirmasi-selesai');
    });

    // ── Edit / Delete Jaminan Kerja (Super Admin only) ────────────────────
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/jaminan-kerja/{jaminanKerja}/edit', [JaminanKerjaController::class, 'edit'])->name('jaminan-kerja.edit');
        Route::put('/jaminan-kerja/{jaminanKerja}',      [JaminanKerjaController::class, 'update'])->name('jaminan-kerja.update');
        Route::delete('/jaminan-kerja/{jaminanKerja}',   [JaminanKerjaController::class, 'destroy'])->name('jaminan-kerja.destroy');
    });

    // ── Laporan Jaminan Kerja (Ijasah) ───────────────────────────────────
    Route::get('/laporan-jaminan-kerja',      [LaporanJaminanKerjaController::class, 'index'])->name('laporan.jaminan-kerja.index');
    Route::get('/laporan-jaminan-kerja/pdf',  [LaporanJaminanKerjaController::class, 'exportPdf'])->name('laporan.jaminan-kerja.pdf');

    // ── Komentar Pengajuan ────────────────────────────────────────────────
    Route::post('/pengajuan/{pengajuan}/komentar',          [KomentarController::class, 'store'])->name('komentar.store');
    Route::delete('/komentar/{komentar}',                   [KomentarController::class, 'destroy'])->name('komentar.destroy');

    // ── KPI (Admin Pusat & Super Admin) ──────────────────────────────────
    Route::middleware('role:ADMIN_PUSAT,SUPER_ADMIN')->group(function () {
        Route::get('/kpi', [KpiController::class, 'index'])->name('kpi.index');
    });

    // ── Audit Log (Super Admin only) ─────────────────────────────────────
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    });

    // ── Administrator ──────────────────────────────────────────────────────
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/administrator/cabang',             [AdministratorController::class, 'cabangIndex'])->name('admin.cabang.index');
        Route::post('/administrator/cabang',            [AdministratorController::class, 'cabangStore'])->name('admin.cabang.store');
        Route::put('/administrator/cabang/{cabang}',    [AdministratorController::class, 'cabangUpdate'])->name('admin.cabang.update');
        Route::delete('/administrator/cabang/{cabang}', [AdministratorController::class, 'cabangDestroy'])->name('admin.cabang.destroy');

        Route::get('/administrator/users',              [AdministratorController::class, 'userIndex'])->name('admin.users.index');
        Route::post('/administrator/users',             [AdministratorController::class, 'userStore'])->name('admin.users.store');
        Route::put('/administrator/users/{user}',       [AdministratorController::class, 'userUpdate'])->name('admin.users.update');
        Route::delete('/administrator/users/{user}',    [AdministratorController::class, 'userDestroy'])->name('admin.users.destroy');
    });
});
