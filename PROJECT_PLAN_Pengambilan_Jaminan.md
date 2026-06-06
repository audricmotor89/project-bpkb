# Project Plan: Sistem Pengajuan Pengambilan Jaminan
## Aplikasi Web — BPKB & Sertifikat untuk Pinjaman Lunas

---

## 1. Ringkasan Proyek

**Nama Proyek:** Sistem Pengajuan Pengambilan Jaminan (SPPJ)
**Tipe Aplikasi:** Web-based (Multi-cabang)
**Skala:** ±30 cabang
**Tujuan:** Menyediakan platform digital bagi admin cabang untuk mengajukan permohonan pengambilan jaminan (BPKB/Sertifikat) kepada kantor pusat, setelah nasabah melunasi pinjaman.

**Keputusan Desain yang Sudah Dikonfirmasi:**
- Tidak ada integrasi ke sistem existing — No. Kartu Piutang diinput manual
- Lampiran dokumen foto/scan wajib disertakan saat pengajuan
- Tidak ada approval berjenjang — pengajuan langsung dari Admin Cabang ke Admin Pusat
- Notifikasi aktif via Email dan WhatsApp saat perubahan status pengajuan

---

## 2. Peran Pengguna (User Roles)

| Peran | Kode | Deskripsi |
|---|---|---|
| Administrator | `SUPER_ADMIN` | Akses penuh: bisa edit & delete semua data |
| Admin Pusat | `ADMIN_PUSAT` | Menerima & memproses pengajuan dari cabang |
| Admin Cabang | `ADMIN_CABANG` | Membuat pengajuan pengambilan jaminan |

### Hak Akses per Peran

| Fitur | Admin Cabang | Admin Pusat | Administrator |
|---|:---:|:---:|:---:|
| Buat pengajuan | ✅ | ❌ | ✅ |
| Lihat pengajuan cabangnya | ✅ | ❌ | ✅ |
| Lihat semua pengajuan | ❌ | ✅ | ✅ |
| Proses / tindak lanjut pengajuan | ❌ | ✅ | ✅ |
| Akses laporan cabang sendiri | ✅ | ✅ | ✅ |
| Akses laporan semua cabang | ❌ | ✅ | ✅ |
| Export laporan (Excel/PDF) | ✅ | ✅ | ✅ |
| Edit pengajuan | ❌ | ❌ | ✅ |
| Delete pengajuan | ❌ | ❌ | ✅ |
| Kelola master data cabang | ❌ | ❌ | ✅ |
| Kelola akun pengguna | ❌ | ❌ | ✅ |

---

## 3. Alur Proses (Business Flow)

```
[Nasabah Lunas di Cabang]
         ↓
[Admin Cabang Login ke Aplikasi]
         ↓
[Admin Cabang Buat Pengajuan]
  - Pilih Jenis Jaminan: BPKB / Sertifikat
  - Input data nasabah & jaminan
         ↓
[Pengajuan Tersimpan → Status: MENUNGGU]
         ↓
[Admin Pusat Melihat Daftar Pengajuan Masuk]
         ↓
[Admin Pusat Memproses Pengajuan]
  - Verifikasi data
  - Update status: DIPROSES / DISETUJUI / DITOLAK
         ↓
[Admin Cabang Dapat Melihat Update Status]
         ↓
[Jaminan Diserahkan ke Nasabah]
```

---

## 4. Data & Form Pengajuan

### 4.1 Pengajuan Jaminan BPKB

