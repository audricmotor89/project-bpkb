-- MySQL dump 10.13  Distrib 9.6.0, for macos14.8 (x86_64)
--
-- Host: localhost    Database: db_bpkb
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '748f849a-50fd-11f1-abe3-8b45b9d2f03e:1-224';

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `aksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_lama` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_log_pengajuan_id_foreign` (`pengajuan_id`),
  KEY `audit_log_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_log_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `audit_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cabang`
--

DROP TABLE IF EXISTS `cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_cabang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_cabang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `aktif` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cabang_kode_cabang_unique` (`kode_cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabang`
--

LOCK TABLES `cabang` WRITE;
/*!40000 ALTER TABLE `cabang` DISABLE KEYS */;
INSERT INTO `cabang` VALUES (25,'AMB01','PONDOK LABU','Margasatwa Raya no. 88',1,'2026-05-16 01:15:08','2026-05-17 05:10:51');
/*!40000 ALTER TABLE `cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('group-mega-cache-login.edy.::1','i:1;',1779019899),('group-mega-cache-login.edy.::1:timer','i:1779019899;',1779019899);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_bpkb`
--

DROP TABLE IF EXISTS `detail_bpkb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_bpkb` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `nama_nasabah` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_ktp` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_polisi` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merek_motor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_motor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_bpkb` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_mesin` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rangka` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pinjaman` decimal(15,2) NOT NULL,
  `no_kartu_piutang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_bpkb_pengajuan_id_foreign` (`pengajuan_id`),
  CONSTRAINT `detail_bpkb_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_bpkb`
--

LOCK TABLES `detail_bpkb` WRITE;
/*!40000 ALTER TABLE `detail_bpkb` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_bpkb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_sertifikat`
--

DROP TABLE IF EXISTS `detail_sertifikat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_sertifikat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `nama_nasabah` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_ktp` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_sertifikat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pinjaman` decimal(15,2) NOT NULL,
  `no_kartu_piutang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_sertifikat_pengajuan_id_foreign` (`pengajuan_id`),
  CONSTRAINT `detail_sertifikat_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_sertifikat`
--

LOCK TABLES `detail_sertifikat` WRITE;
/*!40000 ALTER TABLE `detail_sertifikat` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_sertifikat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `komentar_pengajuan`
--

DROP TABLE IF EXISTS `komentar_pengajuan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `komentar_pengajuan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `komentar` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `komentar_pengajuan_pengajuan_id_foreign` (`pengajuan_id`),
  KEY `komentar_pengajuan_user_id_foreign` (`user_id`),
  CONSTRAINT `komentar_pengajuan_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `komentar_pengajuan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `komentar_pengajuan`
--

LOCK TABLES `komentar_pengajuan` WRITE;
/*!40000 ALTER TABLE `komentar_pengajuan` DISABLE KEYS */;
/*!40000 ALTER TABLE `komentar_pengajuan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lampiran_dokumen`
--

DROP TABLE IF EXISTS `lampiran_dokumen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lampiran_dokumen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `jenis_dokumen` enum('BPKB','SERTIFIKAT','KTP','LAINNYA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_asli` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_storage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ukuran_file` int unsigned NOT NULL,
  `mime_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diupload_oleh` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lampiran_dokumen_pengajuan_id_foreign` (`pengajuan_id`),
  KEY `lampiran_dokumen_diupload_oleh_foreign` (`diupload_oleh`),
  CONSTRAINT `lampiran_dokumen_diupload_oleh_foreign` FOREIGN KEY (`diupload_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lampiran_dokumen_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lampiran_dokumen`
--

LOCK TABLES `lampiran_dokumen` WRITE;
/*!40000 ALTER TABLE `lampiran_dokumen` DISABLE KEYS */;
/*!40000 ALTER TABLE `lampiran_dokumen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lampiran_reimburse`
--

DROP TABLE IF EXISTS `lampiran_reimburse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lampiran_reimburse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reimburse_id` bigint unsigned NOT NULL,
  `jenis_dokumen` enum('KWITANSI','STRUK','FOTO','LAINNYA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_asli` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_storage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ukuran_file` int unsigned NOT NULL,
  `mime_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diupload_oleh` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lampiran_reimburse_reimburse_id_foreign` (`reimburse_id`),
  KEY `lampiran_reimburse_diupload_oleh_foreign` (`diupload_oleh`),
  CONSTRAINT `lampiran_reimburse_diupload_oleh_foreign` FOREIGN KEY (`diupload_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lampiran_reimburse_reimburse_id_foreign` FOREIGN KEY (`reimburse_id`) REFERENCES `reimburse` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lampiran_reimburse`
--

LOCK TABLES `lampiran_reimburse` WRITE;
/*!40000 ALTER TABLE `lampiran_reimburse` DISABLE KEYS */;
/*!40000 ALTER TABLE `lampiran_reimburse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_16_081235_create_cabang_table',1),(5,'2026_05_16_081236_create_pengajuan_table',1),(6,'2026_05_16_081236_modify_users_table_for_bpkb',1),(7,'2026_05_16_081237_create_detail_bpkb_table',1),(8,'2026_05_16_081237_create_detail_sertifikat_table',1),(9,'2026_05_16_081237_create_lampiran_dokumen_table',1),(10,'2026_05_16_081238_create_audit_log_table',1),(11,'2026_05_16_081238_create_notifikasi_log_table',1),(12,'2026_05_16_083929_create_reimburse_table',2),(13,'2026_05_16_083930_create_lampiran_reimburse_table',2),(14,'2026_05_16_091008_create_user_cabang_table',3),(15,'2026_05_16_132333_add_tgl_diambil_to_pengajuan_table',4),(16,'2026_05_16_143655_add_qr_token_to_pengajuan_table',5),(17,'2026_05_16_151647_create_notifikasi_table',6),(18,'2026_05_16_163859_create_komentar_pengajuan_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pengajuan_id` bigint unsigned DEFAULT NULL,
  `judul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INFO',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibaca` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_user_id_foreign` (`user_id`),
  KEY `notifikasi_pengajuan_id_foreign` (`pengajuan_id`),
  CONSTRAINT `notifikasi_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifikasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi_log`
--

DROP TABLE IF EXISTS `notifikasi_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `channel` enum('EMAIL','WHATSAPP') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kirim` enum('SUKSES','GAGAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan_error` text COLLATE utf8mb4_unicode_ci,
  `tgl_kirim` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_log_pengajuan_id_foreign` (`pengajuan_id`),
  KEY `notifikasi_log_user_id_foreign` (`user_id`),
  CONSTRAINT `notifikasi_log_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `notifikasi_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi_log`
--

LOCK TABLES `notifikasi_log` WRITE;
/*!40000 ALTER TABLE `notifikasi_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifikasi_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengajuan`
--

DROP TABLE IF EXISTS `pengajuan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajuan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_pengajuan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jaminan` enum('BPKB','SERTIFIKAT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `dibuat_oleh` bigint unsigned NOT NULL,
  `status` enum('MENUNGGU','DIPROSES','DISETUJUI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU',
  `catatan_pusat` text COLLATE utf8mb4_unicode_ci,
  `diproses_oleh` bigint unsigned DEFAULT NULL,
  `tgl_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_diproses` timestamp NULL DEFAULT NULL,
  `tgl_diambil` timestamp NULL DEFAULT NULL,
  `diambil_oleh` bigint unsigned DEFAULT NULL,
  `qr_token` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengajuan_no_pengajuan_unique` (`no_pengajuan`),
  UNIQUE KEY `pengajuan_qr_token_unique` (`qr_token`),
  KEY `pengajuan_cabang_id_foreign` (`cabang_id`),
  KEY `pengajuan_dibuat_oleh_foreign` (`dibuat_oleh`),
  KEY `pengajuan_diproses_oleh_foreign` (`diproses_oleh`),
  KEY `pengajuan_diambil_oleh_foreign` (`diambil_oleh`),
  CONSTRAINT `pengajuan_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pengajuan_diambil_oleh_foreign` FOREIGN KEY (`diambil_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengajuan_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pengajuan_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajuan`
--

LOCK TABLES `pengajuan` WRITE;
/*!40000 ALTER TABLE `pengajuan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengajuan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reimburse`
--

DROP TABLE IF EXISTS `reimburse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reimburse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_reimburse` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `dibuat_oleh` bigint unsigned NOT NULL,
  `nama_pemohon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pengeluaran` date NOT NULL,
  `kategori` enum('TRANSPORT','MAKAN','AKOMODASI','OPERASIONAL','LAINNYA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal_diajukan` decimal(15,2) NOT NULL,
  `nominal_disetujui` decimal(15,2) DEFAULT NULL,
  `status` enum('MENUNGGU','DISETUJUI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU',
  `catatan_pusat` text COLLATE utf8mb4_unicode_ci,
  `diproses_oleh` bigint unsigned DEFAULT NULL,
  `tgl_diproses` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reimburse_no_reimburse_unique` (`no_reimburse`),
  KEY `reimburse_cabang_id_foreign` (`cabang_id`),
  KEY `reimburse_dibuat_oleh_foreign` (`dibuat_oleh`),
  KEY `reimburse_diproses_oleh_foreign` (`diproses_oleh`),
  CONSTRAINT `reimburse_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reimburse_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reimburse_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reimburse`
--

LOCK TABLES `reimburse` WRITE;
/*!40000 ALTER TABLE `reimburse` DISABLE KEYS */;
/*!40000 ALTER TABLE `reimburse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0tZd3COiNH8tx27655wGHH5a78T6l3rtVgObA6V3',NULL,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJwV1BEV0JyNlVuRm12T0ozSHoydlprRTRWc1lheHBmZGpHamZuRVc2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1778984728),('4qqXsPrEFcDb433yyscX4esKxTemlBlroM4LI4vJ',NULL,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJjVUxqeUU4MHplYmZORUM4UzAxa0xYa1J0NFhKemhjZlpuRFE5dGFYIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1779020066),('8tFJgdQXhJzJcqrCAKqKaEDsg5eMqtZr7tLLTBID',NULL,'127.0.0.1','curl/8.7.1','eyJfdG9rZW4iOiJaSk9OcEp3bHVrV0k4blp0RUkyd05KcVNIbUVRZEJvcjFrdEQ0ZERrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1779610753),('gBNymRGzEMAUb7zAYaJQgeGZfCXsP7QCGhg4R0FD',NULL,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJqZGc5V0hxT3RUejhLRmROSjJ4Rnl3bXJKT0lHTXE2QktvNVd2YzBBIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1778951609),('HUpEH181Zq8esQz5Ujygoaz0xVW5nOc0r8Yf6vr8',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Safari/605.1.15','eyJfdG9rZW4iOiJRTkZwOGxRd284VnRZZWxGM2pEMnFZUDViZ25hUXVnc0VRVzhOVFF1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1779611095),('HWIgTs4BMXW55Sv4bC5tyUXCazHzdldP1ymcuj0F',NULL,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJQMjZPcUlUYzRIZnI4WGNnbGNQc0xIdWJ5bXB6c2NqSFZBdmhVaU5QIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1779114723),('osrvpe2xfQNOwQ9vcNt4oHXGrCfQsO5PhVzfwGpp',NULL,'::1','curl/8.7.1','eyJfdG9rZW4iOiJaU0RZbmxxUG5RZGQ2UXZFVzVRNmU4TTIxRlRJRXgxeDYzQzB4VnkxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJnZW5lcmF0ZWQ6OmRscThJd25QNVYzRGpjYWsifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1779019398),('qCBu2KAQvXsK06UU2JP37uerdqiTm0tzeigc4wo0',NULL,'127.0.0.1','curl/8.7.1','eyJfdG9rZW4iOiJUelZpRkI1MXU2NVR0NnZZWHdHVjNqT3NHb0F0MVRvaWQyNlhiMkxMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJnZW5lcmF0ZWQ6OmRscThJd25QNVYzRGpjYWsifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1779610753),('yECxHxVInuomU0NpeSOnCECZHv1XEJDE0oKOSHnu',NULL,'127.0.0.1','curl/8.7.1','eyJfdG9rZW4iOiI2TDRPVDNib3RxakZCVzNLV3ZmOGpjY0NObGtWVFBvb0VVVnJkRjFXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJnZW5lcmF0ZWQ6OmRscThJd25QNVYzRGpjYWsifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1779610431);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_cabang`
--

DROP TABLE IF EXISTS `user_cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cabang` (
  `user_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`cabang_id`),
  KEY `user_cabang_cabang_id_foreign` (`cabang_id`),
  CONSTRAINT `user_cabang_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_cabang_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_cabang`
--

LOCK TABLES `user_cabang` WRITE;
/*!40000 ALTER TABLE `user_cabang` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('SUPER_ADMIN','ADMIN_PUSAT','ADMIN_CABANG') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notif_email` tinyint NOT NULL DEFAULT '1',
  `notif_whatsapp` tinyint NOT NULL DEFAULT '1',
  `aktif` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_cabang_id_foreign` (`cabang_id`),
  CONSTRAINT `users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'superadmin','$2y$12$MRzt0Aq4tTRECDC6iNJ9bO5X2rjltO31JZEPnQhyz6U.k4ArFMtXG','Super Administrator','SUPER_ADMIN',NULL,'superadmin@bpkb.local',NULL,1,1,1,'TMcKSTZE3grhRBuVCuIQpljXRN55pYt61xO5z2XpfWAePwzLcenpuQxL2za7','2026-05-16 01:15:08','2026-05-16 06:14:53'),(2,'adminpusat','$2y$12$otCvnV4jldqjj9VWe1lKUuHFcvXIlZ88F3840aulvbb1WgeFVNaFm','Admin Pusat','ADMIN_PUSAT',NULL,'adminpusat@bpkb.local',NULL,1,1,1,NULL,'2026-05-16 01:15:08','2026-05-16 01:15:08'),(3,'adminjkt01','$2y$12$FiCWAbOe/09w/wZny2C7M.Peq2Spf6omFxxlsLtpCnRSsXUw3q.1e','Admin Cabang Jakarta Pusat','ADMIN_CABANG',25,'adminjkt01@bpkb.local',NULL,1,1,1,NULL,'2026-05-16 01:15:09','2026-05-17 05:10:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-24 15:25:39