| No | Field | Tipe | Wajib |
|---|---|---|:---:|
| 1 | Nama Nasabah | Text | ✅ |
| 2 | No. KTP | Text (16 digit) | ✅ |
| 3 | No. Polisi Kendaraan | Text | ✅ |
| 4 | Merek Motor | Text | ✅ |
| 5 | Tipe Motor | Text | ✅ |
| 6 | No. BPKB | Text | ✅ |
| 7 | No. Mesin | Text | ✅ |
| 8 | No. Rangka | Text | ✅ |
| 9 | Asal Cabang | Dropdown (pilih dari daftar cabang) | ✅ |
| 10 | Total Pinjaman | Currency (Rp) | ✅ |
| 11 | No. Kartu Piutang | Text (input manual) | ✅ |
| 12 | Foto / Scan BPKB | Upload file (JPG/PNG/PDF, maks. 5MB) | ✅ |
| 13 | Foto / Scan KTP Nasabah | Upload file (JPG/PNG/PDF, maks. 5MB) | ✅ |
| 14 | Dokumen Pendukung Lain | Upload file (opsional, maks. 5MB per file) | ❌ |

### 4.2 Pengajuan Jaminan Sertifikat

| No | Field | Tipe | Wajib |
|---|---|---|:---:|
| 1 | Nama Nasabah | Text | ✅ |
| 2 | No. KTP | Text (16 digit) | ✅ |
| 3 | No. Sertifikat | Text | ✅ |
| 4 | Asal Cabang | Dropdown (pilih dari daftar cabang) | ✅ |
| 5 | Total Pinjaman | Currency (Rp) | ✅ |
| 6 | No. Kartu Piutang | Text (input manual) | ✅ |
| 7 | Foto / Scan Sertifikat | Upload file (JPG/PNG/PDF, maks. 5MB) | ✅ |
| 8 | Foto / Scan KTP Nasabah | Upload file (JPG/PNG/PDF, maks. 5MB) | ✅ |
| 9 | Dokumen Pendukung Lain | Upload file (opsional, maks. 5MB per file) | ❌ |

### 4.3 Status Pengajuan

| Status | Keterangan |
|---|---|
| `MENUNGGU` | Pengajuan baru dibuat, belum diproses pusat |
| `DIPROSES` | Admin pusat sedang memverifikasi |
| `DISETUJUI` | Pengajuan disetujui, jaminan siap diambil |
| `DITOLAK` | Pengajuan ditolak dengan keterangan alasan |

---

## 5. Fitur Aplikasi

### 5.1 Modul Autentikasi
- Login dengan username & password
- Session management (auto logout setelah idle)
- Hak akses berdasarkan role

### 5.2 Modul Pengajuan (Admin Cabang)
- Form pengajuan BPKB (dengan upload foto/scan BPKB dan KTP)
- Form pengajuan Sertifikat (dengan upload foto/scan sertifikat dan KTP)
- Upload dokumen pendukung tambahan (opsional)
- Preview dokumen yang sudah diupload sebelum submit
- Daftar pengajuan yang telah dibuat (read-only)
- Filter pengajuan: berdasarkan status, tanggal, jenis jaminan
- Lihat & unduh lampiran dokumen dari pengajuan yang sudah dibuat
- Cetak/export pengajuan (PDF)

### 5.3 Modul Pemrosesan (Admin Pusat)
- Dashboard semua pengajuan masuk dari ±30 cabang
- Filter & pencarian pengajuan (per cabang, status, tanggal, jenis jaminan)
- Detail pengajuan lengkap beserta preview & unduh lampiran dokumen
- Verifikasi kesesuaian dokumen yang diupload dengan data yang diinput
- Update status + input catatan/keterangan
- Riwayat perubahan status (audit trail)

### 5.4 Modul Administrator
- Kelola akun pengguna (CRUD)
- Assign role ke pengguna
- Kelola master data cabang
- Edit & delete pengajuan (jika diperlukan koreksi)
- Laporan & export data

### 5.5 Modul Laporan

Modul laporan dapat diakses oleh **Admin Pusat** dan **Administrator**. Admin Cabang hanya dapat mengakses laporan untuk cabangnya sendiri.

#### Parameter Filter Laporan

| Parameter | Tipe | Keterangan |
|---|---|---|
| Tanggal Mulai | Date picker | Tanggal pengajuan dibuat (dari) |
| Tanggal Selesai | Date picker | Tanggal pengajuan dibuat (sampai) |
| Cabang | Dropdown multi-select | Pilih satu atau lebih cabang; default: semua cabang |
| Jenis Jaminan | Dropdown | Semua / BPKB / Sertifikat |
| Status Dokumen | Checkbox multi-pilih | Filter berdasarkan status: `MENUNGGU`, `DIPROSES`, `DISETUJUI`, `DITOLAK` (bisa pilih lebih dari satu) |

#### Kolom yang Ditampilkan di Laporan

| No | Kolom | Keterangan |
|---|---|---|
| 1 | No. Pengajuan | Nomor unik pengajuan |
| 2 | Tanggal Pengajuan | Tanggal & waktu pengajuan dibuat |
| 3 | Asal Cabang | Nama cabang pengaju |
| 4 | Jenis Jaminan | BPKB / Sertifikat |
| 5 | Nama Nasabah | Nama nasabah |
| 6 | No. KTP | Nomor identitas nasabah |
| 7 | No. Kartu Piutang | Nomor dari sistem existing |
| 8 | Total Pinjaman | Nominal pinjaman (Rp) |
| 9 | Status | Status terkini pengajuan |
| 10 | Tanggal Diproses | Tanggal & waktu terakhir status diubah |
| 11 | Diproses Oleh | Nama Admin Pusat yang memproses |
| 12 | Catatan | Keterangan dari Admin Pusat |

> Untuk jaminan BPKB, kolom tambahan: No. Polisi, Merek, Tipe Motor, No. BPKB
> Untuk jaminan Sertifikat, kolom tambahan: No. Sertifikat

#### Ringkasan / Summary di Laporan

Bagian atas laporan menampilkan rekap otomatis berdasarkan hasil filter:

| Ringkasan | Keterangan |
|---|---|
| Total Pengajuan | Jumlah seluruh pengajuan sesuai filter |
| Total BPKB | Jumlah pengajuan jaminan BPKB |
| Total Sertifikat | Jumlah pengajuan jaminan Sertifikat |
| Menunggu | Jumlah pengajuan berstatus MENUNGGU |
| Diproses | Jumlah pengajuan berstatus DIPROSES |
| Disetujui | Jumlah pengajuan berstatus DISETUJUI |
| Ditolak | Jumlah pengajuan berstatus DITOLAK |
| Total Nilai Pinjaman | Akumulasi total pinjaman dari semua pengajuan dalam filter |

#### Format Export Laporan

- **Tampil di layar** — tabel interaktif dengan pagination
- **Export Excel (.xlsx)** — seluruh data sesuai filter
- **Export PDF** — format cetak dengan header, filter yang digunakan, dan ringkasan di halaman pertama

#### Hak Akses Laporan

| Peran | Akses |
|---|---|
| Admin Cabang | Hanya laporan cabangnya sendiri |
| Admin Pusat | Semua cabang, semua status |
| Administrator | Semua cabang, semua status |

---

### 5.6 Modul Notifikasi

Notifikasi dikirimkan secara otomatis kepada pihak yang berkepentingan saat terjadi perubahan status pengajuan.

#### Pemicu Notifikasi

| Event | Dikirim Ke | Keterangan |
|---|---|---|
| Pengajuan baru dibuat | Admin Pusat | Pemberitahuan ada pengajuan masuk dari cabang |
| Status berubah → DIPROSES | Admin Cabang | Pengajuan sedang diverifikasi pusat |
| Status berubah → DISETUJUI | Admin Cabang | Pengajuan disetujui, jaminan siap diambil |
| Status berubah → DITOLAK | Admin Cabang | Pengajuan ditolak, disertai alasan penolakan |

#### Channel Notifikasi

**Email**
- Dikirim ke alamat email yang terdaftar di akun pengguna
- Template email berbeda untuk setiap event
- Isi email memuat: No. Pengajuan, Nama Nasabah, Jenis Jaminan, Status Baru, Catatan dari Admin Pusat, dan link langsung ke halaman detail pengajuan
- Layanan pengiriman: SMTP sendiri atau layanan pihak ketiga (misal: SendGrid, Mailgun)

**WhatsApp**
- Dikirim ke nomor WhatsApp yang terdaftar di akun pengguna
- Menggunakan WhatsApp Business API atau layanan gateway pihak ketiga (misal: Fonnte, Wablas, Zenziva)
- Pesan singkat memuat: No. Pengajuan, Nama Nasabah, Status Baru, dan Catatan
- Format pesan disesuaikan agar ringkas dan mudah dibaca di mobile

#### Konfigurasi Notifikasi (dikelola Administrator)
- On/off per channel (Email / WhatsApp) secara global
- Template pesan dapat dikustomisasi
- Pengaturan nomor WA dan email per akun pengguna

#### Penambahan Data di Tabel `users`
Untuk mendukung notifikasi, tabel `users` ditambah field:
```
email, no_whatsapp, notif_email (boolean), notif_whatsapp (boolean)
```

---

## 6. Struktur Database (MySQL)

> Semua tabel menggunakan engine **InnoDB**, charset **utf8mb4**, collation **utf8mb4_unicode_ci**.
> Relasi antar tabel menggunakan **Foreign Key** dengan constraint `ON DELETE RESTRICT` untuk menjaga integritas data.

### Tabel: `users`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
username        VARCHAR(50) UNIQUE NOT NULL
password        VARCHAR(255) NOT NULL           -- bcrypt hash
nama_lengkap    VARCHAR(100) NOT NULL
role            ENUM('SUPER_ADMIN','ADMIN_PUSAT','ADMIN_CABANG') NOT NULL
email           VARCHAR(100) UNIQUE NOT NULL
no_whatsapp     VARCHAR(20) NULL                -- format: 628xxxxxxxx
notif_email     TINYINT(1) DEFAULT 1
notif_whatsapp  TINYINT(1) DEFAULT 1
aktif           TINYINT(1) DEFAULT 1
remember_token  VARCHAR(100) NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `cabang`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
kode_cabang     VARCHAR(10) UNIQUE NOT NULL     -- contoh: JKT01
nama_cabang     VARCHAR(100) NOT NULL
alamat          TEXT NULL
aktif           TINYINT(1) DEFAULT 1
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `pengajuan`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
no_pengajuan    VARCHAR(30) UNIQUE NOT NULL     -- JKT01-BPKB-20260516-001
jenis_jaminan   ENUM('BPKB','SERTIFIKAT') NOT NULL
cabang_id       BIGINT UNSIGNED NOT NULL        -- FK → cabang.id
dibuat_oleh     BIGINT UNSIGNED NOT NULL        -- FK → users.id
status          ENUM('MENUNGGU','DIPROSES','DISETUJUI','DITOLAK') DEFAULT 'MENUNGGU'
catatan_pusat   TEXT NULL
diproses_oleh   BIGINT UNSIGNED NULL            -- FK → users.id
tgl_dibuat      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
tgl_diproses    TIMESTAMP NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `detail_bpkb`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
pengajuan_id    BIGINT UNSIGNED NOT NULL        -- FK → pengajuan.id
nama_nasabah    VARCHAR(100) NOT NULL
no_ktp          VARCHAR(16) NOT NULL
no_polisi       VARCHAR(15) NOT NULL
merek_motor     VARCHAR(50) NOT NULL
tipe_motor      VARCHAR(50) NOT NULL
no_bpkb         VARCHAR(30) NOT NULL
no_mesin        VARCHAR(30) NOT NULL
no_rangka       VARCHAR(30) NOT NULL
total_pinjaman  DECIMAL(15,2) NOT NULL
no_kartu_piutang VARCHAR(30) NOT NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `detail_sertifikat`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
pengajuan_id    BIGINT UNSIGNED NOT NULL        -- FK → pengajuan.id
nama_nasabah    VARCHAR(100) NOT NULL
no_ktp          VARCHAR(16) NOT NULL
no_sertifikat   VARCHAR(50) NOT NULL
total_pinjaman  DECIMAL(15,2) NOT NULL
no_kartu_piutang VARCHAR(30) NOT NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `lampiran_dokumen`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
pengajuan_id    BIGINT UNSIGNED NOT NULL        -- FK → pengajuan.id
jenis_dokumen   ENUM('BPKB','SERTIFIKAT','KTP','LAINNYA') NOT NULL
nama_file_asli  VARCHAR(255) NOT NULL
nama_file_storage VARCHAR(255) NOT NULL         -- nama acak di server
ukuran_file     INT UNSIGNED NOT NULL           -- dalam bytes
mime_type       VARCHAR(50) NOT NULL            -- image/jpeg, application/pdf, dll
diupload_oleh   BIGINT UNSIGNED NOT NULL        -- FK → users.id
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `audit_log`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
pengajuan_id    BIGINT UNSIGNED NOT NULL        -- FK → pengajuan.id
user_id         BIGINT UNSIGNED NOT NULL        -- FK → users.id
aksi            VARCHAR(50) NOT NULL            -- misal: BUAT, UBAH_STATUS
status_lama     VARCHAR(20) NULL
status_baru     VARCHAR(20) NULL
keterangan      TEXT NULL
ip_address      VARCHAR(45) NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

### Tabel: `notifikasi_log`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
pengajuan_id    BIGINT UNSIGNED NOT NULL        -- FK → pengajuan.id
user_id         BIGINT UNSIGNED NOT NULL        -- FK → users.id
channel         ENUM('EMAIL','WHATSAPP') NOT NULL
event           VARCHAR(50) NOT NULL
status_kirim    ENUM('SUKSES','GAGAL') NOT NULL
pesan_error     TEXT NULL
tgl_kirim       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL
```

---

## 7. Tech Stack

| Layer | Teknologi | Keterangan |
|---|---|---|
| Backend Framework | **Laravel 7.x** | PHP framework, MVC pattern |
| Bahasa Backend | **PHP 7.2.5 – 7.4.x** | Versi minimum PHP untuk Laravel 7 |
| Database | **MySQL 5.7 / 8.0** | Relational database, diakses via Eloquent ORM |
| Template Engine | **Blade** | Template engine bawaan Laravel 7 |
| Auth | **Laravel Auth (built-in)** | `php artisan make:auth` + middleware role |
| File Storage | **Laravel Storage (local disk)** | Simpan lampiran di `storage/app/private/` |
| HTTP Client | **Guzzle HTTP** | Bawaan Laravel 7, untuk panggil API WhatsApp |
| Notifikasi Email | **Laravel Mail + SMTP** | Driver mail bawaan Laravel 7 (Mailgun / SMTP) |
| Notifikasi WhatsApp | **Fonnte / Wablas / Zenziva API** | Dipanggil via Guzzle dari Laravel |
| Export Excel | **Maatwebsite/Laravel-Excel 3.1** | Kompatibel dengan Laravel 7 |
| Export / Cetak PDF | **barryvdh/laravel-dompdf** | Kompatibel dengan Laravel 7 |
| Frontend CSS | **Bootstrap 4 / Tailwind CSS** | Dirender via Blade template |
| JavaScript | **jQuery + Vanilla JS** | Untuk interaksi form & AJAX |
| Package Manager | **Composer** | Untuk dependency PHP |
| Hosting | **VPS / shared hosting** | PHP 7.2.5–7.4, MySQL, Apache/Nginx |

### Versi Package Composer Utama (`composer.json`)

```json
{
    "require": {
        "php": "^7.2.5",
        "laravel/framework": "^7.30",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^0.9",
        "guzzlehttp/guzzle": "^7.0"
    }
}
```

### Struktur Folder Laravel (Utama)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── PengajuanController.php
│   │   ├── LaporanController.php
│   │   ├── AdminPusatController.php
│   │   └── AdministratorController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Cabang.php
│   ├── Pengajuan.php
│   ├── DetailBpkb.php
│   ├── DetailSertifikat.php
│   ├── LampiranDokumen.php
│   └── AuditLog.php
database/
├── migrations/
└── seeders/
resources/
├── views/
│   ├── layouts/
│   ├── pengajuan/
│   ├── admin_pusat/
│   ├── laporan/
│   └── administrator/
routes/
└── web.php
storage/
└── app/private/lampiran/
```

> ⚠️ **Catatan Penting — Laravel 7 End-of-Life (EOL)**
> Laravel 7 telah memasuki status EOL sejak **3 Maret 2021**. Tidak ada lagi security patch atau bug fix resmi dari tim Laravel.
> Risiko yang perlu diperhatikan selama masa operasional aplikasi:
> - Celah keamanan baru yang ditemukan tidak akan di-patch secara resmi
> - Beberapa package pihak ketiga terbaru mungkin tidak lagi mendukung Laravel 7
> - Disarankan untuk merencanakan **upgrade ke Laravel 10 atau 11** dalam jangka menengah (6–12 bulan setelah go-live)
> - Pastikan server tidak terekspos langsung ke internet tanpa firewall/WAF tambahan

---

## 8. Aturan Bisnis (Business Rules)

1. **Immutability:** Data pengajuan yang sudah dibuat oleh Admin Cabang dan sudah diproses Admin Pusat TIDAK DAPAT diedit atau dihapus, kecuali oleh Administrator.
2. **Pilih Cabang:** Field "Asal Cabang" diisi oleh Admin Cabang dengan memilih dari daftar cabang yang tersedia dalam sistem. Daftar cabang dikelola oleh Administrator.
3. **Nomor Pengajuan:** Dibuat otomatis oleh sistem dengan format: `[KODE_CABANG]-[JENIS]-[YYYYMMDD]-[SEQ]`. Contoh: `JKT01-BPKB-20260516-001`
4. **No. Kartu Piutang:** Diinput manual oleh Admin Cabang, merujuk ke nomor dari sistem yang sudah ada.
5. **Satu Pengajuan per Pelunasan:** Disarankan ada validasi No. Kartu Piutang agar tidak double submission.
6. **Lampiran Dokumen Wajib:** Setiap pengajuan wajib menyertakan foto/scan dokumen jaminan (BPKB atau Sertifikat) dan KTP nasabah. Pengajuan tidak dapat disubmit tanpa lampiran tersebut. Format file yang diterima: JPG, PNG, PDF. Ukuran maksimal per file: 5MB.
7. **Dokumen Tidak Dapat Dihapus:** Lampiran dokumen yang sudah diupload tidak dapat dihapus oleh Admin Cabang maupun Admin Pusat. Hanya Administrator yang dapat mengelola file lampiran.
8. **Alur Persetujuan Langsung:** Tidak ada approval berjenjang. Pengajuan dari Admin Cabang langsung masuk ke antrian Admin Pusat.
9. **Notifikasi Otomatis:** Sistem mengirimkan notifikasi Email dan WhatsApp secara otomatis pada setiap perubahan status pengajuan. Kegagalan pengiriman notifikasi dicatat di `notifikasi_log` dan tidak mengganggu proses utama.
10. **Audit Trail:** Setiap perubahan status dicatat lengkap dengan user, waktu, dan keterangan.

---

## 9. Rencana Pengembangan (Timeline)

### Fase 1 — Fondasi (Minggu 1–2)
- [ ] Setup environment: PHP 7.2.5–7.4, MySQL 5.7/8.0, Composer
- [ ] Install Laravel 7 (`composer create-project laravel/laravel:^7.0`)
- [ ] Konfigurasi `.env`: koneksi MySQL, mail driver, storage
- [ ] Buat semua migration & jalankan `php artisan migrate`
- [ ] Setup Laravel Auth (`php artisan make:auth`) + middleware CheckRole
- [ ] Seeder data awal: role, akun administrator, master cabang (±30 cabang)
- [ ] Install package: `maatwebsite/excel`, `barryvdh/laravel-dompdf`, `guzzlehttp/guzzle`

### Fase 2 — Fitur Utama (Minggu 3–5)
- [ ] Form pengajuan BPKB + upload lampiran dokumen
- [ ] Form pengajuan Sertifikat + upload lampiran dokumen
- [ ] Preview & validasi file sebelum submit
- [ ] Daftar pengajuan (Admin Cabang — view only + lihat lampiran)
- [ ] Dashboard & pemrosesan pengajuan (Admin Pusat + lihat lampiran)
- [ ] Update status + audit trail

### Fase 3 — Kelengkapan (Minggu 6–7)
- [ ] Modul laporan dengan filter tanggal, cabang, jenis jaminan & status dokumen
- [ ] Ringkasan/summary otomatis di atas tabel laporan
- [ ] Export laporan ke Excel (.xlsx) dan PDF
- [ ] Fitur cetak surat pengajuan per record
- [ ] Integrasi notifikasi Email (SMTP / SendGrid)
- [ ] Integrasi notifikasi WhatsApp (gateway API)
- [ ] Template pesan notifikasi & konfigurasi oleh Administrator
- [ ] Pencarian & filter lanjutan di daftar pengajuan
- [ ] Testing & QA semua role

### Fase 4 — Finalisasi (Minggu 8)
- [ ] User acceptance testing (UAT) bersama tim cabang & pusat
- [ ] Perbaikan bug & penyesuaian
- [ ] Deployment ke server produksi
- [ ] Training pengguna

---

## 10. Pertimbangan Keamanan

- Semua password disimpan dengan `bcrypt` via `Hash::make()` bawaan Laravel
- HTTPS wajib di server produksi, konfigurasi `APP_URL` dengan `https://`
- Laravel CSRF token aktif di semua form Blade (`@csrf`)
- Session timeout otomatis, konfigurasi di `config/session.php`
- Log aktivitas pengguna (siapa login, kapan, dari IP mana) via `audit_log`
- Validasi input di Controller menggunakan `$request->validate()` Laravel untuk mencegah SQL injection & XSS
- File upload: validasi tipe (`mimes:jpg,jpeg,png,pdf`) & ukuran (`max:5120`) di Laravel Validator
- File disimpan di `storage/app/private/lampiran/` — tidak bisa diakses langsung via URL publik
- Akses file lampiran melalui controller dengan pengecekan hak akses sebelum streaming file
- API key WhatsApp gateway dan credential email disimpan di `.env`, tidak pernah di kode sumber
- Backup database MySQL terjadwal harian (`mysqldump`) dan backup folder storage
- ⚠️ **Laravel 7 EOL:** Pantau security advisory PHP dan dependency secara manual. Rencanakan upgrade ke Laravel 10/11 dalam 6–12 bulan setelah go-live

---

## 11. Keputusan Final dari Diskusi

Semua pertanyaan desain awal telah dikonfirmasi dan tidak ada pertanyaan terbuka yang tersisa untuk memulai development.

| # | Pertanyaan | Keputusan |
|---|---|---|
| 1 | Integrasi ke sistem existing? | ❌ Tidak — No. Kartu Piutang diinput manual |
| 2 | Lampiran dokumen foto/scan? | ✅ Ya — wajib saat pengajuan (BPKB/Sertifikat + KTP) |
| 3 | Berapa banyak cabang? | ±30 cabang |
| 4 | Approval berjenjang dari kepala cabang? | ❌ Tidak — langsung dari Admin Cabang ke Admin Pusat |
| 5 | Notifikasi Email & WhatsApp? | ✅ Ya — keduanya aktif pada setiap perubahan status |

---

*Dokumen ini adalah project plan final yang siap dijadikan acuan development.*
*Versi: 1.3 | Tanggal: Mei 2026 | Perubahan: Tech stack dikonfirmasi — Laravel 7.x + MySQL; detail tipe data MySQL ditambahkan; struktur folder Laravel; versi package Composer; catatan EOL Laravel 7*
