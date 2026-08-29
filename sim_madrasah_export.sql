mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.4.11, for macos15.7 (arm64)
--
-- Host: localhost    Database: sim_madrasah
-- ------------------------------------------------------
-- Server version	8.4.11

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
mysqldump: Error: 'Access denied; you need (at least one of) the PROCESS privilege(s) for this operation' when trying to dump tablespaces

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ganjil',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2026/2027','ganjil',1,'2026-08-27 01:09:42','2026-08-27 01:09:42');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `jenis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kegiatan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penyelenggara` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `peringkat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pembimbing` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sertifikat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_verifikasi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `status_publikasi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publik',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `achievements_created_by_foreign` (`created_by`),
  KEY `achievements_student_id_tanggal_index` (`student_id`,`tanggal`),
  CONSTRAINT `achievements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `achievements_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievements`
--

LOCK TABLES `achievements` WRITE;
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
INSERT INTO `achievements` VALUES (1,1,'nonakademik','Lomba Pidato Bahasa Arab','kabupaten','Kemenag Kabupaten','2026-07-27','Juara 1','Bapak Imam Syafii',NULL,NULL,'terverifikasi','publik',3,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `attribute_changes` json DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,'default','created','App\\Models\\Room',1,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-001\", \"name\": \"Ruang Guru\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung Utama\", \"capacity\": 30, \"condition\": \"baik\", \"employee_id\": 9}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(2,'default','created','App\\Models\\Room',2,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-002\", \"name\": \"Kantor Kepala Madrasah\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung Utama\", \"capacity\": 5, \"condition\": \"baik\", \"employee_id\": 9}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(3,'default','created','App\\Models\\Room',3,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-003\", \"name\": \"Kantor Tata Usaha\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung Utama\", \"capacity\": 8, \"condition\": \"baik\", \"employee_id\": 39}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(4,'default','created','App\\Models\\Room',4,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-004\", \"name\": \"Ruang BK\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung Utama\", \"capacity\": 4, \"condition\": \"baik\", \"employee_id\": null}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(5,'default','created','App\\Models\\Room',5,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-005\", \"name\": \"Ruang Perpustakaan\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung Utama\", \"capacity\": 20, \"condition\": \"baik\", \"employee_id\": null}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(6,'default','created','App\\Models\\Room',6,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-006\", \"name\": \"Ruang Kelas I\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung A\", \"capacity\": 36, \"condition\": \"baik\", \"employee_id\": null}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(7,'default','created','App\\Models\\Room',7,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-007\", \"name\": \"Ruang Kelas II\", \"type\": \"ruangan\", \"floor\": \"Lantai 1\", \"building\": \"Gedung A\", \"capacity\": 36, \"condition\": \"baik\", \"employee_id\": null}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(8,'default','created','App\\Models\\Room',8,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-008\", \"name\": \"Aula\", \"type\": \"ruangan\", \"floor\": \"Lantai 2\", \"building\": \"Gedung Utama\", \"capacity\": 200, \"condition\": \"rusak_ringan\", \"employee_id\": 36}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(9,'default','created','App\\Models\\Room',9,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-009\", \"name\": \"Lab IPA\", \"type\": \"laboratorium\", \"floor\": \"Lantai 1\", \"building\": \"Gedung B\", \"capacity\": 30, \"condition\": \"baik\", \"employee_id\": 12}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(10,'default','created','App\\Models\\Room',10,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-010\", \"name\": \"Lab Komputer\", \"type\": \"laboratorium\", \"floor\": \"Lantai 2\", \"building\": \"Gedung B\", \"capacity\": 25, \"condition\": \"baik\", \"employee_id\": 37}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(11,'default','created','App\\Models\\Room',11,'created',NULL,NULL,'{\"attributes\": {\"code\": \"R-011\", \"name\": \"Lab Bahasa\", \"type\": \"laboratorium\", \"floor\": \"Lantai 1\", \"building\": \"Gedung B\", \"capacity\": 30, \"condition\": \"dalam_perbaikan\", \"employee_id\": null}}','[]','2026-08-27 01:47:25','2026-08-27 01:47:25'),(12,'ppdb','Pengaturan PPDB diperbarui (status: closed).',NULL,NULL,'updated','App\\Models\\User',3,'[]','[]','2026-08-27 18:29:11','2026-08-27 18:29:11'),(13,'default','created','App\\Models\\Student',29,'created','App\\Models\\User',3,'{\"attributes\": {\"nis\": null}}','[]','2026-08-27 18:31:28','2026-08-27 18:31:28'),(14,'default','updated','App\\Models\\PpdbRegistration',3,'updated','App\\Models\\User',3,'{\"old\": {\"status\": \"submitted\"}, \"attributes\": {\"status\": \"accepted\"}}','[]','2026-08-27 18:31:28','2026-08-27 18:31:28'),(15,'ppdb','PPDB diterima: MUHAMMAD FARHAN RAMADHAN (NIS & kelas diisi di Data Siswa)','App\\Models\\PpdbRegistration',3,'accepted','App\\Models\\User',3,'[]','{\"student_id\": 29}','2026-08-27 18:31:28','2026-08-27 18:31:28'),(16,'default','created','App\\Models\\Student',30,'created',NULL,NULL,'{\"attributes\": {\"nis\": \"251001\"}}','[]','2026-08-27 21:59:01','2026-08-27 21:59:01'),(17,'default','created','App\\Models\\Student',31,'created','App\\Models\\User',3,'{\"attributes\": {\"nis\": null}}','[]','2026-08-27 22:20:51','2026-08-27 22:20:51'),(18,'default','updated','App\\Models\\PpdbRegistration',2,'updated','App\\Models\\User',3,'{\"old\": {\"status\": \"submitted\"}, \"attributes\": {\"status\": \"accepted\"}}','[]','2026-08-27 22:20:51','2026-08-27 22:20:51'),(19,'ppdb','PPDB diterima: SITI NURHALIZA (data disalin ke Master Data Siswa; NIS & kelas diisi di Data Siswa)','App\\Models\\PpdbRegistration',2,'accepted','App\\Models\\User',3,'[]','{\"student_id\": 31}','2026-08-27 22:20:51','2026-08-27 22:20:51'),(20,'akademik','siswa_diubah','App\\Models\\Student',25,NULL,'App\\Models\\User',3,'[]','[]','2026-08-27 22:23:24','2026-08-27 22:23:24'),(21,'default','created','App\\Models\\MutasiRegistration',1,'created',NULL,NULL,'{\"attributes\": {\"status\": \"submitted\", \"kelas_asal\": \"VIII-A\", \"kelas_tujuan\": \"VIII-A\"}}','[]','2026-08-28 03:51:20','2026-08-28 03:51:20'),(22,'default','created','App\\Models\\MutasiRegistration',2,'created',NULL,NULL,'{\"attributes\": {\"status\": \"submitted\", \"kelas_asal\": \"VII-A\", \"kelas_tujuan\": \"VII-A\"}}','[]','2026-08-28 03:51:20','2026-08-28 03:51:20'),(23,'default','created','App\\Models\\MutasiRegistration',3,'created',NULL,NULL,'{\"attributes\": {\"status\": \"submitted\", \"kelas_asal\": \"IX-B\", \"kelas_tujuan\": \"IX-A\"}}','[]','2026-08-28 03:51:20','2026-08-28 03:51:20'),(24,'mutasi','Pengaturan Mutasi Masuk diperbarui (status: open).',NULL,NULL,'updated','App\\Models\\User',3,'[]','[]','2026-08-28 04:04:18','2026-08-28 04:04:18'),(25,'mutasi','Data pindahan diperbarui: BIMA ARDIANSYAH','App\\Models\\MutasiRegistration',3,'updated','App\\Models\\User',3,'[]','[]','2026-08-28 04:34:50','2026-08-28 04:34:50'),(26,'account_provisioning','Backfill: akun bendahara ditautkan ke pegawai Ratna Dewi, S.E.','App\\Models\\Employee',4,NULL,NULL,NULL,'[]','[]','2026-08-28 18:12:36','2026-08-28 18:12:36'),(27,'account_provisioning','Backfill: akun kepala ditautkan ke pegawai Drs. H. Ahmad Fauzi, M.Pd.','App\\Models\\Employee',1,NULL,NULL,NULL,'[]','[]','2026-08-28 18:12:36','2026-08-28 18:12:36'),(28,'auth','Kata sandi berhasil diubah.',NULL,NULL,NULL,'App\\Models\\User',3,'[]','[]','2026-08-28 18:21:22','2026-08-28 18:21:22'),(29,'default','updated','App\\Models\\Employee',1,'updated','App\\Models\\User',3,'{\"old\": {\"position_id\": 1, \"organizational_unit_id\": 1}, \"attributes\": {\"position_id\": 6, \"organizational_unit_id\": 7}}','[]','2026-08-28 18:42:58','2026-08-28 18:42:58'),(30,'kepegawaian','pegawai_diubah','App\\Models\\Employee',1,NULL,'App\\Models\\User',3,'[]','{\"nama\": \"Drs. H. Ahmad Fauzi, M.Pd.\"}','2026-08-28 18:42:58','2026-08-28 18:42:58'),(31,'default','created','App\\Models\\Employee',40,'created','App\\Models\\User',3,'{\"attributes\": {\"nip\": null, \"tmt\": \"2026-07-01T00:00:00.000000Z\", \"status\": \"aktif\", \"position_id\": 6, \"employee_status\": \"honor\", \"organizational_unit_id\": 7}}','[]','2026-08-28 18:48:34','2026-08-28 18:48:34'),(32,'kepegawaian','pegawai_baru','App\\Models\\Employee',40,NULL,'App\\Models\\User',3,'[]','{\"nama\": \"testing guru\"}','2026-08-28 18:48:34','2026-08-28 18:48:34'),(33,'account_provisioning','Akun pegawai dibuat otomatis: 1234567890123456','App\\Models\\Employee',40,NULL,'App\\Models\\User',3,'[]','[]','2026-08-28 18:48:34','2026-08-28 18:48:34'),(34,'default','updated','App\\Models\\Employee',39,'updated','App\\Models\\User',3,'{\"old\": {\"tmt\": null}, \"attributes\": {\"tmt\": \"2026-07-01T00:00:00.000000Z\"}}','[]','2026-08-28 18:49:59','2026-08-28 18:49:59'),(35,'kepegawaian','pegawai_diubah','App\\Models\\Employee',39,NULL,'App\\Models\\User',3,'[]','{\"nama\": \"ZAHRATUNNISA, S.Pd\"}','2026-08-28 18:49:59','2026-08-28 18:49:59'),(36,'account_provisioning','Akun pegawai dibuat otomatis: 198702102011012004','App\\Models\\Employee',2,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:35','2026-08-28 19:18:35'),(37,'account_provisioning','Akun pegawai dibuat otomatis: 3508160504990005','App\\Models\\Employee',5,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:35','2026-08-28 19:18:35'),(38,'account_provisioning','Akun pegawai dibuat otomatis: 3508193008980008','App\\Models\\Employee',8,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:35','2026-08-28 19:18:35'),(39,'account_provisioning','Akun pegawai dibuat otomatis: 197512122007012044','App\\Models\\Employee',9,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:35','2026-08-28 19:18:35'),(40,'account_provisioning','Akun pegawai dibuat otomatis: 197406071999032001','App\\Models\\Employee',10,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:36','2026-08-28 19:18:36'),(41,'account_provisioning','Akun pegawai dibuat otomatis: 196804081999032004','App\\Models\\Employee',11,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:36','2026-08-28 19:18:36'),(42,'account_provisioning','Akun pegawai dibuat otomatis: 197706212007011017','App\\Models\\Employee',12,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:36','2026-08-28 19:18:36'),(43,'account_provisioning','Akun pegawai dibuat otomatis: 197810051999031003','App\\Models\\Employee',13,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:36','2026-08-28 19:18:36'),(44,'account_provisioning','Akun pegawai dibuat otomatis: 197905062007102008','App\\Models\\Employee',14,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:37','2026-08-28 19:18:37'),(45,'account_provisioning','Akun pegawai dibuat otomatis: 196801121997032002','App\\Models\\Employee',15,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:37','2026-08-28 19:18:37'),(46,'account_provisioning','Akun pegawai dibuat otomatis: 198507122005012001','App\\Models\\Employee',16,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:37','2026-08-28 19:18:37'),(47,'account_provisioning','Akun pegawai dibuat otomatis: 197106122007012034','App\\Models\\Employee',17,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:37','2026-08-28 19:18:37'),(48,'account_provisioning','Akun pegawai dibuat otomatis: 198304252007102001','App\\Models\\Employee',18,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:38','2026-08-28 19:18:38'),(49,'account_provisioning','Akun pegawai dibuat otomatis: 4154747650300013','App\\Models\\Employee',19,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:38','2026-08-28 19:18:38'),(50,'account_provisioning','Akun pegawai dibuat otomatis: 5538748652200002','App\\Models\\Employee',20,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:38','2026-08-28 19:18:38'),(51,'account_provisioning','Akun pegawai dibuat otomatis: 3936743643200002','App\\Models\\Employee',21,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:38','2026-08-28 19:18:38'),(52,'account_provisioning','Akun pegawai dibuat otomatis: 1559763663200003','App\\Models\\Employee',22,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:39','2026-08-28 19:18:39'),(53,'account_provisioning','Akun pegawai dibuat otomatis: 6746760661300162','App\\Models\\Employee',23,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:39','2026-08-28 19:18:39'),(54,'account_provisioning','Akun pegawai dibuat otomatis: 6135764665200013','App\\Models\\Employee',24,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:39','2026-08-28 19:18:39'),(55,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000017','App\\Models\\Employee',25,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:39','2026-08-28 19:18:39'),(56,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000018','App\\Models\\Employee',26,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:40','2026-08-28 19:18:40'),(57,'account_provisioning','Akun pegawai dibuat otomatis: 5938767668200012','App\\Models\\Employee',27,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:40','2026-08-28 19:18:40'),(58,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000020','App\\Models\\Employee',28,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:40','2026-08-28 19:18:40'),(59,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000021','App\\Models\\Employee',29,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:41','2026-08-28 19:18:41'),(60,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000022','App\\Models\\Employee',30,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:41','2026-08-28 19:18:41'),(61,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000023','App\\Models\\Employee',31,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:41','2026-08-28 19:18:41'),(62,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000024','App\\Models\\Employee',32,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:41','2026-08-28 19:18:41'),(63,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000025','App\\Models\\Employee',33,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:42','2026-08-28 19:18:42'),(64,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000026','App\\Models\\Employee',34,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:42','2026-08-28 19:18:42'),(65,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000027','App\\Models\\Employee',35,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:42','2026-08-28 19:18:42'),(66,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000028','App\\Models\\Employee',36,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:42','2026-08-28 19:18:42'),(67,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000029','App\\Models\\Employee',37,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:43','2026-08-28 19:18:43'),(68,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000030','App\\Models\\Employee',38,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:43','2026-08-28 19:18:43'),(69,'account_provisioning','Akun pegawai dibuat otomatis: 6201000000000031','App\\Models\\Employee',39,NULL,NULL,NULL,'[]','[]','2026-08-28 19:18:43','2026-08-28 19:18:43');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agenda`
--

DROP TABLE IF EXISTS `agenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agenda` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agenda',
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `lokasi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penanggung_jawab` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci,
  `target` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publik',
  `tampil_mulai` date NOT NULL,
  `tampil_selesai` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agenda_created_by_foreign` (`created_by`),
  KEY `agenda_status_tampil_mulai_index` (`status`,`tampil_mulai`),
  CONSTRAINT `agenda_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agenda`
--

LOCK TABLES `agenda` WRITE;
/*!40000 ALTER TABLE `agenda` DISABLE KEYS */;
INSERT INTO `agenda` VALUES (1,'Rapat Dewan Guru — Pembagian Rapor','agenda','2026-08-30','14:00:00','Aula Madrasah','Wakamad Kurikulum','Pembahasan kelulusan dan persiapan pembagian rapor.','internal','2026-08-27',NULL,'aktif',9,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,'Asesmen Sumatif Semester Ganjil','pengumuman','2026-09-10',NULL,NULL,'Wakamad Kurikulum','Asesmen sumatif semester ganjil akan berlangsung dua pekan lagi. Siswa diharapkan mempersiapkan diri.','publik','2026-08-27',NULL,'aktif',9,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `agenda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `author_id` bigint unsigned DEFAULT NULL,
  `reviewer_id` bigint unsigned DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_author_id_foreign` (`author_id`),
  KEY `articles_reviewer_id_foreign` (`reviewer_id`),
  KEY `articles_status_published_at_index` (`status`,`published_at`),
  CONSTRAINT `articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `articles_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (1,'Kegiatan MPLS Tahun Ajaran 2026/2027 Sukses Dilaksanakan','kegiatan-mpls-tahun-ajaran-20262027-sukses-dilaksanakan','Rangkaian Masa Pengenalan Lingkungan Sekolah berjalan lancar dan penuh antusiasme.','Masa Pengenalan Lingkungan Sekolah (MPLS) Tahun Ajaran 2026/2027 telah sukses dilaksanakan.\n\nKegiatan diisi dengan pengenalan lingkungan madrasah, tata tertib, serta perkenalan dengan dewan guru dan staf. Orang tua siswa juga diberikan sesi informasi mengenai program madrasah selama satu tahun ke depan.','Kegiatan','mpls, kegiatan',NULL,'publish',9,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,'Penerimaan PPDB Gelombang Dua Dibuka','penerimaan-ppdb-gelombang-dua-dibuka','PPDB jalur reguler gelombang kedua dibuka mulai 10 September 2026.','Madrasah membuka Penerimaan Peserta Didik Baru (PPDB) gelombang kedua.\n\nPendaftaran dapat dilakukan secara daring melalui laman resmi madrasah. Persyaratan dan tata cara pendaftaran dapat dilihat pada menu PPDB.','PPDB','ppdb, pendaftaran',NULL,'publish',1,NULL,NULL,'2026-08-26 01:09:45','2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,'Inovasi Pembelajaran Berbasis Proyek','draft-inovasi-pembelajaran-berbasis-proyek',NULL,'Draft artikel ini menunggu dilengkapi oleh penulis.','Akademik',NULL,NULL,'draft',1,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_reviews`
--

DROP TABLE IF EXISTS `attendance_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_group_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `reviewed_by` bigint unsigned NOT NULL,
  `reviewed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_review_unique` (`class_group_id`,`attendance_date`),
  KEY `attendance_reviews_academic_year_id_foreign` (`academic_year_id`),
  KEY `attendance_reviews_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `attendance_reviews_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_reviews_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_reviews_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_reviews`
--

LOCK TABLES `attendance_reviews` WRITE;
/*!40000 ALTER TABLE `attendance_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `class_group_id` bigint unsigned NOT NULL,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `note` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_unique` (`student_enrollment_id`,`attendance_date`),
  KEY `attendances_academic_year_id_foreign` (`academic_year_id`),
  KEY `attendances_class_group_id_foreign` (`class_group_id`),
  KEY `attendances_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `attendances_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
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
INSERT INTO `cache` VALUES ('sim-madrasah-cache-boost:mcp:database-schema:mysql::1:0:0:0','a:2:{s:6:\"engine\";s:5:\"mysql\";s:6:\"tables\";a:81:{s:14:\"academic_years\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:11:\"varchar(20)\";s:8:\"semester\";s:11:\"varchar(10)\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:12:\"achievements\";a:16:{s:2:\"id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:5:\"jenis\";s:11:\"varchar(20)\";s:13:\"nama_kegiatan\";s:12:\"varchar(150)\";s:7:\"tingkat\";s:11:\"varchar(30)\";s:13:\"penyelenggara\";s:12:\"varchar(100)\";s:7:\"tanggal\";s:4:\"date\";s:9:\"peringkat\";s:11:\"varchar(50)\";s:10:\"pembimbing\";s:12:\"varchar(100)\";s:10:\"sertifikat\";s:12:\"varchar(255)\";s:4:\"foto\";s:12:\"varchar(255)\";s:17:\"status_verifikasi\";s:11:\"varchar(20)\";s:16:\"status_publikasi\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:12:\"activity_log\";a:12:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"log_name\";s:12:\"varchar(255)\";s:11:\"description\";s:4:\"text\";s:12:\"subject_type\";s:12:\"varchar(255)\";s:10:\"subject_id\";s:15:\"bigint unsigned\";s:5:\"event\";s:12:\"varchar(255)\";s:11:\"causer_type\";s:12:\"varchar(255)\";s:9:\"causer_id\";s:15:\"bigint unsigned\";s:17:\"attribute_changes\";s:4:\"json\";s:10:\"properties\";s:4:\"json\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:6:\"agenda\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"title\";s:12:\"varchar(150)\";s:5:\"jenis\";s:11:\"varchar(20)\";s:7:\"tanggal\";s:4:\"date\";s:5:\"waktu\";s:4:\"time\";s:6:\"lokasi\";s:12:\"varchar(100)\";s:16:\"penanggung_jawab\";s:12:\"varchar(100)\";s:3:\"isi\";s:4:\"text\";s:6:\"target\";s:11:\"varchar(20)\";s:12:\"tampil_mulai\";s:4:\"date\";s:14:\"tampil_selesai\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"articles\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"title\";s:12:\"varchar(200)\";s:4:\"slug\";s:12:\"varchar(220)\";s:7:\"summary\";s:12:\"varchar(300)\";s:4:\"body\";s:8:\"longtext\";s:8:\"category\";s:11:\"varchar(50)\";s:4:\"tags\";s:12:\"varchar(255)\";s:14:\"featured_image\";s:12:\"varchar(255)\";s:6:\"status\";s:11:\"varchar(20)\";s:9:\"author_id\";s:15:\"bigint unsigned\";s:11:\"reviewer_id\";s:15:\"bigint unsigned\";s:12:\"scheduled_at\";s:9:\"timestamp\";s:12:\"published_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:18:\"attendance_reviews\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:15:\"attendance_date\";s:4:\"date\";s:11:\"reviewed_by\";s:15:\"bigint unsigned\";s:11:\"reviewed_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"attendances\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:15:\"attendance_date\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(10)\";s:4:\"note\";s:4:\"text\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:5:\"cache\";a:3:{s:3:\"key\";s:12:\"varchar(255)\";s:5:\"value\";s:10:\"mediumtext\";s:10:\"expiration\";s:6:\"bigint\";}s:11:\"cache_locks\";a:3:{s:3:\"key\";s:12:\"varchar(255)\";s:5:\"owner\";s:12:\"varchar(255)\";s:10:\"expiration\";s:6:\"bigint\";}s:12:\"class_groups\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:11:\"varchar(20)\";s:11:\"grade_level\";s:11:\"varchar(10)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:19:\"counseling_sessions\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:17:\"counselor_user_id\";s:15:\"bigint unsigned\";s:12:\"session_date\";s:4:\"date\";s:15:\"counseling_type\";s:11:\"varchar(50)\";s:5:\"topic\";s:12:\"varchar(255)\";s:19:\"problem_description\";s:4:\"text\";s:17:\"assessment_result\";s:4:\"text\";s:12:\"action_taken\";s:4:\"text\";s:14:\"follow_up_plan\";s:4:\"text\";s:21:\"confidentiality_level\";s:11:\"varchar(30)\";s:6:\"status\";s:11:\"varchar(30)\";s:15:\"attachment_path\";s:12:\"varchar(255)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:20:\"employee_attendances\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:11:\"employee_id\";s:15:\"bigint unsigned\";s:15:\"attendance_date\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(20)\";s:8:\"clock_in\";s:4:\"time\";s:9:\"clock_out\";s:4:\"time\";s:4:\"note\";s:4:\"text\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:27:\"employee_position_histories\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:11:\"employee_id\";s:15:\"bigint unsigned\";s:11:\"position_id\";s:15:\"bigint unsigned\";s:22:\"organizational_unit_id\";s:15:\"bigint unsigned\";s:10:\"started_at\";s:4:\"date\";s:8:\"ended_at\";s:4:\"date\";s:6:\"reason\";s:11:\"varchar(60)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:9:\"employees\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:9:\"person_id\";s:15:\"bigint unsigned\";s:22:\"organizational_unit_id\";s:15:\"bigint unsigned\";s:11:\"position_id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:3:\"nip\";s:11:\"varchar(20)\";s:15:\"username_source\";s:11:\"varchar(10)\";s:15:\"employee_status\";s:11:\"varchar(20)\";s:6:\"status\";s:11:\"varchar(20)\";s:3:\"tmt\";s:4:\"date\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:10:\"deleted_at\";s:9:\"timestamp\";}s:27:\"extracurricular_attendances\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:18:\"extracurricular_id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:7:\"tanggal\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(10)\";s:8:\"predikat\";s:7:\"char(1)\";s:10:\"keterangan\";s:12:\"varchar(255)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:23:\"extracurricular_members\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:18:\"extracurricular_id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:17:\"tanggal_bergabung\";s:4:\"date\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"extracurriculars\";a:12:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(100)\";s:4:\"slug\";s:12:\"varchar(120)\";s:11:\"description\";s:4:\"text\";s:10:\"pembina_id\";s:15:\"bigint unsigned\";s:4:\"hari\";s:11:\"varchar(10)\";s:5:\"waktu\";s:4:\"time\";s:6:\"lokasi\";s:12:\"varchar(100)\";s:6:\"status\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"failed_jobs\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"uuid\";s:12:\"varchar(255)\";s:10:\"connection\";s:12:\"varchar(255)\";s:5:\"queue\";s:12:\"varchar(255)\";s:7:\"payload\";s:8:\"longtext\";s:9:\"exception\";s:8:\"longtext\";s:9:\"failed_at\";s:9:\"timestamp\";}s:16:\"guardian_student\";a:3:{s:11:\"guardian_id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:8:\"relation\";s:11:\"varchar(20)\";}s:9:\"guardians\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(100)\";s:3:\"nik\";s:11:\"varchar(16)\";s:6:\"status\";s:11:\"varchar(30)\";s:11:\"birth_place\";s:11:\"varchar(60)\";s:10:\"birth_date\";s:4:\"date\";s:9:\"education\";s:11:\"varchar(30)\";s:3:\"job\";s:11:\"varchar(30)\";s:6:\"income\";s:11:\"varchar(30)\";s:5:\"phone\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:20:\"homeroom_assignments\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:6:\"status\";s:11:\"varchar(30)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:20:\"inventory_categories\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:11:\"varchar(60)\";s:11:\"description\";s:4:\"text\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"inventory_items\";a:19:{s:2:\"id\";s:15:\"bigint unsigned\";s:11:\"category_id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(30)\";s:4:\"name\";s:12:\"varchar(120)\";s:5:\"brand\";s:11:\"varchar(60)\";s:5:\"model\";s:11:\"varchar(60)\";s:13:\"serial_number\";s:11:\"varchar(60)\";s:8:\"quantity\";s:12:\"int unsigned\";s:4:\"unit\";s:11:\"varchar(20)\";s:9:\"condition\";s:11:\"varchar(20)\";s:8:\"location\";s:12:\"varchar(100)\";s:13:\"purchase_date\";s:4:\"date\";s:14:\"purchase_price\";s:12:\"int unsigned\";s:6:\"status\";s:11:\"varchar(20)\";s:5:\"photo\";s:12:\"varchar(255)\";s:5:\"notes\";s:4:\"text\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:22:\"inventory_maintenances\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"item_id\";s:15:\"bigint unsigned\";s:4:\"type\";s:11:\"varchar(20)\";s:11:\"description\";s:4:\"text\";s:4:\"cost\";s:12:\"int unsigned\";s:10:\"start_date\";s:4:\"date\";s:8:\"end_date\";s:4:\"date\";s:10:\"technician\";s:12:\"varchar(100)\";s:6:\"status\";s:11:\"varchar(20)\";s:5:\"notes\";s:4:\"text\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:19:\"inventory_mutations\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"item_id\";s:15:\"bigint unsigned\";s:13:\"from_location\";s:12:\"varchar(100)\";s:11:\"to_location\";s:12:\"varchar(100)\";s:8:\"quantity\";s:12:\"int unsigned\";s:13:\"mutation_date\";s:4:\"date\";s:6:\"reason\";s:12:\"varchar(200)\";s:11:\"approved_by\";s:15:\"bigint unsigned\";s:6:\"status\";s:11:\"varchar(20)\";s:5:\"notes\";s:4:\"text\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"job_batches\";a:10:{s:2:\"id\";s:12:\"varchar(255)\";s:4:\"name\";s:12:\"varchar(255)\";s:10:\"total_jobs\";s:3:\"int\";s:12:\"pending_jobs\";s:3:\"int\";s:11:\"failed_jobs\";s:3:\"int\";s:14:\"failed_job_ids\";s:8:\"longtext\";s:7:\"options\";s:10:\"mediumtext\";s:12:\"cancelled_at\";s:3:\"int\";s:10:\"created_at\";s:3:\"int\";s:11:\"finished_at\";s:3:\"int\";}s:4:\"jobs\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"queue\";s:12:\"varchar(255)\";s:7:\"payload\";s:8:\"longtext\";s:8:\"attempts\";s:17:\"smallint unsigned\";s:11:\"reserved_at\";s:12:\"int unsigned\";s:12:\"available_at\";s:12:\"int unsigned\";s:10:\"created_at\";s:12:\"int unsigned\";}s:17:\"letter_categories\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(255)\";s:11:\"description\";s:4:\"text\";s:10:\"sort_order\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:7:\"letters\";a:17:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"type\";s:22:\"enum(\'masuk\',\'keluar\')\";s:6:\"number\";s:12:\"varchar(255)\";s:4:\"date\";s:4:\"date\";s:7:\"from_to\";s:12:\"varchar(255)\";s:7:\"subject\";s:12:\"varchar(255)\";s:11:\"description\";s:4:\"text\";s:6:\"status\";s:45:\"enum(\'diterima\',\'diproses\',\'selesai\',\'arsip\')\";s:8:\"priority\";s:42:\"enum(\'biasa\',\'penting\',\'segera\',\'rahasia\')\";s:8:\"category\";s:12:\"varchar(255)\";s:14:\"disposition_to\";s:12:\"varchar(255)\";s:16:\"disposition_note\";s:4:\"text\";s:8:\"file_url\";s:12:\"varchar(255)\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:13:\"library_books\";a:19:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(30)\";s:5:\"title\";s:12:\"varchar(200)\";s:6:\"author\";s:12:\"varchar(120)\";s:9:\"publisher\";s:12:\"varchar(120)\";s:4:\"year\";s:17:\"smallint unsigned\";s:11:\"category_id\";s:15:\"bigint unsigned\";s:4:\"isbn\";s:11:\"varchar(30)\";s:9:\"total_qty\";s:17:\"smallint unsigned\";s:13:\"available_qty\";s:17:\"smallint unsigned\";s:8:\"location\";s:12:\"varchar(100)\";s:11:\"cover_image\";s:12:\"varchar(255)\";s:8:\"is_ebook\";s:10:\"tinyint(1)\";s:9:\"ebook_url\";s:12:\"varchar(500)\";s:11:\"description\";s:4:\"text\";s:6:\"status\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:18:\"library_categories\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:11:\"varchar(60)\";s:11:\"description\";s:4:\"text\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:13:\"library_loans\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"book_id\";s:15:\"bigint unsigned\";s:9:\"member_id\";s:15:\"bigint unsigned\";s:9:\"loan_date\";s:4:\"date\";s:8:\"due_date\";s:4:\"date\";s:11:\"return_date\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(20)\";s:4:\"note\";s:4:\"text\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"library_members\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:11:\"member_type\";s:11:\"varchar(10)\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:11:\"employee_id\";s:15:\"bigint unsigned\";s:9:\"member_no\";s:11:\"varchar(20)\";s:4:\"name\";s:12:\"varchar(100)\";s:6:\"status\";s:11:\"varchar(20)\";s:9:\"joined_at\";s:4:\"date\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:12:\"media_albums\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"title\";s:12:\"varchar(150)\";s:4:\"slug\";s:12:\"varchar(170)\";s:8:\"kategori\";s:11:\"varchar(50)\";s:11:\"description\";s:4:\"text\";s:11:\"cover_image\";s:12:\"varchar(255)\";s:6:\"status\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"media_items\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"album_id\";s:15:\"bigint unsigned\";s:4:\"tipe\";s:11:\"varchar(10)\";s:9:\"file_path\";s:12:\"varchar(255)\";s:9:\"video_url\";s:12:\"varchar(255)\";s:7:\"caption\";s:12:\"varchar(255)\";s:10:\"sort_order\";s:16:\"tinyint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:10:\"migrations\";a:3:{s:2:\"id\";s:12:\"int unsigned\";s:9:\"migration\";s:12:\"varchar(255)\";s:5:\"batch\";s:3:\"int\";}s:16:\"mutasi_interests\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(100)\";s:5:\"phone\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:20:\"mutasi_registrations\";a:109:{s:2:\"id\";s:15:\"bigint unsigned\";s:15:\"registration_no\";s:11:\"varchar(20)\";s:6:\"status\";s:47:\"enum(\'draft\',\'submitted\',\'accepted\',\'rejected\')\";s:16:\"rejection_reason\";s:12:\"varchar(255)\";s:5:\"notes\";s:4:\"text\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:10:\"ip_address\";s:11:\"varchar(45)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:4:\"name\";s:12:\"varchar(100)\";s:3:\"nik\";s:11:\"varchar(16)\";s:4:\"nisn\";s:11:\"varchar(10)\";s:8:\"nis_asal\";s:11:\"varchar(20)\";s:6:\"gender\";s:10:\"varchar(1)\";s:8:\"religion\";s:11:\"varchar(20)\";s:11:\"birth_place\";s:11:\"varchar(60)\";s:10:\"birth_date\";s:4:\"date\";s:15:\"previous_school\";s:12:\"varchar(100)\";s:5:\"hobby\";s:11:\"varchar(60)\";s:8:\"ambition\";s:11:\"varchar(60)\";s:11:\"child_order\";s:16:\"tinyint unsigned\";s:13:\"sibling_count\";s:16:\"tinyint unsigned\";s:7:\"ever_tk\";s:11:\"varchar(30)\";s:9:\"ever_paud\";s:11:\"varchar(30)\";s:10:\"entry_date\";s:4:\"date\";s:13:\"origin_school\";s:12:\"varchar(100)\";s:10:\"origin_nsm\";s:11:\"varchar(12)\";s:11:\"origin_npsn\";s:10:\"varchar(8)\";s:14:\"origin_address\";s:12:\"varchar(255)\";s:10:\"kelas_asal\";s:11:\"varchar(20)\";s:12:\"kelas_tujuan\";s:11:\"varchar(20)\";s:13:\"alasan_pindah\";s:4:\"text\";s:14:\"tanggal_mutasi\";s:4:\"date\";s:7:\"address\";s:12:\"varchar(255)\";s:8:\"province\";s:11:\"varchar(60)\";s:4:\"city\";s:11:\"varchar(60)\";s:8:\"district\";s:11:\"varchar(60)\";s:7:\"village\";s:11:\"varchar(60)\";s:2:\"rt\";s:10:\"varchar(3)\";s:2:\"rw\";s:10:\"varchar(3)\";s:11:\"postal_code\";s:10:\"varchar(5)\";s:13:\"student_phone\";s:11:\"varchar(20)\";s:13:\"student_email\";s:12:\"varchar(100)\";s:11:\"father_name\";s:12:\"varchar(100)\";s:10:\"father_nik\";s:11:\"varchar(16)\";s:10:\"father_job\";s:11:\"varchar(30)\";s:12:\"father_phone\";s:11:\"varchar(20)\";s:11:\"mother_name\";s:12:\"varchar(100)\";s:10:\"mother_nik\";s:11:\"varchar(16)\";s:10:\"mother_job\";s:11:\"varchar(30)\";s:12:\"mother_phone\";s:11:\"varchar(20)\";s:13:\"guardian_name\";s:12:\"varchar(100)\";s:12:\"guardian_nik\";s:11:\"varchar(16)\";s:14:\"guardian_phone\";s:11:\"varchar(20)\";s:19:\"scanned_rekomendasi\";s:12:\"varchar(500)\";s:13:\"scanned_rapor\";s:12:\"varchar(500)\";s:10:\"scanned_kk\";s:12:\"varchar(500)\";s:12:\"scanned_akta\";s:12:\"varchar(500)\";s:13:\"scanned_photo\";s:12:\"varchar(500)\";s:8:\"imm_hepb\";s:10:\"tinyint(1)\";s:9:\"imm_polio\";s:10:\"tinyint(1)\";s:7:\"imm_bcg\";s:10:\"tinyint(1)\";s:10:\"imm_campak\";s:10:\"tinyint(1)\";s:7:\"imm_dpt\";s:10:\"tinyint(1)\";s:9:\"imm_covid\";s:10:\"tinyint(1)\";s:8:\"dis_deaf\";s:10:\"tinyint(1)\";s:9:\"dis_blind\";s:10:\"tinyint(1)\";s:12:\"dis_disabled\";s:10:\"tinyint(1)\";s:16:\"dis_intellectual\";s:10:\"tinyint(1)\";s:14:\"dis_behavioral\";s:10:\"tinyint(1)\";s:16:\"dis_slow_learner\";s:10:\"tinyint(1)\";s:17:\"dis_communication\";s:10:\"tinyint(1)\";s:10:\"dis_gifted\";s:10:\"tinyint(1)\";s:14:\"residence_type\";s:11:\"varchar(60)\";s:8:\"distance\";s:11:\"varchar(20)\";s:9:\"transport\";s:11:\"varchar(60)\";s:12:\"commute_time\";s:11:\"varchar(30)\";s:10:\"home_phone\";s:11:\"varchar(20)\";s:9:\"kk_number\";s:11:\"varchar(16)\";s:12:\"kk_head_name\";s:12:\"varchar(100)\";s:13:\"father_status\";s:11:\"varchar(30)\";s:18:\"father_birth_place\";s:11:\"varchar(60)\";s:17:\"father_birth_date\";s:4:\"date\";s:16:\"father_education\";s:11:\"varchar(30)\";s:13:\"father_income\";s:11:\"varchar(30)\";s:13:\"mother_status\";s:11:\"varchar(30)\";s:18:\"mother_birth_place\";s:11:\"varchar(60)\";s:17:\"mother_birth_date\";s:4:\"date\";s:16:\"mother_education\";s:11:\"varchar(30)\";s:13:\"mother_income\";s:11:\"varchar(30)\";s:19:\"guardian_birth_date\";s:4:\"date\";s:18:\"guardian_education\";s:11:\"varchar(30)\";s:12:\"guardian_job\";s:11:\"varchar(30)\";s:15:\"guardian_income\";s:11:\"varchar(30)\";s:10:\"social_kks\";s:11:\"varchar(30)\";s:10:\"social_pkh\";s:11:\"varchar(30)\";s:10:\"social_kip\";s:11:\"varchar(30)\";s:16:\"parent_ownership\";s:11:\"varchar(40)\";s:14:\"parent_address\";s:12:\"varchar(255)\";s:15:\"parent_province\";s:11:\"varchar(60)\";s:11:\"parent_city\";s:11:\"varchar(60)\";s:15:\"parent_district\";s:11:\"varchar(60)\";s:14:\"parent_village\";s:11:\"varchar(60)\";s:9:\"parent_rt\";s:10:\"varchar(3)\";s:9:\"parent_rw\";s:10:\"varchar(3)\";s:18:\"parent_postal_code\";s:10:\"varchar(5)\";s:15:\"scanned_kk_wali\";s:12:\"varchar(500)\";s:14:\"scanned_ijazah\";s:12:\"varchar(500)\";}s:8:\"offenses\";a:16:{s:2:\"id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:8:\"kategori\";s:11:\"varchar(50)\";s:7:\"tingkat\";s:11:\"varchar(20)\";s:4:\"poin\";s:16:\"tinyint unsigned\";s:16:\"tanggal_kejadian\";s:4:\"date\";s:9:\"kronologi\";s:4:\"text\";s:7:\"pelapor\";s:12:\"varchar(100)\";s:5:\"bukti\";s:12:\"varchar(255)\";s:8:\"tindakan\";s:12:\"varchar(255)\";s:16:\"pemanggilan_ortu\";s:10:\"tinyint(1)\";s:16:\"surat_peringatan\";s:11:\"varchar(10)\";s:19:\"status_penyelesaian\";s:11:\"varchar(20)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:20:\"organizational_units\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(10)\";s:4:\"name\";s:11:\"varchar(60)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:21:\"password_reset_tokens\";a:3:{s:5:\"email\";s:12:\"varchar(255)\";s:5:\"token\";s:12:\"varchar(255)\";s:10:\"created_at\";s:9:\"timestamp\";}s:17:\"pembiasaan_materi\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"modul\";s:11:\"varchar(10)\";s:7:\"no_urut\";s:17:\"smallint unsigned\";s:11:\"nama_materi\";s:12:\"varchar(150)\";s:5:\"jenis\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:25:\"pembiasaan_materi_periode\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:9:\"materi_id\";s:15:\"bigint unsigned\";s:5:\"kelas\";s:16:\"tinyint unsigned\";s:8:\"semester\";s:16:\"tinyint unsigned\";s:5:\"aktif\";s:10:\"tinyint(1)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"pembiasaan_nilai\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"siswa_id\";s:15:\"bigint unsigned\";s:9:\"materi_id\";s:15:\"bigint unsigned\";s:5:\"kelas\";s:16:\"tinyint unsigned\";s:8:\"semester\";s:16:\"tinyint unsigned\";s:15:\"tahun_pelajaran\";s:11:\"varchar(20)\";s:5:\"nilai\";s:16:\"tinyint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:6:\"people\";a:20:{s:2:\"id\";s:15:\"bigint unsigned\";s:3:\"nik\";s:11:\"varchar(16)\";s:4:\"name\";s:12:\"varchar(100)\";s:6:\"gender\";s:10:\"varchar(1)\";s:8:\"religion\";s:11:\"varchar(20)\";s:11:\"birth_place\";s:11:\"varchar(60)\";s:10:\"birth_date\";s:4:\"date\";s:5:\"phone\";s:11:\"varchar(20)\";s:5:\"email\";s:12:\"varchar(100)\";s:7:\"address\";s:12:\"varchar(255)\";s:8:\"province\";s:11:\"varchar(60)\";s:4:\"city\";s:11:\"varchar(60)\";s:8:\"district\";s:11:\"varchar(60)\";s:7:\"village\";s:11:\"varchar(60)\";s:2:\"rt\";s:10:\"varchar(3)\";s:2:\"rw\";s:10:\"varchar(3)\";s:11:\"postal_code\";s:10:\"varchar(5)\";s:10:\"home_phone\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:9:\"positions\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(20)\";s:4:\"name\";s:11:\"varchar(60)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:14:\"ppdb_interests\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(100)\";s:5:\"phone\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:18:\"ppdb_registrations\";a:107:{s:2:\"id\";s:15:\"bigint unsigned\";s:15:\"registration_no\";s:11:\"varchar(20)\";s:6:\"status\";s:47:\"enum(\'draft\',\'submitted\',\'accepted\',\'rejected\')\";s:16:\"rejection_reason\";s:12:\"varchar(255)\";s:5:\"notes\";s:4:\"text\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:10:\"ip_address\";s:11:\"varchar(45)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:4:\"name\";s:12:\"varchar(100)\";s:3:\"nik\";s:11:\"varchar(16)\";s:4:\"nisn\";s:11:\"varchar(10)\";s:6:\"gender\";s:10:\"varchar(1)\";s:8:\"religion\";s:11:\"varchar(20)\";s:11:\"birth_place\";s:11:\"varchar(60)\";s:10:\"birth_date\";s:4:\"date\";s:15:\"previous_school\";s:12:\"varchar(100)\";s:5:\"hobby\";s:11:\"varchar(60)\";s:8:\"ambition\";s:11:\"varchar(60)\";s:11:\"child_order\";s:16:\"tinyint unsigned\";s:13:\"sibling_count\";s:16:\"tinyint unsigned\";s:7:\"ever_tk\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:9:\"ever_paud\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:10:\"entry_date\";s:4:\"date\";s:10:\"scanned_kk\";s:12:\"varchar(500)\";s:15:\"scanned_kk_wali\";s:12:\"varchar(500)\";s:12:\"scanned_akta\";s:12:\"varchar(500)\";s:14:\"scanned_ijazah\";s:12:\"varchar(500)\";s:13:\"scanned_photo\";s:12:\"varchar(500)\";s:8:\"imm_hepb\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:9:\"imm_polio\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:7:\"imm_bcg\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:10:\"imm_campak\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:7:\"imm_dpt\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:9:\"imm_covid\";s:22:\"enum(\'PERNAH\',\'TIDAK\')\";s:8:\"dis_deaf\";s:10:\"tinyint(1)\";s:9:\"dis_blind\";s:10:\"tinyint(1)\";s:12:\"dis_disabled\";s:10:\"tinyint(1)\";s:16:\"dis_intellectual\";s:10:\"tinyint(1)\";s:14:\"dis_behavioral\";s:10:\"tinyint(1)\";s:16:\"dis_slow_learner\";s:10:\"tinyint(1)\";s:17:\"dis_communication\";s:10:\"tinyint(1)\";s:10:\"dis_gifted\";s:10:\"tinyint(1)\";s:14:\"residence_type\";s:11:\"varchar(60)\";s:7:\"address\";s:12:\"varchar(255)\";s:8:\"province\";s:11:\"varchar(60)\";s:4:\"city\";s:11:\"varchar(60)\";s:8:\"district\";s:11:\"varchar(60)\";s:7:\"village\";s:11:\"varchar(60)\";s:2:\"rt\";s:10:\"varchar(3)\";s:2:\"rw\";s:10:\"varchar(3)\";s:11:\"postal_code\";s:10:\"varchar(5)\";s:8:\"distance\";s:11:\"varchar(20)\";s:9:\"transport\";s:11:\"varchar(60)\";s:12:\"commute_time\";s:11:\"varchar(30)\";s:10:\"home_phone\";s:11:\"varchar(20)\";s:13:\"student_phone\";s:11:\"varchar(20)\";s:13:\"student_email\";s:12:\"varchar(100)\";s:9:\"kk_number\";s:11:\"varchar(16)\";s:12:\"kk_head_name\";s:12:\"varchar(100)\";s:11:\"father_name\";s:12:\"varchar(100)\";s:13:\"father_status\";s:11:\"varchar(30)\";s:10:\"father_nik\";s:11:\"varchar(16)\";s:18:\"father_birth_place\";s:11:\"varchar(60)\";s:17:\"father_birth_date\";s:4:\"date\";s:16:\"father_education\";s:11:\"varchar(30)\";s:10:\"father_job\";s:11:\"varchar(30)\";s:13:\"father_income\";s:11:\"varchar(30)\";s:12:\"father_phone\";s:11:\"varchar(20)\";s:11:\"mother_name\";s:12:\"varchar(100)\";s:13:\"mother_status\";s:11:\"varchar(30)\";s:10:\"mother_nik\";s:11:\"varchar(16)\";s:18:\"mother_birth_place\";s:11:\"varchar(60)\";s:17:\"mother_birth_date\";s:4:\"date\";s:16:\"mother_education\";s:11:\"varchar(30)\";s:10:\"mother_job\";s:11:\"varchar(30)\";s:13:\"mother_income\";s:11:\"varchar(30)\";s:12:\"mother_phone\";s:11:\"varchar(20)\";s:13:\"guardian_name\";s:12:\"varchar(100)\";s:12:\"guardian_nik\";s:11:\"varchar(16)\";s:20:\"guardian_birth_place\";s:11:\"varchar(60)\";s:19:\"guardian_birth_date\";s:4:\"date\";s:18:\"guardian_education\";s:11:\"varchar(30)\";s:12:\"guardian_job\";s:11:\"varchar(30)\";s:15:\"guardian_income\";s:11:\"varchar(30)\";s:14:\"guardian_phone\";s:11:\"varchar(20)\";s:10:\"social_kks\";s:11:\"varchar(30)\";s:10:\"social_pkh\";s:11:\"varchar(30)\";s:10:\"social_kip\";s:11:\"varchar(30)\";s:16:\"parent_ownership\";s:11:\"varchar(40)\";s:14:\"parent_address\";s:12:\"varchar(255)\";s:15:\"parent_province\";s:11:\"varchar(60)\";s:11:\"parent_city\";s:11:\"varchar(60)\";s:15:\"parent_district\";s:11:\"varchar(60)\";s:14:\"parent_village\";s:11:\"varchar(60)\";s:9:\"parent_rt\";s:10:\"varchar(3)\";s:9:\"parent_rw\";s:10:\"varchar(3)\";s:18:\"parent_postal_code\";s:10:\"varchar(5)\";s:13:\"origin_school\";s:12:\"varchar(100)\";s:10:\"origin_nsm\";s:11:\"varchar(12)\";s:11:\"origin_npsn\";s:10:\"varchar(8)\";s:14:\"origin_address\";s:12:\"varchar(255)\";s:5:\"kelas\";s:11:\"varchar(10)\";s:6:\"rombel\";s:11:\"varchar(10)\";s:8:\"nis_nism\";s:11:\"varchar(18)\";s:9:\"nis_last6\";s:10:\"varchar(6)\";}s:17:\"ppi_exam_archives\";a:16:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:4:\"nisn\";s:11:\"varchar(20)\";s:10:\"nama_siswa\";s:12:\"varchar(150)\";s:7:\"rata_p1\";s:12:\"decimal(5,2)\";s:7:\"rata_p2\";s:12:\"decimal(5,2)\";s:7:\"rata_p3\";s:12:\"decimal(5,2)\";s:12:\"rata_hafalan\";s:12:\"decimal(5,2)\";s:11:\"nilai_akhir\";s:12:\"decimal(5,2)\";s:8:\"predikat\";s:11:\"varchar(10)\";s:9:\"deskripsi\";s:12:\"varchar(255)\";s:12:\"status_lulus\";s:11:\"varchar(10)\";s:4:\"rank\";s:11:\"varchar(20)\";s:6:\"rombel\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:26:\"ppi_exam_aspect_categories\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:4:\"kode\";s:11:\"varchar(10)\";s:4:\"nama\";s:12:\"varchar(150)\";s:14:\"penguji_urutan\";s:16:\"tinyint unsigned\";s:6:\"urutan\";s:17:\"smallint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"ppi_exam_aspects\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:18:\"aspect_category_id\";s:15:\"bigint unsigned\";s:4:\"kode\";s:11:\"varchar(10)\";s:4:\"nama\";s:12:\"varchar(150)\";s:6:\"urutan\";s:17:\"smallint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:18:\"ppi_exam_examiners\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:12:\"exam_room_id\";s:15:\"bigint unsigned\";s:11:\"employee_id\";s:15:\"bigint unsigned\";s:6:\"urutan\";s:16:\"tinyint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"ppi_exam_groups\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:4:\"nama\";s:12:\"varchar(100)\";s:22:\"pembimbing_employee_id\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:23:\"ppi_exam_hafalan_materi\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:4:\"nama\";s:12:\"varchar(150)\";s:6:\"urutan\";s:17:\"smallint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:23:\"ppi_exam_hafalan_scores\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"participant_id\";s:15:\"bigint unsigned\";s:17:\"hafalan_materi_id\";s:15:\"bigint unsigned\";s:5:\"nilai\";s:16:\"tinyint unsigned\";s:13:\"tanggal_setor\";s:4:\"date\";s:24:\"dinilai_oleh_employee_id\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:21:\"ppi_exam_participants\";a:24:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:12:\"exam_room_id\";s:15:\"bigint unsigned\";s:8:\"group_id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:7:\"no_urut\";s:17:\"smallint unsigned\";s:6:\"status\";s:11:\"varchar(20)\";s:9:\"jumlah_p1\";s:17:\"smallint unsigned\";s:7:\"rata_p1\";s:12:\"decimal(5,2)\";s:9:\"jumlah_p2\";s:17:\"smallint unsigned\";s:7:\"rata_p2\";s:12:\"decimal(5,2)\";s:9:\"jumlah_p3\";s:17:\"smallint unsigned\";s:7:\"rata_p3\";s:12:\"decimal(5,2)\";s:18:\"jumlah_ujian_lisan\";s:17:\"smallint unsigned\";s:16:\"rata_ujian_lisan\";s:12:\"decimal(5,2)\";s:12:\"rata_hafalan\";s:12:\"decimal(5,2)\";s:11:\"nilai_akhir\";s:12:\"decimal(5,2)\";s:18:\"predicate_scale_id\";s:15:\"bigint unsigned\";s:12:\"status_lulus\";s:10:\"tinyint(1)\";s:10:\"rank_total\";s:17:\"smallint unsigned\";s:10:\"rank_lokal\";s:17:\"smallint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"ppi_exam_periods\";a:16:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:5:\"judul\";s:12:\"varchar(150)\";s:21:\"tanggal_setoran_mulai\";s:4:\"date\";s:23:\"tanggal_setoran_selesai\";s:4:\"date\";s:13:\"tanggal_ujian\";s:4:\"date\";s:6:\"status\";s:11:\"varchar(20)\";s:16:\"config_locked_at\";s:9:\"timestamp\";s:8:\"bobot_p1\";s:16:\"tinyint unsigned\";s:8:\"bobot_p2\";s:16:\"tinyint unsigned\";s:8:\"bobot_p3\";s:16:\"tinyint unsigned\";s:13:\"bobot_hafalan\";s:16:\"tinyint unsigned\";s:7:\"teks_mc\";s:4:\"text\";s:7:\"teks_ba\";s:4:\"text\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:25:\"ppi_exam_predicate_scales\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:8:\"predikat\";s:11:\"varchar(10)\";s:9:\"nilai_min\";s:16:\"tinyint unsigned\";s:9:\"nilai_max\";s:16:\"tinyint unsigned\";s:9:\"deskripsi\";s:12:\"varchar(255)\";s:14:\"is_tidak_lulus\";s:10:\"tinyint(1)\";s:6:\"urutan\";s:17:\"smallint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:14:\"ppi_exam_rooms\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"exam_period_id\";s:15:\"bigint unsigned\";s:4:\"nama\";s:12:\"varchar(100)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"ppi_exam_scores\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:14:\"participant_id\";s:15:\"bigint unsigned\";s:9:\"aspect_id\";s:15:\"bigint unsigned\";s:5:\"nilai\";s:16:\"tinyint unsigned\";s:20:\"examiner_employee_id\";s:15:\"bigint unsigned\";s:8:\"input_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:12:\"report_items\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:9:\"report_id\";s:15:\"bigint unsigned\";s:12:\"subject_code\";s:11:\"varchar(10)\";s:12:\"subject_name\";s:11:\"varchar(60)\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:10:\"class_name\";s:11:\"varchar(20)\";s:12:\"teacher_name\";s:12:\"varchar(100)\";s:5:\"score\";s:16:\"tinyint unsigned\";s:10:\"sort_order\";s:16:\"tinyint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:7:\"reports\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:8:\"semester\";s:11:\"varchar(10)\";s:8:\"snapshot\";s:4:\"json\";s:8:\"pdf_path\";s:12:\"varchar(255)\";s:6:\"status\";s:11:\"varchar(20)\";s:7:\"version\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:5:\"rooms\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(20)\";s:4:\"name\";s:12:\"varchar(100)\";s:4:\"type\";s:11:\"varchar(20)\";s:8:\"building\";s:11:\"varchar(60)\";s:5:\"floor\";s:11:\"varchar(20)\";s:8:\"capacity\";s:17:\"smallint unsigned\";s:11:\"employee_id\";s:15:\"bigint unsigned\";s:9:\"condition\";s:11:\"varchar(20)\";s:11:\"description\";s:4:\"text\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:14:\"schedule_cells\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:17:\"schedule_model_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:3:\"day\";s:11:\"varchar(10)\";s:9:\"period_no\";s:16:\"tinyint unsigned\";s:10:\"teacher_id\";s:15:\"bigint unsigned\";s:10:\"subject_id\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:27:\"schedule_model_grade_levels\";a:2:{s:17:\"schedule_model_id\";s:15:\"bigint unsigned\";s:11:\"grade_level\";s:11:\"varchar(10)\";}s:20:\"schedule_model_slots\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:17:\"schedule_model_id\";s:15:\"bigint unsigned\";s:9:\"period_no\";s:16:\"tinyint unsigned\";s:10:\"start_time\";s:4:\"time\";s:8:\"end_time\";s:4:\"time\";s:8:\"is_break\";s:10:\"tinyint(1)\";s:5:\"label\";s:11:\"varchar(40)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"schedule_models\";a:9:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:4:\"name\";s:11:\"varchar(80)\";s:10:\"start_time\";s:4:\"time\";s:17:\"max_hours_per_day\";s:16:\"tinyint unsigned\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:6:\"scores\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:10:\"subject_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:8:\"semester\";s:11:\"varchar(10)\";s:5:\"score\";s:16:\"tinyint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"sessions\";a:6:{s:2:\"id\";s:12:\"varchar(255)\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"ip_address\";s:11:\"varchar(45)\";s:10:\"user_agent\";s:4:\"text\";s:7:\"payload\";s:8:\"longtext\";s:13:\"last_activity\";s:3:\"int\";}s:8:\"settings\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:3:\"key\";s:12:\"varchar(100)\";s:5:\"value\";s:4:\"text\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:19:\"student_enrollments\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:6:\"status\";s:11:\"varchar(20)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:17:\"student_mutations\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:14:\"tanggal_mutasi\";s:4:\"date\";s:14:\"sekolah_tujuan\";s:12:\"varchar(100)\";s:10:\"tujuan_nsm\";s:11:\"varchar(12)\";s:11:\"tujuan_npsn\";s:10:\"varchar(8)\";s:13:\"alasan_pindah\";s:11:\"varchar(30)\";s:10:\"keterangan\";s:4:\"text\";s:8:\"no_surat\";s:12:\"varchar(100)\";s:10:\"created_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"students\";a:53:{s:2:\"id\";s:15:\"bigint unsigned\";s:9:\"person_id\";s:15:\"bigint unsigned\";s:3:\"nis\";s:11:\"varchar(20)\";s:4:\"nisn\";s:11:\"varchar(10)\";s:15:\"previous_school\";s:12:\"varchar(100)\";s:13:\"origin_school\";s:12:\"varchar(100)\";s:10:\"origin_nsm\";s:11:\"varchar(12)\";s:11:\"origin_npsn\";s:10:\"varchar(8)\";s:14:\"origin_address\";s:12:\"varchar(255)\";s:10:\"entry_date\";s:4:\"date\";s:5:\"hobby\";s:11:\"varchar(60)\";s:8:\"ambition\";s:11:\"varchar(60)\";s:11:\"child_order\";s:16:\"tinyint unsigned\";s:13:\"sibling_count\";s:16:\"tinyint unsigned\";s:7:\"ever_tk\";s:11:\"varchar(30)\";s:9:\"ever_paud\";s:11:\"varchar(30)\";s:14:\"residence_type\";s:11:\"varchar(60)\";s:8:\"distance\";s:11:\"varchar(20)\";s:9:\"transport\";s:11:\"varchar(60)\";s:12:\"commute_time\";s:11:\"varchar(30)\";s:9:\"kk_number\";s:11:\"varchar(16)\";s:12:\"kk_head_name\";s:12:\"varchar(100)\";s:10:\"social_kks\";s:11:\"varchar(30)\";s:10:\"social_pkh\";s:11:\"varchar(30)\";s:10:\"social_kip\";s:11:\"varchar(30)\";s:16:\"parent_ownership\";s:11:\"varchar(40)\";s:14:\"parent_address\";s:12:\"varchar(255)\";s:15:\"parent_province\";s:11:\"varchar(60)\";s:11:\"parent_city\";s:11:\"varchar(60)\";s:15:\"parent_district\";s:11:\"varchar(60)\";s:14:\"parent_village\";s:11:\"varchar(60)\";s:9:\"parent_rt\";s:10:\"varchar(3)\";s:9:\"parent_rw\";s:10:\"varchar(3)\";s:18:\"parent_postal_code\";s:10:\"varchar(5)\";s:4:\"name\";s:12:\"varchar(100)\";s:6:\"gender\";s:10:\"varchar(1)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:8:\"imm_hepb\";s:10:\"tinyint(1)\";s:9:\"imm_polio\";s:10:\"tinyint(1)\";s:7:\"imm_bcg\";s:10:\"tinyint(1)\";s:10:\"imm_campak\";s:10:\"tinyint(1)\";s:7:\"imm_dpt\";s:10:\"tinyint(1)\";s:9:\"imm_covid\";s:10:\"tinyint(1)\";s:8:\"dis_deaf\";s:10:\"tinyint(1)\";s:9:\"dis_blind\";s:10:\"tinyint(1)\";s:12:\"dis_disabled\";s:10:\"tinyint(1)\";s:16:\"dis_intellectual\";s:10:\"tinyint(1)\";s:14:\"dis_behavioral\";s:10:\"tinyint(1)\";s:16:\"dis_slow_learner\";s:10:\"tinyint(1)\";s:17:\"dis_communication\";s:10:\"tinyint(1)\";s:10:\"dis_gifted\";s:10:\"tinyint(1)\";s:9:\"documents\";s:4:\"json\";}s:8:\"subjects\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:11:\"varchar(10)\";s:4:\"name\";s:11:\"varchar(60)\";s:10:\"sort_order\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:19:\"teacher_assignments\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:14:\"class_group_id\";s:15:\"bigint unsigned\";s:10:\"subject_id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:17:\"teaching_journals\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:21:\"teacher_assignment_id\";s:15:\"bigint unsigned\";s:12:\"journal_date\";s:4:\"date\";s:9:\"period_no\";s:16:\"tinyint unsigned\";s:6:\"materi\";s:4:\"text\";s:6:\"tujuan\";s:4:\"text\";s:6:\"metode\";s:12:\"varchar(100)\";s:7:\"catatan\";s:4:\"text\";s:13:\"tindak_lanjut\";s:4:\"text\";s:8:\"lampiran\";s:12:\"varchar(255)\";s:6:\"status\";s:11:\"varchar(10)\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:17:\"tuition_overrides\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:7:\"nominal\";s:12:\"int unsigned\";s:10:\"keterangan\";s:12:\"varchar(255)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"tuition_payments\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:21:\"student_enrollment_id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:8:\"semester\";s:11:\"varchar(10)\";s:5:\"bulan\";s:16:\"tinyint unsigned\";s:7:\"nominal\";s:12:\"int unsigned\";s:6:\"status\";s:11:\"varchar(20)\";s:13:\"tanggal_bayar\";s:4:\"date\";s:6:\"metode\";s:11:\"varchar(30)\";s:7:\"catatan\";s:12:\"varchar(255)\";s:11:\"recorded_by\";s:15:\"bigint unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"tuition_settings\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:16:\"academic_year_id\";s:15:\"bigint unsigned\";s:7:\"nominal\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:10:\"user_roles\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:4:\"role\";s:11:\"varchar(30)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:5:\"users\";a:14:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(255)\";s:8:\"username\";s:12:\"varchar(255)\";s:4:\"role\";s:11:\"varchar(30)\";s:10:\"student_id\";s:15:\"bigint unsigned\";s:20:\"must_change_password\";s:10:\"tinyint(1)\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:5:\"email\";s:12:\"varchar(255)\";s:17:\"email_verified_at\";s:9:\"timestamp\";s:8:\"password\";s:12:\"varchar(255)\";s:14:\"remember_token\";s:12:\"varchar(100)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:10:\"deleted_at\";s:9:\"timestamp\";}}}',1787976191),('sim-madrasah-cache-roster:project:v3:3f0b7837a2600687140225cc3e441927','O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:46:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:139:{s:19:\"bacon/bacon-qr-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"bacon/bacon-qr-code\";s:10:\"\0*\0version\";s:5:\"2.0.8\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/bacon/bacon-qr-code\";}s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/barryvdh/laravel-dompdf\";}s:24:\"blade-ui-kit/blade-icons\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"blade-ui-kit/blade-icons\";s:10:\"\0*\0version\";s:6:\"1.10.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.10\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/blade-ui-kit/blade-icons\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:6:\"0.18.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/brick/math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/carbonphp/carbon-doctrine-types\";}s:13:\"composer/pcre\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"composer/pcre\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/composer/pcre\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.4.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/composer/semver\";}s:12:\"dasprid/enum\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"dasprid/enum\";s:10:\"\0*\0version\";s:5:\"1.0.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dasprid/enum\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dflydev/dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/doctrine/inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/doctrine/lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dompdf/dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dompdf/php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dompdf/php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/dragonmantank/cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/egulias/email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/fruitcake/php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.1.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/graham-campbell/result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/guzzlehttp/guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/guzzlehttp/promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/guzzlehttp/psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/guzzlehttp/uri-template\";}s:30:\"jean85/pretty-package-versions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"jean85/pretty-package-versions\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/jean85/pretty-package-versions\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.26.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:6:\"2.0.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:81:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/league/uri-interfaces\";}s:17:\"maatwebsite/excel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"maatwebsite/excel\";s:10:\"\0*\0version\";s:5:\"4.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^4.0\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/maatwebsite/excel\";}s:23:\"maennchen/zipstream-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"maennchen/zipstream-php\";s:10:\"\0*\0version\";s:5:\"3.2.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/maennchen/zipstream-php\";}s:17:\"markbaker/complex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"markbaker/complex\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/markbaker/complex\";}s:16:\"markbaker/matrix\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"markbaker/matrix\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/markbaker/matrix\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/masterminds/html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/monolog/monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.13.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nesbot/carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nette/schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nette/utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.8.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nikic/php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nunomaduro/termwind\";}s:11:\"nyholm/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nyholm/psr7\";s:10:\"\0*\0version\";s:5:\"1.8.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nyholm/psr7\";}s:24:\"phpoffice/phpspreadsheet\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"phpoffice/phpspreadsheet\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpoffice/phpspreadsheet\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:5:\"1.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpoption/phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psr/simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/psy/psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/ramsey/collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/ramsey/uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sabberworm/php-css-parser\";}s:13:\"sentry/sentry\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"sentry/sentry\";s:10:\"\0*\0version\";s:6:\"4.30.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sentry/sentry\";}s:21:\"sentry/sentry-laravel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sentry/sentry-laravel\";s:10:\"\0*\0version\";s:6:\"4.27.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^4.27\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sentry/sentry-laravel\";}s:30:\"simplesoftwareio/simple-qrcode\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"simplesoftwareio/simple-qrcode\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^4.2\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/simplesoftwareio/simple-qrcode\";}s:26:\"spatie/laravel-activitylog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"spatie/laravel-activitylog\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^5.1\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/spatie/laravel-activitylog\";}s:28:\"spatie/laravel-package-tools\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"spatie/laravel-package-tools\";s:10:\"\0*\0version\";s:6:\"1.93.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:81:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/spatie/laravel-package-tools\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"8.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:87:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/mime\";}s:24:\"symfony/options-resolver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/options-resolver\";s:10:\"\0*\0version\";s:5:\"8.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/options-resolver\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.38.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:85:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.38.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-php82\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-php86\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/process\";}s:31:\"symfony/psr-http-message-bridge\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"symfony/psr-http-message-bridge\";s:10:\"\0*\0version\";s:5:\"8.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/psr-http-message-bridge\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/var-dumper\";}s:21:\"thecodingmachine/safe\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"thecodingmachine/safe\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/thecodingmachine/safe\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/tijsverkoyen/css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.6.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/vlucas/phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/voku/portable-ascii\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/fakerphp/faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/filp/whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/hamcrest/hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:5:\"2.5.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^2.5\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.30.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/laravel/roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/mockery/mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/myclabs/deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/nunomaduro/collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phar-io/manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phar-io/version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.33\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/phpunit/phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/sebastian/version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:81:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/staabm/side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/symfony/yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/vendor/theseer/tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:139:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;i:127;r:1021;i:128;r:1029;i:129;r:1037;i:130;r:1045;i:131;r:1053;i:132;r:1061;i:133;r:1069;i:134;r:1077;i:135;r:1085;i:136;r:1093;i:137;r:1101;i:138;r:1109;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:383:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.0\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@alcalzone/ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@laravel/multiplex\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.41\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@vue/reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.41\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@vue/shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.16.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^3.16.2\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/alpinejs\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"6.2.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.51.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-east-asian-width\";}s:9:\"heroicons\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"heroicons\";s:10:\"\0*\0version\";s:5:\"2.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^2.2.0\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/heroicons\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/patch-console\";}s:15:\"playwright-core\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"playwright-core\";s:10:\"\0*\0version\";s:6:\"1.62.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^1.62.1\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/playwright-core\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.2.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/slice-ansi\";}s:10:\"sortablejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"sortablejs\";s:10:\"\0*\0version\";s:6:\"1.15.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^1.15.7\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/sortablejs\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:5:\"5.8.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.21.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yoga-layout\";}s:17:\"@babel/code-frame\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@babel/code-frame\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/code-frame\";}s:18:\"@babel/compat-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@babel/compat-data\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/compat-data\";}s:11:\"@babel/core\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@babel/core\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/core\";}s:16:\"@babel/generator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@babel/generator\";s:10:\"\0*\0version\";s:6:\"7.29.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/generator\";}s:30:\"@babel/helper-annotate-as-pure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@babel/helper-annotate-as-pure\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:89:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-annotate-as-pure\";}s:33:\"@babel/helper-compilation-targets\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@babel/helper-compilation-targets\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-compilation-targets\";}s:42:\"@babel/helper-create-class-features-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:42:\"@babel/helper-create-class-features-plugin\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:101:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-create-class-features-plugin\";}s:21:\"@babel/helper-globals\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@babel/helper-globals\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-globals\";}s:44:\"@babel/helper-member-expression-to-functions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:44:\"@babel/helper-member-expression-to-functions\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:103:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-member-expression-to-functions\";}s:28:\"@babel/helper-module-imports\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@babel/helper-module-imports\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:87:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-module-imports\";}s:31:\"@babel/helper-module-transforms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@babel/helper-module-transforms\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:90:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-module-transforms\";}s:38:\"@babel/helper-optimise-call-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@babel/helper-optimise-call-expression\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:97:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-optimise-call-expression\";}s:26:\"@babel/helper-plugin-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"@babel/helper-plugin-utils\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:85:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-plugin-utils\";}s:28:\"@babel/helper-replace-supers\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@babel/helper-replace-supers\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:87:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-replace-supers\";}s:50:\"@babel/helper-skip-transparent-expression-wrappers\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:50:\"@babel/helper-skip-transparent-expression-wrappers\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:109:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-skip-transparent-expression-wrappers\";}s:27:\"@babel/helper-string-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@babel/helper-string-parser\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-string-parser\";}s:34:\"@babel/helper-validator-identifier\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@babel/helper-validator-identifier\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-validator-identifier\";}s:30:\"@babel/helper-validator-option\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@babel/helper-validator-option\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:89:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helper-validator-option\";}s:14:\"@babel/helpers\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"@babel/helpers\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/helpers\";}s:13:\"@babel/parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"@babel/parser\";s:10:\"\0*\0version\";s:6:\"7.29.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/parser\";}s:24:\"@babel/plugin-syntax-jsx\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@babel/plugin-syntax-jsx\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/plugin-syntax-jsx\";}s:31:\"@babel/plugin-syntax-typescript\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@babel/plugin-syntax-typescript\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:90:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/plugin-syntax-typescript\";}s:40:\"@babel/plugin-transform-modules-commonjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:40:\"@babel/plugin-transform-modules-commonjs\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:99:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/plugin-transform-modules-commonjs\";}s:34:\"@babel/plugin-transform-typescript\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@babel/plugin-transform-typescript\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/plugin-transform-typescript\";}s:24:\"@babel/preset-typescript\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@babel/preset-typescript\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/preset-typescript\";}s:15:\"@babel/template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@babel/template\";s:10:\"\0*\0version\";s:6:\"7.29.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/template\";}s:15:\"@babel/traverse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@babel/traverse\";s:10:\"\0*\0version\";s:6:\"7.29.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/traverse\";}s:12:\"@babel/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"@babel/types\";s:10:\"\0*\0version\";s:6:\"7.29.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@babel/types\";}s:16:\"@dotenvx/dotenvx\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@dotenvx/dotenvx\";s:10:\"\0*\0version\";s:6:\"1.75.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@dotenvx/dotenvx\";}s:19:\"@dotenvx/primitives\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@dotenvx/primitives\";s:10:\"\0*\0version\";s:5:\"0.8.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@dotenvx/primitives\";}s:17:\"@hono/node-server\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@hono/node-server\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@hono/node-server\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@jridgewell/gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@jridgewell/remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@jridgewell/resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.5.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@jridgewell/sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@jridgewell/trace-mapping\";}s:25:\"@modelcontextprotocol/sdk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@modelcontextprotocol/sdk\";s:10:\"\0*\0version\";s:6:\"1.30.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@modelcontextprotocol/sdk\";}s:19:\"@nodelib/fs.scandir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@nodelib/fs.scandir\";s:10:\"\0*\0version\";s:5:\"2.1.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@nodelib/fs.scandir\";}s:16:\"@nodelib/fs.stat\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.stat\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@nodelib/fs.stat\";}s:16:\"@nodelib/fs.walk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.walk\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@nodelib/fs.walk\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.146.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@oxc-project/types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:90:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:89:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:87:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:88:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:96:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:90:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:94:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@rolldown/pluginutils\";}s:24:\"@sec-ant/readable-stream\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@sec-ant/readable-stream\";s:10:\"\0*\0version\";s:5:\"0.4.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@sec-ant/readable-stream\";}s:27:\"@sindresorhus/merge-streams\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@sindresorhus/merge-streams\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@sindresorhus/merge-streams\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:90:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:88:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:89:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:97:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:93:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:94:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:89:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:94:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:92:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@tailwindcss/vite\";}s:16:\"@ts-morph/common\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@ts-morph/common\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@ts-morph/common\";}s:32:\"@types/validate-npm-package-name\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@types/validate-npm-package-name\";s:10:\"\0*\0version\";s:5:\"4.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/@types/validate-npm-package-name\";}s:7:\"accepts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"accepts\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/accepts\";}s:3:\"ajv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ajv\";s:10:\"\0*\0version\";s:6:\"8.20.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ajv\";}s:11:\"ajv-formats\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ajv-formats\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ajv-formats\";}s:11:\"ansi-colors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-colors\";s:10:\"\0*\0version\";s:5:\"4.1.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ansi-colors\";}s:8:\"argparse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"argparse\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/argparse\";}s:9:\"ast-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"ast-types\";s:10:\"\0*\0version\";s:6:\"0.16.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ast-types\";}s:10:\"atomically\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"atomically\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/atomically\";}s:14:\"balanced-match\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"balanced-match\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/balanced-match\";}s:24:\"baseline-browser-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"baseline-browser-mapping\";s:10:\"\0*\0version\";s:7:\"2.11.19\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/baseline-browser-mapping\";}s:11:\"body-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"body-parser\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/body-parser\";}s:15:\"brace-expansion\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"brace-expansion\";s:10:\"\0*\0version\";s:5:\"5.0.9\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/brace-expansion\";}s:6:\"braces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"braces\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/braces\";}s:12:\"browserslist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"browserslist\";s:10:\"\0*\0version\";s:6:\"4.28.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/browserslist\";}s:11:\"bundle-name\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"bundle-name\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/bundle-name\";}s:5:\"bytes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"bytes\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/bytes\";}s:23:\"call-bind-apply-helpers\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"call-bind-apply-helpers\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/call-bind-apply-helpers\";}s:10:\"call-bound\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"call-bound\";s:10:\"\0*\0version\";s:5:\"1.0.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/call-bound\";}s:9:\"callsites\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"callsites\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/callsites\";}s:12:\"caniuse-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"caniuse-lite\";s:10:\"\0*\0version\";s:12:\"1.0.30001810\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/caniuse-lite\";}s:12:\"cli-spinners\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-spinners\";s:10:\"\0*\0version\";s:5:\"2.9.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cli-spinners\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cliui\";}s:17:\"code-block-writer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"code-block-writer\";s:10:\"\0*\0version\";s:6:\"13.0.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/code-block-writer\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/concurrently\";}s:4:\"conf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"conf\";s:10:\"\0*\0version\";s:6:\"10.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/conf\";}s:19:\"content-disposition\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"content-disposition\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/content-disposition\";}s:12:\"content-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"content-type\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/content-type\";}s:18:\"convert-source-map\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"convert-source-map\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/convert-source-map\";}s:6:\"cookie\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cookie\";s:10:\"\0*\0version\";s:5:\"0.7.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cookie\";}s:16:\"cookie-signature\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"cookie-signature\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cookie-signature\";}s:4:\"cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"cors\";s:10:\"\0*\0version\";s:5:\"2.8.6\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cors\";}s:11:\"cosmiconfig\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"cosmiconfig\";s:10:\"\0*\0version\";s:5:\"9.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cosmiconfig\";}s:11:\"cross-spawn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"cross-spawn\";s:10:\"\0*\0version\";s:5:\"7.0.6\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cross-spawn\";}s:6:\"cssesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cssesc\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/cssesc\";}s:11:\"debounce-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"debounce-fn\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/debounce-fn\";}s:5:\"debug\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"debug\";s:10:\"\0*\0version\";s:5:\"4.4.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/debug\";}s:6:\"dedent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"dedent\";s:10:\"\0*\0version\";s:5:\"1.7.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/dedent\";}s:9:\"deepmerge\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"deepmerge\";s:10:\"\0*\0version\";s:5:\"4.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/deepmerge\";}s:15:\"default-browser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"default-browser\";s:10:\"\0*\0version\";s:5:\"5.5.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/default-browser\";}s:18:\"default-browser-id\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"default-browser-id\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/default-browser-id\";}s:16:\"define-lazy-prop\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"define-lazy-prop\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/define-lazy-prop\";}s:4:\"depd\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"depd\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/depd\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/detect-libc\";}s:4:\"diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"diff\";s:10:\"\0*\0version\";s:5:\"8.0.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/diff\";}s:8:\"dot-prop\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"dot-prop\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/dot-prop\";}s:6:\"dotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"dotenv\";s:10:\"\0*\0version\";s:6:\"17.4.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/dotenv\";}s:12:\"dunder-proto\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"dunder-proto\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/dunder-proto\";}s:8:\"ee-first\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"ee-first\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ee-first\";}s:20:\"electron-to-chromium\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"electron-to-chromium\";s:10:\"\0*\0version\";s:7:\"1.5.414\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/electron-to-chromium\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/emoji-regex\";}s:9:\"encodeurl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"encodeurl\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/encodeurl\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.24.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/enhanced-resolve\";}s:8:\"enquirer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"enquirer\";s:10:\"\0*\0version\";s:5:\"2.4.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/enquirer\";}s:9:\"env-paths\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"env-paths\";s:10:\"\0*\0version\";s:5:\"2.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/env-paths\";}s:8:\"error-ex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"error-ex\";s:10:\"\0*\0version\";s:5:\"1.3.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/error-ex\";}s:18:\"es-define-property\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"es-define-property\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/es-define-property\";}s:9:\"es-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"es-errors\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/es-errors\";}s:15:\"es-object-atoms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"es-object-atoms\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/es-object-atoms\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/escalade\";}s:11:\"escape-html\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"escape-html\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/escape-html\";}s:7:\"esprima\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"esprima\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/esprima\";}s:4:\"etag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"etag\";s:10:\"\0*\0version\";s:5:\"1.8.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/etag\";}s:11:\"eventsource\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"eventsource\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/eventsource\";}s:18:\"eventsource-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"eventsource-parser\";s:10:\"\0*\0version\";s:5:\"3.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/eventsource-parser\";}s:5:\"execa\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"execa\";s:10:\"\0*\0version\";s:5:\"9.6.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/execa\";}s:7:\"express\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"express\";s:10:\"\0*\0version\";s:5:\"5.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/express\";}s:18:\"express-rate-limit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"express-rate-limit\";s:10:\"\0*\0version\";s:5:\"8.6.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/express-rate-limit\";}s:15:\"fast-deep-equal\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"fast-deep-equal\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fast-deep-equal\";}s:9:\"fast-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fast-glob\";s:10:\"\0*\0version\";s:5:\"3.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fast-glob\";}s:8:\"fast-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fast-uri\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fast-uri\";}s:5:\"fastq\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fastq\";s:10:\"\0*\0version\";s:6:\"1.20.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fastq\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fdir\";}s:7:\"figures\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"figures\";s:10:\"\0*\0version\";s:5:\"6.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/figures\";}s:10:\"fill-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"fill-range\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fill-range\";}s:12:\"finalhandler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"finalhandler\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/finalhandler\";}s:7:\"find-up\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"find-up\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/find-up\";}s:9:\"forwarded\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"forwarded\";s:10:\"\0*\0version\";s:5:\"0.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/forwarded\";}s:5:\"fresh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fresh\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fresh\";}s:8:\"fs-extra\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fs-extra\";s:10:\"\0*\0version\";s:6:\"11.4.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fs-extra\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fsevents\";}s:13:\"function-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"function-bind\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/function-bind\";}s:9:\"fuzzysort\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fuzzysort\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/fuzzysort\";}s:7:\"gensync\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"gensync\";s:10:\"\0*\0version\";s:12:\"1.0.0-beta.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/gensync\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-caller-file\";}s:13:\"get-intrinsic\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"get-intrinsic\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-intrinsic\";}s:23:\"get-own-enumerable-keys\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"get-own-enumerable-keys\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-own-enumerable-keys\";}s:9:\"get-proto\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"get-proto\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-proto\";}s:10:\"get-stream\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"get-stream\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/get-stream\";}s:11:\"glob-parent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"glob-parent\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/glob-parent\";}s:4:\"gopd\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"gopd\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/gopd\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/graceful-fs\";}s:11:\"has-symbols\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"has-symbols\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/has-symbols\";}s:6:\"hasown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"hasown\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/hasown\";}s:4:\"hono\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"hono\";s:10:\"\0*\0version\";s:6:\"4.13.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/hono\";}s:11:\"http-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"http-errors\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/http-errors\";}s:13:\"human-signals\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"human-signals\";s:10:\"\0*\0version\";s:5:\"8.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/human-signals\";}s:10:\"iconv-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"iconv-lite\";s:10:\"\0*\0version\";s:5:\"0.7.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/iconv-lite\";}s:6:\"ignore\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"ignore\";s:10:\"\0*\0version\";s:5:\"5.3.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ignore\";}s:12:\"import-fresh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"import-fresh\";s:10:\"\0*\0version\";s:5:\"3.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/import-fresh\";}s:8:\"inherits\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"inherits\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/inherits\";}s:10:\"ip-address\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ip-address\";s:10:\"\0*\0version\";s:6:\"10.5.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ip-address\";}s:9:\"ipaddr.js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"ipaddr.js\";s:10:\"\0*\0version\";s:5:\"1.9.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ipaddr.js\";}s:11:\"is-arrayish\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"is-arrayish\";s:10:\"\0*\0version\";s:5:\"0.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-arrayish\";}s:9:\"is-docker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-docker\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-docker\";}s:10:\"is-extglob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-extglob\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-extglob\";}s:7:\"is-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"is-glob\";s:10:\"\0*\0version\";s:5:\"4.0.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-glob\";}s:9:\"is-in-ssh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-in-ssh\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-in-ssh\";}s:19:\"is-inside-container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"is-inside-container\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-inside-container\";}s:14:\"is-interactive\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-interactive\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-interactive\";}s:9:\"is-number\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-number\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-number\";}s:6:\"is-obj\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"is-obj\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-obj\";}s:12:\"is-plain-obj\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"is-plain-obj\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-plain-obj\";}s:10:\"is-promise\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-promise\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-promise\";}s:9:\"is-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-regexp\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-regexp\";}s:9:\"is-stream\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-stream\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-stream\";}s:20:\"is-unicode-supported\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"is-unicode-supported\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-unicode-supported\";}s:6:\"is-wsl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"is-wsl\";s:10:\"\0*\0version\";s:5:\"3.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/is-wsl\";}s:5:\"isexe\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"isexe\";s:10:\"\0*\0version\";s:5:\"3.1.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/isexe\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/jiti\";}s:4:\"jose\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jose\";s:10:\"\0*\0version\";s:6:\"6.2.10\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/jose\";}s:9:\"js-tokens\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"js-tokens\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/js-tokens\";}s:7:\"js-yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"js-yaml\";s:10:\"\0*\0version\";s:5:\"4.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/js-yaml\";}s:5:\"jsesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"jsesc\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/jsesc\";}s:29:\"json-parse-even-better-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"json-parse-even-better-errors\";s:10:\"\0*\0version\";s:5:\"2.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:88:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/json-parse-even-better-errors\";}s:20:\"json-schema-traverse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"json-schema-traverse\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/json-schema-traverse\";}s:17:\"json-schema-typed\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"json-schema-typed\";s:10:\"\0*\0version\";s:5:\"8.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/json-schema-typed\";}s:5:\"json5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"json5\";s:10:\"\0*\0version\";s:5:\"2.2.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/json5\";}s:8:\"jsonfile\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"jsonfile\";s:10:\"\0*\0version\";s:5:\"6.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/jsonfile\";}s:5:\"kleur\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"kleur\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/kleur\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:85:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:83:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:91:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:87:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:88:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:85:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:88:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:86:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lightningcss-win32-x64-msvc\";}s:17:\"lines-and-columns\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"lines-and-columns\";s:10:\"\0*\0version\";s:5:\"1.2.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lines-and-columns\";}s:11:\"locate-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"locate-path\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/locate-path\";}s:11:\"log-symbols\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"log-symbols\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/log-symbols\";}s:9:\"lru-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"lru-cache\";s:10:\"\0*\0version\";s:5:\"5.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/lru-cache\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/magic-string\";}s:15:\"math-intrinsics\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"math-intrinsics\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/math-intrinsics\";}s:11:\"media-typer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"media-typer\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/media-typer\";}s:17:\"merge-descriptors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"merge-descriptors\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/merge-descriptors\";}s:12:\"merge-stream\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"merge-stream\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/merge-stream\";}s:6:\"merge2\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"merge2\";s:10:\"\0*\0version\";s:5:\"1.4.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/merge2\";}s:10:\"micromatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"micromatch\";s:10:\"\0*\0version\";s:5:\"4.0.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/micromatch\";}s:7:\"mime-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"mime-db\";s:10:\"\0*\0version\";s:6:\"1.54.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/mime-db\";}s:10:\"mime-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"mime-types\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/mime-types\";}s:14:\"mimic-function\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"mimic-function\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/mimic-function\";}s:9:\"minimatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"minimatch\";s:10:\"\0*\0version\";s:6:\"10.2.6\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/minimatch\";}s:8:\"minimist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"minimist\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/minimist\";}s:2:\"ms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ms\";s:10:\"\0*\0version\";s:5:\"2.1.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ms\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.18\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/nanoid\";}s:10:\"negotiator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"negotiator\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/negotiator\";}s:13:\"node-releases\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"node-releases\";s:10:\"\0*\0version\";s:6:\"2.0.53\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/node-releases\";}s:12:\"npm-run-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"npm-run-path\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/npm-run-path\";}s:13:\"object-assign\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"object-assign\";s:10:\"\0*\0version\";s:5:\"4.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/object-assign\";}s:14:\"object-inspect\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"object-inspect\";s:10:\"\0*\0version\";s:6:\"1.13.4\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/object-inspect\";}s:14:\"object-treeify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"object-treeify\";s:10:\"\0*\0version\";s:6:\"1.1.33\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/object-treeify\";}s:11:\"on-finished\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"on-finished\";s:10:\"\0*\0version\";s:5:\"2.4.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/on-finished\";}s:4:\"once\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"once\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/once\";}s:4:\"open\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"open\";s:10:\"\0*\0version\";s:6:\"11.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/open\";}s:3:\"ora\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ora\";s:10:\"\0*\0version\";s:5:\"8.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ora\";}s:7:\"p-limit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"p-limit\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/p-limit\";}s:8:\"p-locate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"p-locate\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/p-locate\";}s:5:\"p-try\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"p-try\";s:10:\"\0*\0version\";s:5:\"2.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/p-try\";}s:13:\"parent-module\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"parent-module\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/parent-module\";}s:10:\"parse-json\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"parse-json\";s:10:\"\0*\0version\";s:5:\"5.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/parse-json\";}s:8:\"parse-ms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"parse-ms\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/parse-ms\";}s:8:\"parseurl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"parseurl\";s:10:\"\0*\0version\";s:5:\"1.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/parseurl\";}s:15:\"path-browserify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"path-browserify\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/path-browserify\";}s:11:\"path-exists\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"path-exists\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/path-exists\";}s:8:\"path-key\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"path-key\";s:10:\"\0*\0version\";s:5:\"3.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/path-key\";}s:14:\"path-to-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"path-to-regexp\";s:10:\"\0*\0version\";s:5:\"8.4.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/path-to-regexp\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"4.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/picomatch\";}s:14:\"pkce-challenge\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"pkce-challenge\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/pkce-challenge\";}s:6:\"pkg-up\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"pkg-up\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/pkg-up\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.26\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/postcss\";}s:23:\"postcss-selector-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"postcss-selector-parser\";s:10:\"\0*\0version\";s:5:\"7.1.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/postcss-selector-parser\";}s:16:\"powershell-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"powershell-utils\";s:10:\"\0*\0version\";s:5:\"0.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/powershell-utils\";}s:9:\"pretty-ms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"pretty-ms\";s:10:\"\0*\0version\";s:5:\"9.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/pretty-ms\";}s:7:\"prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"prompts\";s:10:\"\0*\0version\";s:5:\"2.4.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/prompts\";}s:10:\"proxy-addr\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"proxy-addr\";s:10:\"\0*\0version\";s:5:\"2.0.7\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/proxy-addr\";}s:2:\"qs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"qs\";s:10:\"\0*\0version\";s:6:\"6.15.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/qs\";}s:15:\"queue-microtask\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"queue-microtask\";s:10:\"\0*\0version\";s:5:\"1.2.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/queue-microtask\";}s:12:\"range-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"range-parser\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/range-parser\";}s:8:\"raw-body\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"raw-body\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/raw-body\";}s:6:\"recast\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"recast\";s:10:\"\0*\0version\";s:7:\"0.23.21\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/recast\";}s:19:\"require-from-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"require-from-string\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/require-from-string\";}s:12:\"resolve-from\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"resolve-from\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/resolve-from\";}s:7:\"reusify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"reusify\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/reusify\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:5:\"1.2.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/rolldown\";}s:6:\"router\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"router\";s:10:\"\0*\0version\";s:5:\"2.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/router\";}s:15:\"run-applescript\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"run-applescript\";s:10:\"\0*\0version\";s:5:\"7.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/run-applescript\";}s:12:\"run-parallel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"run-parallel\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/run-parallel\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/rxjs\";}s:12:\"safer-buffer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"safer-buffer\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/safer-buffer\";}s:6:\"semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"semver\";s:10:\"\0*\0version\";s:5:\"6.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/semver\";}s:4:\"send\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"send\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/send\";}s:12:\"serve-static\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"serve-static\";s:10:\"\0*\0version\";s:5:\"2.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/serve-static\";}s:14:\"setprototypeof\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"setprototypeof\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/setprototypeof\";}s:6:\"shadcn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"shadcn\";s:10:\"\0*\0version\";s:6:\"4.19.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/shadcn\";}s:15:\"shebang-command\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"shebang-command\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/shebang-command\";}s:13:\"shebang-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"shebang-regex\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/shebang-regex\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:5:\"1.9.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/shell-quote\";}s:12:\"side-channel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"side-channel\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/side-channel\";}s:17:\"side-channel-list\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"side-channel-list\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/side-channel-list\";}s:16:\"side-channel-map\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"side-channel-map\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/side-channel-map\";}s:20:\"side-channel-weakmap\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"side-channel-weakmap\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/side-channel-weakmap\";}s:10:\"sisteransi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"sisteransi\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/sisteransi\";}s:12:\"smart-buffer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"smart-buffer\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/smart-buffer\";}s:5:\"socks\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"socks\";s:10:\"\0*\0version\";s:5:\"2.8.9\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/socks\";}s:10:\"source-map\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"source-map\";s:10:\"\0*\0version\";s:5:\"0.6.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/source-map\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/source-map-js\";}s:8:\"statuses\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"statuses\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/statuses\";}s:15:\"stdin-discarder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"stdin-discarder\";s:10:\"\0*\0version\";s:5:\"0.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/stdin-discarder\";}s:16:\"stringify-object\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"stringify-object\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/stringify-object\";}s:9:\"strip-bom\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"strip-bom\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/strip-bom\";}s:19:\"strip-final-newline\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"strip-final-newline\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:78:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/strip-final-newline\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/supports-color\";}s:17:\"systeminformation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"systeminformation\";s:10:\"\0*\0version\";s:6:\"5.33.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/systeminformation\";}s:14:\"tailwind-merge\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"tailwind-merge\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tailwind-merge\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tapable\";}s:14:\"tiny-invariant\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"tiny-invariant\";s:10:\"\0*\0version\";s:5:\"1.3.3\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tiny-invariant\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tinyglobby\";}s:14:\"to-regex-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"to-regex-range\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/to-regex-range\";}s:12:\"toidentifier\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"toidentifier\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/toidentifier\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tree-kill\";}s:8:\"ts-morph\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"ts-morph\";s:10:\"\0*\0version\";s:6:\"26.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/ts-morph\";}s:14:\"tsconfig-paths\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"tsconfig-paths\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tsconfig-paths\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/tslib\";}s:7:\"type-is\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"type-is\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/type-is\";}s:6:\"undici\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"undici\";s:10:\"\0*\0version\";s:6:\"7.29.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/undici\";}s:13:\"unicorn-magic\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"unicorn-magic\";s:10:\"\0*\0version\";s:5:\"0.3.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/unicorn-magic\";}s:12:\"universalify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"universalify\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/universalify\";}s:6:\"unpipe\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"unpipe\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/unpipe\";}s:22:\"update-browserslist-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"update-browserslist-db\";s:10:\"\0*\0version\";s:5:\"1.3.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:81:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/update-browserslist-db\";}s:14:\"util-deprecate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"util-deprecate\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/util-deprecate\";}s:25:\"validate-npm-package-name\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"validate-npm-package-name\";s:10:\"\0*\0version\";s:5:\"7.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:84:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/validate-npm-package-name\";}s:4:\"vary\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vary\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/vary\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:82:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/vite-plugin-full-reload\";}s:5:\"which\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"which\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/which\";}s:6:\"wrappy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"wrappy\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/wrappy\";}s:9:\"wsl-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wsl-utils\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/wsl-utils\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/y18n\";}s:7:\"yallist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"yallist\";s:10:\"\0*\0version\";s:5:\"3.1.1\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yallist\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yargs-parser\";}s:13:\"yocto-spinner\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"yocto-spinner\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yocto-spinner\";}s:11:\"yoctocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoctocolors\";s:10:\"\0*\0version\";s:5:\"2.2.0\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/yoctocolors\";}s:3:\"zod\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"zod\";s:10:\"\0*\0version\";s:7:\"3.25.76\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/zod\";}s:18:\"zod-to-json-schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"zod-to-json-schema\";s:10:\"\0*\0version\";s:6:\"3.25.2\";s:9:\"\0*\0source\";r:1264;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"/Users/admin/Downloads/Proyek Sim Madrasah V2/node_modules/zod-to-json-schema\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:383:{i:0;r:1261;i:1;r:1269;i:2;r:1277;i:3;r:1285;i:4;r:1293;i:5;r:1301;i:6;r:1309;i:7;r:1317;i:8;r:1325;i:9;r:1333;i:10;r:1341;i:11;r:1349;i:12;r:1357;i:13;r:1365;i:14;r:1373;i:15;r:1381;i:16;r:1389;i:17;r:1397;i:18;r:1405;i:19;r:1413;i:20;r:1421;i:21;r:1429;i:22;r:1437;i:23;r:1445;i:24;r:1453;i:25;r:1461;i:26;r:1469;i:27;r:1477;i:28;r:1485;i:29;r:1493;i:30;r:1501;i:31;r:1509;i:32;r:1517;i:33;r:1525;i:34;r:1533;i:35;r:1541;i:36;r:1549;i:37;r:1557;i:38;r:1565;i:39;r:1573;i:40;r:1581;i:41;r:1589;i:42;r:1597;i:43;r:1605;i:44;r:1613;i:45;r:1621;i:46;r:1629;i:47;r:1637;i:48;r:1645;i:49;r:1653;i:50;r:1661;i:51;r:1669;i:52;r:1677;i:53;r:1685;i:54;r:1693;i:55;r:1701;i:56;r:1709;i:57;r:1717;i:58;r:1725;i:59;r:1733;i:60;r:1741;i:61;r:1749;i:62;r:1757;i:63;r:1765;i:64;r:1773;i:65;r:1781;i:66;r:1789;i:67;r:1797;i:68;r:1805;i:69;r:1813;i:70;r:1821;i:71;r:1829;i:72;r:1837;i:73;r:1845;i:74;r:1853;i:75;r:1861;i:76;r:1869;i:77;r:1877;i:78;r:1885;i:79;r:1893;i:80;r:1901;i:81;r:1909;i:82;r:1917;i:83;r:1925;i:84;r:1933;i:85;r:1941;i:86;r:1949;i:87;r:1957;i:88;r:1965;i:89;r:1973;i:90;r:1981;i:91;r:1989;i:92;r:1997;i:93;r:2005;i:94;r:2013;i:95;r:2021;i:96;r:2029;i:97;r:2037;i:98;r:2045;i:99;r:2053;i:100;r:2061;i:101;r:2069;i:102;r:2077;i:103;r:2085;i:104;r:2093;i:105;r:2101;i:106;r:2109;i:107;r:2117;i:108;r:2125;i:109;r:2133;i:110;r:2141;i:111;r:2149;i:112;r:2157;i:113;r:2165;i:114;r:2173;i:115;r:2181;i:116;r:2189;i:117;r:2197;i:118;r:2205;i:119;r:2213;i:120;r:2221;i:121;r:2229;i:122;r:2237;i:123;r:2245;i:124;r:2253;i:125;r:2261;i:126;r:2269;i:127;r:2277;i:128;r:2285;i:129;r:2293;i:130;r:2301;i:131;r:2309;i:132;r:2317;i:133;r:2325;i:134;r:2333;i:135;r:2341;i:136;r:2349;i:137;r:2357;i:138;r:2365;i:139;r:2373;i:140;r:2381;i:141;r:2389;i:142;r:2397;i:143;r:2405;i:144;r:2413;i:145;r:2421;i:146;r:2429;i:147;r:2437;i:148;r:2445;i:149;r:2453;i:150;r:2461;i:151;r:2469;i:152;r:2477;i:153;r:2485;i:154;r:2493;i:155;r:2501;i:156;r:2509;i:157;r:2517;i:158;r:2525;i:159;r:2533;i:160;r:2541;i:161;r:2549;i:162;r:2557;i:163;r:2565;i:164;r:2573;i:165;r:2581;i:166;r:2589;i:167;r:2597;i:168;r:2605;i:169;r:2613;i:170;r:2621;i:171;r:2629;i:172;r:2637;i:173;r:2645;i:174;r:2653;i:175;r:2661;i:176;r:2669;i:177;r:2677;i:178;r:2685;i:179;r:2693;i:180;r:2701;i:181;r:2709;i:182;r:2717;i:183;r:2725;i:184;r:2733;i:185;r:2741;i:186;r:2749;i:187;r:2757;i:188;r:2765;i:189;r:2773;i:190;r:2781;i:191;r:2789;i:192;r:2797;i:193;r:2805;i:194;r:2813;i:195;r:2821;i:196;r:2829;i:197;r:2837;i:198;r:2845;i:199;r:2853;i:200;r:2861;i:201;r:2869;i:202;r:2877;i:203;r:2885;i:204;r:2893;i:205;r:2901;i:206;r:2909;i:207;r:2917;i:208;r:2925;i:209;r:2933;i:210;r:2941;i:211;r:2949;i:212;r:2957;i:213;r:2965;i:214;r:2973;i:215;r:2981;i:216;r:2989;i:217;r:2997;i:218;r:3005;i:219;r:3013;i:220;r:3021;i:221;r:3029;i:222;r:3037;i:223;r:3045;i:224;r:3053;i:225;r:3061;i:226;r:3069;i:227;r:3077;i:228;r:3085;i:229;r:3093;i:230;r:3101;i:231;r:3109;i:232;r:3117;i:233;r:3125;i:234;r:3133;i:235;r:3141;i:236;r:3149;i:237;r:3157;i:238;r:3165;i:239;r:3173;i:240;r:3181;i:241;r:3189;i:242;r:3197;i:243;r:3205;i:244;r:3213;i:245;r:3221;i:246;r:3229;i:247;r:3237;i:248;r:3245;i:249;r:3253;i:250;r:3261;i:251;r:3269;i:252;r:3277;i:253;r:3285;i:254;r:3293;i:255;r:3301;i:256;r:3309;i:257;r:3317;i:258;r:3325;i:259;r:3333;i:260;r:3341;i:261;r:3349;i:262;r:3357;i:263;r:3365;i:264;r:3373;i:265;r:3381;i:266;r:3389;i:267;r:3397;i:268;r:3405;i:269;r:3413;i:270;r:3421;i:271;r:3429;i:272;r:3437;i:273;r:3445;i:274;r:3453;i:275;r:3461;i:276;r:3469;i:277;r:3477;i:278;r:3485;i:279;r:3493;i:280;r:3501;i:281;r:3509;i:282;r:3517;i:283;r:3525;i:284;r:3533;i:285;r:3541;i:286;r:3549;i:287;r:3557;i:288;r:3565;i:289;r:3573;i:290;r:3581;i:291;r:3589;i:292;r:3597;i:293;r:3605;i:294;r:3613;i:295;r:3621;i:296;r:3629;i:297;r:3637;i:298;r:3645;i:299;r:3653;i:300;r:3661;i:301;r:3669;i:302;r:3677;i:303;r:3685;i:304;r:3693;i:305;r:3701;i:306;r:3709;i:307;r:3717;i:308;r:3725;i:309;r:3733;i:310;r:3741;i:311;r:3749;i:312;r:3757;i:313;r:3765;i:314;r:3773;i:315;r:3781;i:316;r:3789;i:317;r:3797;i:318;r:3805;i:319;r:3813;i:320;r:3821;i:321;r:3829;i:322;r:3837;i:323;r:3845;i:324;r:3853;i:325;r:3861;i:326;r:3869;i:327;r:3877;i:328;r:3885;i:329;r:3893;i:330;r:3901;i:331;r:3909;i:332;r:3917;i:333;r:3925;i:334;r:3933;i:335;r:3941;i:336;r:3949;i:337;r:3957;i:338;r:3965;i:339;r:3973;i:340;r:3981;i:341;r:3989;i:342;r:3997;i:343;r:4005;i:344;r:4013;i:345;r:4021;i:346;r:4029;i:347;r:4037;i:348;r:4045;i:349;r:4053;i:350;r:4061;i:351;r:4069;i:352;r:4077;i:353;r:4085;i:354;r:4093;i:355;r:4101;i:356;r:4109;i:357;r:4117;i:358;r:4125;i:359;r:4133;i:360;r:4141;i:361;r:4149;i:362;r:4157;i:363;r:4165;i:364;r:4173;i:365;r:4181;i:366;r:4189;i:367;r:4197;i:368;r:4205;i:369;r:4213;i:370;r:4221;i:371;r:4229;i:372;r:4237;i:373;r:4245;i:374;r:4253;i:375;r:4261;i:376;r:4269;i:377;r:4277;i:378;r:4285;i:379;r:4293;i:380;r:4301;i:381;r:4309;i:382;r:4317;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";i:1;E:35:\"Laravel\\Roster\\Enums\\Agent:OpenCode\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}',1787977511);
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
-- Table structure for table `class_groups`
--

DROP TABLE IF EXISTS `class_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_level` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_groups`
--

LOCK TABLES `class_groups` WRITE;
/*!40000 ALTER TABLE `class_groups` DISABLE KEYS */;
INSERT INTO `class_groups` VALUES (1,'I-A','I','2026-08-27 01:09:42','2026-08-27 01:09:42'),(2,'I-B','I','2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,'I-C','I','2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,'II-A','II','2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,'II-B','II','2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,'III-A','III','2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,'III-B','III','2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,'IV-A','IV','2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,'IV-B','IV','2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,'V-A','V','2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,'V-B','V','2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,'VI-A','VI','2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,'VI-B','VI','2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `class_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `counseling_sessions`
--

DROP TABLE IF EXISTS `counseling_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `counseling_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `counselor_user_id` bigint unsigned NOT NULL,
  `session_date` date NOT NULL,
  `counseling_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'individual, kelompok, krisis',
  `topic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `problem_description` text COLLATE utf8mb4_unicode_ci,
  `assessment_result` text COLLATE utf8mb4_unicode_ci,
  `action_taken` text COLLATE utf8mb4_unicode_ci,
  `follow_up_plan` text COLLATE utf8mb4_unicode_ci,
  `confidentiality_level` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plus_wali_kelas' COMMENT 'guru_bk_only, plus_kepala, plus_wali_kelas',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif' COMMENT 'aktif, ditutup',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cs_counselor_conf_index` (`counselor_user_id`,`confidentiality_level`),
  KEY `cs_enrollment_status_index` (`student_enrollment_id`,`status`),
  CONSTRAINT `counseling_sessions_counselor_user_id_foreign` FOREIGN KEY (`counselor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `counseling_sessions_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counseling_sessions`
--

LOCK TABLES `counseling_sessions` WRITE;
/*!40000 ALTER TABLE `counseling_sessions` DISABLE KEYS */;
INSERT INTO `counseling_sessions` VALUES (1,1,1,'2026-08-17','individual','Motivasi Belajar','Siswa menunjukkan penurunan motivasi belajar dalam 2 bulan terakhir. Nilai merata turun di beberapa mata pelajaran.','Siswa mengalami kesulitan fokus akibat masalah di rumah. Perlu pendekatan emosional dan akademik.','Melakukan sesi konseling individu, berdiskusi dengan wali kelas, dan menghubungi orang tua.','Pemantauan mingguan selama 1 bulan. Evaluasi perkembangan siswa.','plus_wali_kelas','aktif',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,2,1,'2026-08-22','individual','Konflik Antar Teman','Siswa terlibat konflik dengan teman sekelasnya. Dampak: siswa tidak nyaman di kelas.',NULL,'Mediasi antara kedua siswa. Penjelasan tentang pentingnya komunikasi.','Pemantauan selama 2 minggu.','plus_kepala','aktif',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,3,1,'2026-08-07','kelompok','Persiapan Ujian','Sesi kelompok untuk siswa kelas VI tentang strategi menghadapi ujian.','Sebagian besar siswa sudah siap. 3 siswa perlu pendampingan khusus.','Memberikan tips belajar efektif dan jadwal belajar mandiri.',NULL,'guru_bk_only','ditutup',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `counseling_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_attendances`
--

DROP TABLE IF EXISTS `employee_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_attendance_unique` (`employee_id`,`attendance_date`),
  KEY `employee_attendances_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `employee_attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_attendances_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=469 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendances`
--

LOCK TABLES `employee_attendances` WRITE;
/*!40000 ALTER TABLE `employee_attendances` DISABLE KEYS */;
INSERT INTO `employee_attendances` VALUES (1,1,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(2,1,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(3,1,'2026-08-17','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(4,1,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(5,1,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(6,1,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(7,1,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(8,1,'2026-08-22','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(9,1,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(10,1,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(11,1,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(12,1,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(13,2,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(14,2,'2026-08-15','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(15,2,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(16,2,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(17,2,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(18,2,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(19,2,'2026-08-21','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(20,2,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(21,2,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(22,2,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(23,2,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(24,2,'2026-08-27','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(25,3,'2026-08-14','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(26,3,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(27,3,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(28,3,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(29,3,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(30,3,'2026-08-20','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(31,3,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(32,3,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(33,3,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(34,3,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(35,3,'2026-08-26','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(36,3,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(37,4,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(38,4,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(39,4,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(40,4,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(41,4,'2026-08-19','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(42,4,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(43,4,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(44,4,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(45,4,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(46,4,'2026-08-25','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(47,4,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(48,4,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(49,5,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(50,5,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(51,5,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(52,5,'2026-08-18','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(53,5,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(54,5,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(55,5,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(56,5,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(57,5,'2026-08-24','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(58,5,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(59,5,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(60,5,'2026-08-27','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(61,6,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(62,6,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(63,6,'2026-08-17','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(64,6,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(65,6,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(66,6,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(67,6,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(68,6,'2026-08-22','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(69,6,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(70,6,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(71,6,'2026-08-26','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(72,6,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(73,7,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(74,7,'2026-08-15','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(75,7,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(76,7,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(77,7,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(78,7,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(79,7,'2026-08-21','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(80,7,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(81,7,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(82,7,'2026-08-25','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(83,7,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(84,7,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(85,8,'2026-08-14','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(86,8,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(87,8,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(88,8,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(89,8,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(90,8,'2026-08-20','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(91,8,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(92,8,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(93,8,'2026-08-24','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(94,8,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(95,8,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(96,8,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(97,9,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(98,9,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(99,9,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(100,9,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(101,9,'2026-08-19','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(102,9,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(103,9,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(104,9,'2026-08-22','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(105,9,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(106,9,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(107,9,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(108,9,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(109,10,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(110,10,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(111,10,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(112,10,'2026-08-18','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(113,10,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(114,10,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(115,10,'2026-08-21','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(116,10,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(117,10,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(118,10,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(119,10,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(120,10,'2026-08-27','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(121,11,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(122,11,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(123,11,'2026-08-17','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(124,11,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(125,11,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(126,11,'2026-08-20','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(127,11,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(128,11,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(129,11,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(130,11,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(131,11,'2026-08-26','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(132,11,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(133,12,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(134,12,'2026-08-15','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(135,12,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(136,12,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(137,12,'2026-08-19','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(138,12,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(139,12,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(140,12,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(141,12,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(142,12,'2026-08-25','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(143,12,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(144,12,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(145,13,'2026-08-14','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(146,13,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(147,13,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(148,13,'2026-08-18','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(149,13,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(150,13,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(151,13,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(152,13,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(153,13,'2026-08-24','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(154,13,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(155,13,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(156,13,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(157,14,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(158,14,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(159,14,'2026-08-17','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(160,14,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(161,14,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(162,14,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(163,14,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(164,14,'2026-08-22','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(165,14,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(166,14,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(167,14,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(168,14,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(169,15,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(170,15,'2026-08-15','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(171,15,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(172,15,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(173,15,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(174,15,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(175,15,'2026-08-21','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(176,15,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(177,15,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(178,15,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(179,15,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(180,15,'2026-08-27','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(181,16,'2026-08-14','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(182,16,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(183,16,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(184,16,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(185,16,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(186,16,'2026-08-20','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(187,16,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(188,16,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(189,16,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(190,16,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(191,16,'2026-08-26','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(192,16,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(193,17,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(194,17,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(195,17,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(196,17,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(197,17,'2026-08-19','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(198,17,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(199,17,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(200,17,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(201,17,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(202,17,'2026-08-25','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(203,17,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(204,17,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(205,18,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(206,18,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(207,18,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(208,18,'2026-08-18','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(209,18,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(210,18,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(211,18,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(212,18,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(213,18,'2026-08-24','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(214,18,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(215,18,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(216,18,'2026-08-27','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(217,19,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(218,19,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(219,19,'2026-08-17','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(220,19,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(221,19,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(222,19,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(223,19,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(224,19,'2026-08-22','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(225,19,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(226,19,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(227,19,'2026-08-26','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(228,19,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(229,20,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(230,20,'2026-08-15','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(231,20,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(232,20,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(233,20,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(234,20,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(235,20,'2026-08-21','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(236,20,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(237,20,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(238,20,'2026-08-25','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(239,20,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(240,20,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(241,21,'2026-08-14','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(242,21,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(243,21,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(244,21,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(245,21,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(246,21,'2026-08-20','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(247,21,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(248,21,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(249,21,'2026-08-24','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(250,21,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(251,21,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(252,21,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(253,22,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(254,22,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(255,22,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(256,22,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(257,22,'2026-08-19','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(258,22,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(259,22,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(260,22,'2026-08-22','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(261,22,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(262,22,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(263,22,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(264,22,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(265,23,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(266,23,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(267,23,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(268,23,'2026-08-18','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(269,23,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(270,23,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(271,23,'2026-08-21','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(272,23,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(273,23,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(274,23,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(275,23,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(276,23,'2026-08-27','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(277,24,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(278,24,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(279,24,'2026-08-17','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(280,24,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(281,24,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(282,24,'2026-08-20','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(283,24,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(284,24,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(285,24,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(286,24,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(287,24,'2026-08-26','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(288,24,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(289,25,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(290,25,'2026-08-15','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(291,25,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(292,25,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(293,25,'2026-08-19','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(294,25,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(295,25,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(296,25,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(297,25,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(298,25,'2026-08-25','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(299,25,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(300,25,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(301,26,'2026-08-14','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(302,26,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(303,26,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(304,26,'2026-08-18','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(305,26,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(306,26,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(307,26,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(308,26,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(309,26,'2026-08-24','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(310,26,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(311,26,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(312,26,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(313,27,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(314,27,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(315,27,'2026-08-17','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(316,27,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(317,27,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(318,27,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(319,27,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(320,27,'2026-08-22','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(321,27,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(322,27,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(323,27,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(324,27,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(325,28,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(326,28,'2026-08-15','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(327,28,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(328,28,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(329,28,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(330,28,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(331,28,'2026-08-21','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(332,28,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(333,28,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(334,28,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(335,28,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(336,28,'2026-08-27','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(337,29,'2026-08-14','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(338,29,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(339,29,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(340,29,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(341,29,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(342,29,'2026-08-20','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(343,29,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(344,29,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(345,29,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(346,29,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(347,29,'2026-08-26','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(348,29,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(349,30,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(350,30,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(351,30,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(352,30,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(353,30,'2026-08-19','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(354,30,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(355,30,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(356,30,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(357,30,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(358,30,'2026-08-25','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(359,30,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(360,30,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(361,31,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(362,31,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(363,31,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(364,31,'2026-08-18','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(365,31,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(366,31,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(367,31,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(368,31,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(369,31,'2026-08-24','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(370,31,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(371,31,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(372,31,'2026-08-27','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(373,32,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(374,32,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(375,32,'2026-08-17','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(376,32,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(377,32,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(378,32,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(379,32,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(380,32,'2026-08-22','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(381,32,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(382,32,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(383,32,'2026-08-26','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(384,32,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(385,33,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(386,33,'2026-08-15','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(387,33,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(388,33,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(389,33,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(390,33,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(391,33,'2026-08-21','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(392,33,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(393,33,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(394,33,'2026-08-25','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(395,33,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(396,33,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(397,34,'2026-08-14','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(398,34,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(399,34,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(400,34,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(401,34,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(402,34,'2026-08-20','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(403,34,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(404,34,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(405,34,'2026-08-24','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(406,34,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(407,34,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(408,34,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(409,35,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(410,35,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(411,35,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(412,35,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(413,35,'2026-08-19','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(414,35,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(415,35,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(416,35,'2026-08-22','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(417,35,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(418,35,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(419,35,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(420,35,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(421,36,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(422,36,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(423,36,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(424,36,'2026-08-18','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(425,36,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(426,36,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(427,36,'2026-08-21','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(428,36,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(429,36,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(430,36,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(431,36,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(432,36,'2026-08-27','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(433,37,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(434,37,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(435,37,'2026-08-17','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(436,37,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(437,37,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(438,37,'2026-08-20','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(439,37,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(440,37,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(441,37,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(442,37,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(443,37,'2026-08-26','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(444,37,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(445,38,'2026-08-14','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(446,38,'2026-08-15','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(447,38,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(448,38,'2026-08-18','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(449,38,'2026-08-19','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(450,38,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(451,38,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(452,38,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(453,38,'2026-08-24','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(454,38,'2026-08-25','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(455,38,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(456,38,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(457,39,'2026-08-14','sakit',NULL,NULL,'Sakit (surat keterangan)',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(458,39,'2026-08-15','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(459,39,'2026-08-17','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(460,39,'2026-08-18','izin',NULL,NULL,NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(461,39,'2026-08-19','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(462,39,'2026-08-20','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(463,39,'2026-08-21','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(464,39,'2026-08-22','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(465,39,'2026-08-24','terlambat','07:45:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(466,39,'2026-08-25','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(467,39,'2026-08-26','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(468,39,'2026-08-27','hadir','07:15:00','13:45:00',NULL,3,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `employee_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_position_histories`
--

DROP TABLE IF EXISTS `employee_position_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_position_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `started_at` date NOT NULL,
  `ended_at` date DEFAULT NULL,
  `reason` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_position_histories_employee_id_foreign` (`employee_id`),
  KEY `employee_position_histories_position_id_foreign` (`position_id`),
  KEY `employee_position_histories_organizational_unit_id_foreign` (`organizational_unit_id`),
  CONSTRAINT `employee_position_histories_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_position_histories_organizational_unit_id_foreign` FOREIGN KEY (`organizational_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_position_histories_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_position_histories`
--

LOCK TABLES `employee_position_histories` WRITE;
/*!40000 ALTER TABLE `employee_position_histories` DISABLE KEYS */;
INSERT INTO `employee_position_histories` VALUES (1,1,1,1,'2010-01-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(2,2,2,2,'2011-02-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(3,3,6,7,'2019-03-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(4,4,8,6,'2020-07-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(5,5,9,6,'2021-08-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(6,6,6,7,'2018-07-15',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(7,7,7,3,'2022-09-01',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(8,8,10,8,'2023-01-10',NULL,'pengangkatan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(9,1,6,7,'2026-08-29',NULL,'mutasi','2026-08-28 18:42:58','2026-08-28 18:42:58'),(10,40,6,7,'2026-07-01',NULL,'pengangkatan','2026-08-28 18:48:34','2026-08-28 18:48:34');
/*!40000 ALTER TABLE `employee_position_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username_source` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'honor',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `tmt` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_nip_unique` (`nip`),
  KEY `employees_person_id_foreign` (`person_id`),
  KEY `employees_organizational_unit_id_foreign` (`organizational_unit_id`),
  KEY `employees_position_id_foreign` (`position_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  CONSTRAINT `employees_organizational_unit_id_foreign` FOREIGN KEY (`organizational_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,1,7,6,10,'198503122010011003',NULL,'pns','aktif','2010-01-01','2026-08-27 01:09:44','2026-08-28 18:42:58',NULL),(2,2,2,2,12,'198702102011012004','nip','pns','aktif','2011-02-01','2026-08-27 01:09:44','2026-08-28 19:18:35',NULL),(3,3,7,6,1,'199001152019031005',NULL,'pppk','aktif','2019-03-01','2026-08-27 01:09:44','2026-08-27 01:09:46',NULL),(4,4,6,8,8,NULL,NULL,'honor','aktif','2020-07-01','2026-08-27 01:09:44','2026-08-28 18:12:36',NULL),(5,5,6,9,13,NULL,'nik','honor','aktif','2021-08-01','2026-08-27 01:09:44','2026-08-28 19:18:35',NULL),(6,6,7,6,4,NULL,NULL,'honor','aktif','2018-07-15','2026-08-27 01:09:44','2026-08-27 01:09:46',NULL),(7,7,3,7,5,NULL,NULL,'pppk','aktif','2022-09-01','2026-08-27 01:09:44','2026-08-27 01:09:46',NULL),(8,8,8,10,14,NULL,'nik','honor','aktif','2023-01-10','2026-08-27 01:09:44','2026-08-28 19:18:35',NULL),(9,9,1,1,15,'197512122007012044','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:35',NULL),(10,10,7,6,16,'197406071999032001','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:36',NULL),(11,11,7,6,17,'196804081999032004','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:36',NULL),(12,12,7,6,18,'197706212007011017','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:36',NULL),(13,13,7,6,19,'197810051999031003','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:36',NULL),(14,14,7,6,20,'197905062007102008','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:37',NULL),(15,15,7,6,21,'196801121997032002','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:37',NULL),(16,16,7,6,22,'198507122005012001','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:37',NULL),(17,17,7,6,23,'197106122007012034','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:37',NULL),(18,18,7,6,24,'198304252007102001','nip','pns','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:38',NULL),(19,19,7,6,25,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:38',NULL),(20,20,7,6,26,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:38',NULL),(21,21,7,6,27,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:38',NULL),(22,22,7,6,28,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:39',NULL),(23,23,7,6,29,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:39',NULL),(24,24,7,6,30,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:39',NULL),(25,25,7,6,31,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:39',NULL),(26,26,7,6,32,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:40',NULL),(27,27,7,6,33,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:40',NULL),(28,28,7,6,34,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:40',NULL),(29,29,7,6,35,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:41',NULL),(30,30,7,6,36,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:41',NULL),(31,31,7,6,37,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:41',NULL),(32,32,7,6,38,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:41',NULL),(33,33,7,6,39,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:42',NULL),(34,34,7,6,40,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:42',NULL),(35,35,7,6,41,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:42',NULL),(36,36,4,12,42,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:42',NULL),(37,37,6,13,43,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:43',NULL),(38,38,7,6,44,NULL,'nik','honor','aktif',NULL,'2026-08-27 01:09:44','2026-08-28 19:18:43',NULL),(39,39,6,9,45,NULL,'nik','honor','aktif','2026-07-01','2026-08-27 01:09:44','2026-08-28 19:18:43',NULL),(40,71,7,6,11,NULL,'nik','honor','aktif','2026-07-01','2026-08-28 18:48:34','2026-08-28 18:48:34',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extracurricular_attendances`
--

DROP TABLE IF EXISTS `extracurricular_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extracurricular_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `extracurricular_id` bigint unsigned NOT NULL,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `predikat` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ekskul_presensi_unique` (`extracurricular_id`,`student_enrollment_id`,`tanggal`),
  KEY `extracurricular_attendances_student_enrollment_id_foreign` (`student_enrollment_id`),
  KEY `extracurricular_attendances_extracurricular_id_tanggal_index` (`extracurricular_id`,`tanggal`),
  CONSTRAINT `extracurricular_attendances_extracurricular_id_foreign` FOREIGN KEY (`extracurricular_id`) REFERENCES `extracurriculars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `extracurricular_attendances_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extracurricular_attendances`
--

LOCK TABLES `extracurricular_attendances` WRITE;
/*!40000 ALTER TABLE `extracurricular_attendances` DISABLE KEYS */;
INSERT INTO `extracurricular_attendances` VALUES (1,1,1,'2026-08-20','hadir','A',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,1,'2026-08-13','hadir','A',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,2,'2026-08-20','hadir','B',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,2,'2026-08-13','hadir','B',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,3,'2026-08-20','hadir','A',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,3,'2026-08-13','hadir','A',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `extracurricular_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extracurricular_members`
--

DROP TABLE IF EXISTS `extracurricular_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extracurricular_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `extracurricular_id` bigint unsigned NOT NULL,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `tanggal_bergabung` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ekskul_member_unique` (`extracurricular_id`,`student_enrollment_id`),
  KEY `extracurricular_members_student_enrollment_id_foreign` (`student_enrollment_id`),
  CONSTRAINT `extracurricular_members_extracurricular_id_foreign` FOREIGN KEY (`extracurricular_id`) REFERENCES `extracurriculars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `extracurricular_members_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extracurricular_members`
--

LOCK TABLES `extracurricular_members` WRITE;
/*!40000 ALTER TABLE `extracurricular_members` DISABLE KEYS */;
INSERT INTO `extracurricular_members` VALUES (1,1,1,'2026-07-30','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,2,'2026-07-30','2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,3,'2026-07-30','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `extracurricular_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extracurriculars`
--

DROP TABLE IF EXISTS `extracurriculars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extracurriculars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `pembina_id` bigint unsigned DEFAULT NULL,
  `hari` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `lokasi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extracurriculars_slug_unique` (`slug`),
  KEY `extracurriculars_pembina_id_foreign` (`pembina_id`),
  KEY `extracurriculars_created_by_foreign` (`created_by`),
  CONSTRAINT `extracurriculars_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `extracurriculars_pembina_id_foreign` FOREIGN KEY (`pembina_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extracurriculars`
--

LOCK TABLES `extracurriculars` WRITE;
/*!40000 ALTER TABLE `extracurriculars` DISABLE KEYS */;
INSERT INTO `extracurriculars` VALUES (1,'Pramuka','pramuka','Kepramukaan wajib — latihan rutin mingguan.',1,'sabtu','14:00:00','Lapangan utama','aktif',3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `extracurriculars` ENABLE KEYS */;
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
-- Table structure for table `guardian_student`
--

DROP TABLE IF EXISTS `guardian_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardian_student` (
  `guardian_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `relation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`guardian_id`,`student_id`),
  KEY `guardian_student_student_id_foreign` (`student_id`),
  CONSTRAINT `guardian_student_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guardian_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardian_student`
--

LOCK TABLES `guardian_student` WRITE;
/*!40000 ALTER TABLE `guardian_student` DISABLE KEYS */;
INSERT INTO `guardian_student` VALUES (1,1,NULL),(2,29,NULL),(3,29,'ayah'),(4,29,'ibu'),(5,29,'wali'),(6,31,'ayah'),(7,31,'ibu');
/*!40000 ALTER TABLE `guardian_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guardians_user_id_foreign` (`user_id`),
  KEY `guardians_nik_index` (`nik`),
  CONSTRAINT `guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardians`
--

LOCK TABLES `guardians` WRITE;
/*!40000 ALTER TABLE `guardians` DISABLE KEYS */;
INSERT INTO `guardians` VALUES (1,2,'Ibu Ratna Dewi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:43','2026-08-27 01:09:43'),(2,NULL,'ABDUL RAHMAN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 18:31:28','2026-08-27 18:31:28'),(3,NULL,'ABDUL RAHMAN','6172010101010013','Masih Hidup','BANJARMASIN','1982-05-20','7','03','Rp3jt – 5jt','081255556666','2026-08-27 21:57:39','2026-08-27 21:57:39'),(4,NULL,'NURHAYATI','6172010101010014','Masih Hidup','PALANGKA RAYA','1985-09-12','3','07','Rp1jt – 2jt','081277778888','2026-08-27 21:57:39','2026-08-27 21:57:39'),(5,NULL,'H. MOHAMMAD YUSUF','6172010101010015',NULL,'BANJARMASIN','1965-11-02','2','12','Rp2jt – 3jt','081399991111','2026-08-27 21:57:39','2026-08-27 21:57:39'),(6,NULL,'HASANUDIN',NULL,'Masih Hidup',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 22:20:51','2026-08-27 22:20:51'),(7,NULL,'NURUL Hidayah','6172010101010004','Masih Hidup',NULL,'1991-07-10','3','06','Rp1jt-2jt',NULL,'2026-08-27 22:20:51','2026-08-27 22:20:51');
/*!40000 ALTER TABLE `guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homeroom_assignments`
--

DROP TABLE IF EXISTS `homeroom_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homeroom_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_group_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homeroom_assignments_class_group_id_foreign` (`class_group_id`),
  KEY `homeroom_assignments_academic_year_id_foreign` (`academic_year_id`),
  KEY `homeroom_assignments_user_id_foreign` (`user_id`),
  KEY `homeroom_assignments_created_by_foreign` (`created_by`),
  CONSTRAINT `homeroom_assignments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homeroom_assignments_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homeroom_assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `homeroom_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homeroom_assignments`
--

LOCK TABLES `homeroom_assignments` WRITE;
/*!40000 ALTER TABLE `homeroom_assignments` DISABLE KEYS */;
INSERT INTO `homeroom_assignments` VALUES (1,12,1,1,'aktif',3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `homeroom_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_categories`
--

DROP TABLE IF EXISTS `inventory_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_categories`
--

LOCK TABLES `inventory_categories` WRITE;
/*!40000 ALTER TABLE `inventory_categories` DISABLE KEYS */;
INSERT INTO `inventory_categories` VALUES (1,'Elektronik','Perangkat listrik & elektronik (LCD, AC, komputer).','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'Furniture','Meja, kursi, lemari, dan perabotan.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,'Alat Tulis Kantor','ATK dan perlengkapan administrasi.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,'Olahraga','Perlengkapan olahraga & sarana kegiatan fisik.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,'Lab & Peraga','Alat laboratorium dan alat peraga pembelajaran.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,'Perpustakaan','Koleksi buku dan sarana perpustakaan.','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `inventory_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `condition` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_items_code_unique` (`code`),
  KEY `inventory_items_category_id_foreign` (`category_id`),
  KEY `inventory_items_created_by_foreign` (`created_by`),
  CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,1,'INV-202608-001','Proyektor LCD','Epson','EB-X05','EKS-001',4,'unit','baik','Ruang Guru','2025-08-27',25000000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'INV-202608-002','AC Split 2 PK','Daikin','FTC50','AC-001',6,'unit','baik','Ruang Kelas I-A','2025-08-27',90000000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,2,'INV-202608-003','Meja Belajar Siswa',NULL,NULL,NULL,120,'unit','baik','Gudang','2025-08-27',36000000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,2,'INV-202608-004','Kursi Tamu','Olympic','Sofa','KRS-001',2,'set','rusak_ringan','Ruang Tamu','2025-08-27',4500000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,3,'INV-202608-005','Kertas HVS A4','Sidu','70 gsm',NULL,50,'rim','baik','Gudang ATK','2025-08-27',2750000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,4,'INV-202608-006','Bola Sepak','Adidas','Club','BOL-001',10,'buah','baik','Gudang Olahraga','2025-08-27',1000000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,5,'INV-202608-007','Mikroskop','Olympus','CX23','MIC-001',2,'unit','baik','Lab IPA','2025-08-27',18000000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,6,'INV-202608-008','Rak Buku',NULL,NULL,NULL,8,'unit','baik','Perpustakaan','2025-08-27',9600000,'tersedia',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,1,'INV-202608-009','Komputer Guru','Dell','OptiPlex','PC-001',15,'unit','rusak_berat','Ruang Guru','2025-08-27',15000000,'tidak_aktif',NULL,NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_maintenances`
--

DROP TABLE IF EXISTS `inventory_maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` int unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `technician` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'berlangsung',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_maintenances_item_id_foreign` (`item_id`),
  KEY `inventory_maintenances_created_by_foreign` (`created_by`),
  CONSTRAINT `inventory_maintenances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_maintenances_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_maintenances`
--

LOCK TABLES `inventory_maintenances` WRITE;
/*!40000 ALTER TABLE `inventory_maintenances` DISABLE KEYS */;
INSERT INTO `inventory_maintenances` VALUES (1,2,'perawatan','Servis berkala AC ruang kelas.',500000,'2026-07-27','2026-08-06','Toko Elektronik Jaya','selesai',NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,9,'perbaikan','Perbaikan motherboard komputer guru.',1200000,'2026-08-20',NULL,'Toko Komputer Andalan','berlangsung',NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `inventory_maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_mutations`
--

DROP TABLE IF EXISTS `inventory_mutations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_mutations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `from_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `mutation_date` date NOT NULL,
  `reason` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_mutations_item_id_foreign` (`item_id`),
  KEY `inventory_mutations_approved_by_foreign` (`approved_by`),
  KEY `inventory_mutations_created_by_foreign` (`created_by`),
  CONSTRAINT `inventory_mutations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_mutations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_mutations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_mutations`
--

LOCK TABLES `inventory_mutations` WRITE;
/*!40000 ALTER TABLE `inventory_mutations` DISABLE KEYS */;
INSERT INTO `inventory_mutations` VALUES (1,1,'Ruang Aula','Ruang Guru',1,'2026-06-27','Kebutuhan pengajaran',3,'disetujui',NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `inventory_mutations` ENABLE KEYS */;
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
-- Table structure for table `letter_categories`
--

DROP TABLE IF EXISTS `letter_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `letter_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_categories`
--

LOCK TABLES `letter_categories` WRITE;
/*!40000 ALTER TABLE `letter_categories` DISABLE KEYS */;
INSERT INTO `letter_categories` VALUES (1,'Undangan','Surat undangan acara/meeting',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'Pemberitahuan','Surat pemberitahuan resmi',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,'Edaran','Surat edaran ke seluruh unit',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,'Nota Dinas','Nota dinas internal',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,'Surat Tugas','Penugasan pegawai/guru',5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,'Surat Keterangan','Surat keterangan resmi',6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,'Laporan','Surat laporan kegiatan',7,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,'Permohonan','Surat permohonan',8,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,'Lainnya','Kategori lainnya',99,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `letter_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letters`
--

DROP TABLE IF EXISTS `letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `letters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `from_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('diterima','diproses','selesai','arsip') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diterima',
  `priority` enum('biasa','penting','segera','rahasia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'biasa',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disposition_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disposition_note` text COLLATE utf8mb4_unicode_ci,
  `file_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `letters_recorded_by_foreign` (`recorded_by`),
  KEY `letters_academic_year_id_foreign` (`academic_year_id`),
  KEY `letters_type_status_index` (`type`,`status`),
  KEY `letters_type_date_index` (`type`,`date`),
  KEY `letters_from_to_index` (`from_to`),
  KEY `letters_subject_index` (`subject`),
  CONSTRAINT `letters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `letters_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letters`
--

LOCK TABLES `letters` WRITE;
/*!40000 ALTER TABLE `letters` DISABLE KEYS */;
INSERT INTO `letters` VALUES (1,'masuk','001/SM/08/2026','2026-08-22','Kantor Kementerian Agama Kabupaten','Edaran Pelaksanaan Ujian Akhir Semester','Edaran tentang jadwal dan teknis pelaksanaan UAS semester ganjil tahun ajaran 2026/2027.','diterima','penting','Edaran','Wakamad Kurikulum','Mohon ditindaklanjuti untuk penyebaran ke guru mapel.',NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'masuk','002/SM/08/2026','2026-08-24','Dinas Pendidikan Provinsi','Undangan Rapat Koordinasi Kepala Madrasah','Undangan rapat koordinasi untuk membahas programsemester depan.','diproses','biasa','Undangan','Kepala Madrasah',NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,'masuk','003/SM/08/2026','2026-08-26','Yayasan Pendidikan Madrasah','Pemberitahuan Libur Nasional','Pemberitahuan hari libur nasional dan cuti bersama.','diterima','biasa','Pemberitahuan',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,'masuk',NULL,'2026-08-27','PT. Maju Bersama','Penawaran Kerja Sama Program Beasiswa','Penawaran program beasiswa untuk siswa berprestasi.','diterima','penting','Lainnya',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,'masuk','005/SM/07/2026','2026-08-20','Kantor Kementerian Agama Kabupaten','Surat Tugas Pelatihan Guru','Surat tugas untuk mengikuti pelatihan metodologi pembelajaran.','selesai','biasa','Surat Tugas','Wakamad Kurikulum','Daftarkan 3 guru untuk pelatihan.',NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,'keluar','001/TK/08/2026','2026-08-23','Kantor Kementerian Agama Kabupaten','Laporan Pelaksanaan Ujian Akhir Semester','Laporan hasil pelaksanaan UAS semester ganjil.','selesai','penting','Laporan',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,'keluar','001/TK/08/2026','2026-08-25','Dinas Pendidikan Provinsi','Surat Undangan Rapat Koordinasi','Undangan rapat koordinasi kepala madrasah se-kabupaten.','diproses','biasa','Undangan',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,'keluar','001/TK/08/2026','2026-08-27','Kepala Dinas Pendidikan','Permohonan Izin Kegiatan Outbound','Permohonan izin untuk melaksanakan kegiatan outbound siswa.','diterima','segera','Permohonan',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,'keluar','001/TK/08/2026','2026-08-21','Orang Tua Siswa','Surat Keterangan Aktif Siswa','Surat keterangan untuk keperluan administrasi.','arsip','biasa','Surat Keterangan',NULL,NULL,NULL,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_books`
--

DROP TABLE IF EXISTS `library_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` smallint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `isbn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_qty` smallint unsigned NOT NULL DEFAULT '1',
  `available_qty` smallint unsigned NOT NULL DEFAULT '1',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_ebook` tinyint(1) NOT NULL DEFAULT '0',
  `ebook_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library_books_code_unique` (`code`),
  KEY `library_books_category_id_foreign` (`category_id`),
  KEY `library_books_created_by_foreign` (`created_by`),
  CONSTRAINT `library_books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `library_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `library_books_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_books`
--

LOCK TABLES `library_books` WRITE;
/*!40000 ALTER TABLE `library_books` DISABLE KEYS */;
INSERT INTO `library_books` VALUES (1,'BUK-202608-001','Laskar Pelangi','Andrea Hirata','Bentang Pustaka',2005,1,'978-979-3062-79-4',3,2,'Rak F-1',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'BUK-202608-002','Tenggelamnya Kapal Van Der Wijck','Hamka','Republika',2016,1,'978-602-291-124-9',2,2,'Rak F-2',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,'BUK-202608-003','Matematika Kelas VI','Tim Penulis','Kementerian Agama',2024,3,'978-602-XXX-001-X',5,5,'Rak P-1',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,'BUK-202608-004','Bahasa Arab Kelas V','Tim Penulis','Kementerian Agama',2024,3,NULL,4,4,'Rak P-2',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,'BUK-202608-005','Ensiklopedia Sains','Khairul Amri','Erlangga',2020,4,'978-602-291-200-X',2,2,'Rak R-1',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,'BUK-202608-006','Buku Doa Harian','Abdul Somad','Gema Insani',2022,2,NULL,3,3,'Rak A-1',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,'BUK-202608-007','Fiqih Islam','Hasan Bashari','Bumi Aksara',2021,2,NULL,2,2,'Rak A-2',NULL,0,NULL,NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,'BUK-202608-008','Mengenal Al-Qur\'an untuk Anak','Khalid Muhammad','Republika',2023,2,NULL,3,3,'Rak A-1',NULL,1,'https://drive.google.com/file/d/example-ebook',NULL,'tersedia',3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `library_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_categories`
--

DROP TABLE IF EXISTS `library_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_categories`
--

LOCK TABLES `library_categories` WRITE;
/*!40000 ALTER TABLE `library_categories` DISABLE KEYS */;
INSERT INTO `library_categories` VALUES (1,'Fiksi','Novel, cerpen, dan karya fiksi lainnya.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'Agama','Buku-buku keagamaan dan keislaman.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,'Pelajaran','Buku pelajaran kurikulum madrasah.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,'Referensi','Ensiklopedia, kamus, dan buku rujukan.','2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,'Umum','Buku umum, motivasi, dan keterampilan hidup.','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `library_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_loans`
--

DROP TABLE IF EXISTS `library_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `book_id` bigint unsigned NOT NULL,
  `member_id` bigint unsigned NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dipinjam',
  `note` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_loans_book_id_foreign` (`book_id`),
  KEY `library_loans_member_id_foreign` (`member_id`),
  KEY `library_loans_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `library_loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `library_loans_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `library_members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `library_loans_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_loans`
--

LOCK TABLES `library_loans` WRITE;
/*!40000 ALTER TABLE `library_loans` DISABLE KEYS */;
INSERT INTO `library_loans` VALUES (1,1,1,'2026-08-20','2026-09-03',NULL,'dipinjam','Peminjaman contoh',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,3,1,'2026-07-27','2026-08-13','2026-08-20','dikembalikan',NULL,3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `library_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_members`
--

DROP TABLE IF EXISTS `library_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `member_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `joined_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library_members_member_no_unique` (`member_no`),
  KEY `library_members_student_id_foreign` (`student_id`),
  KEY `library_members_employee_id_foreign` (`employee_id`),
  CONSTRAINT `library_members_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `library_members_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_members`
--

LOCK TABLES `library_members` WRITE;
/*!40000 ALTER TABLE `library_members` DISABLE KEYS */;
INSERT INTO `library_members` VALUES (1,'siswa',1,NULL,'ANG-2026-001','Aisyah Nur Azizah','aktif','2026-05-27','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'pegawai',NULL,1,'ANG-2026-002','Drs. H. Ahmad Fauzi, M.Pd.','aktif','2026-06-27','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `library_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_albums`
--

DROP TABLE IF EXISTS `media_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_albums` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publik',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_albums_slug_unique` (`slug`),
  KEY `media_albums_created_by_foreign` (`created_by`),
  CONSTRAINT `media_albums_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_albums`
--

LOCK TABLES `media_albums` WRITE;
/*!40000 ALTER TABLE `media_albums` DISABLE KEYS */;
INSERT INTO `media_albums` VALUES (1,'Dokumentasi MPLS 2026','dokumentasi-mpls-2026','Kegiatan','Momen masa pengenalan lingkungan sekolah tahun ajaran 2026/2027.','galeri/demo-mpls-1.png','publik',9,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,'Arsip Internal Rapat','arsip-internal-rapat','Internal','Dokumentasi internal — tidak dipublikasikan.',NULL,'privat',9,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `media_albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_items`
--

DROP TABLE IF EXISTS `media_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint unsigned NOT NULL,
  `tipe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_items_album_id_sort_order_index` (`album_id`,`sort_order`),
  CONSTRAINT `media_items_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `media_albums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_items`
--

LOCK TABLES `media_items` WRITE;
/*!40000 ALTER TABLE `media_items` DISABLE KEYS */;
INSERT INTO `media_items` VALUES (1,1,'foto','galeri/demo-mpls-1.png',NULL,'Sesi perkenalan dewan guru',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'foto','galeri/demo-mpls-2.png',NULL,'Perkenalan tata tertib madrasah',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,'foto','galeri/demo-mpls-3.png',NULL,'Penutupan MPLS',3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `media_items` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_23_000001_add_role_to_users_table',1),(5,'2026_08_23_000002_create_skeleton_tables',1),(6,'2026_08_23_000003_make_reports_versioned',1),(7,'2026_08_23_000004_create_kepegawaian_tables',1),(8,'2026_08_23_000005_add_sort_order_to_subjects',1),(9,'2026_08_23_000006_add_person_id_to_students',1),(10,'2026_08_23_000007_create_attendances_table',1),(11,'2026_08_23_000008_create_schedules_table',1),(12,'2026_08_23_000009_create_schedule_models_tables',1),(13,'2026_08_23_043809_create_activity_log_table',1),(14,'2026_08_24_000010_create_teaching_journals_table',1),(15,'2026_08_24_000011_create_attendance_reviews_table',1),(16,'2026_08_24_000012_create_report_items_table',1),(17,'2026_08_24_000013_consolidate_legacy_reports',1),(18,'2026_08_24_000014_create_tuition_tables',1),(19,'2026_08_24_000015_add_student_id_to_users',1),(20,'2026_08_24_000016_create_articles_table',1),(21,'2026_08_24_000017_create_agenda_table',1),(22,'2026_08_24_000018_create_achievements_table',1),(23,'2026_08_24_000019_create_offenses_table',1),(24,'2026_08_24_000020_create_media_tables',1),(25,'2026_08_24_000021_create_extracurricular_tables',1),(26,'2026_08_25_000022_create_user_roles_table',1),(27,'2026_08_25_000023_add_soft_deletes_to_users_table',1),(28,'2026_08_25_000024_create_counseling_sessions_table',1),(29,'2026_08_25_000025_create_homeroom_assignments_table',1),(30,'2026_08_25_163559_create_inventory_tables',1),(31,'2026_08_26_000026_create_library_tables',1),(32,'2026_08_26_000027_create_letter_tables',1),(33,'2026_08_26_000029_create_ppdb_registrations_table',1),(34,'2026_08_26_000030_create_nis_counters_table',1),(35,'2026_08_26_000031_make_student_nis_nullable',1),(36,'2026_08_26_000032_drop_nis_counters',1),(37,'2026_08_26_014455_create_settings_table',1),(38,'2026_08_26_033031_alter_guardians_user_id_nullable',1),(39,'2026_08_26_092638_create_ppdb_interests_table',1),(40,'2026_08_26_100000_create_pembiasaan_materi_table',1),(41,'2026_08_26_100001_create_pembiasaan_materi_periode_table',1),(42,'2026_08_26_100002_create_pembiasaan_nilai_table',1),(43,'2026_08_27_000100_create_ppi_exam_tables',1),(44,'2026_08_27_000101_create_employee_attendances_table',1),(45,'2026_08_27_000102_create_rooms_table',2),(46,'2026_08_27_000103_create_student_profiles_table',3),(47,'2026_08_27_000104_add_origin_to_student_profiles',4),(48,'2026_08_27_000105_add_master_student_columns',5),(49,'2026_08_27_000106_backfill_master_from_profiles',5),(50,'2026_08_27_000107_create_mutasi_registrations_table',6),(51,'2026_08_27_000108_create_mutasi_interests_table',6),(52,'2026_08_27_000109_add_ppdb_fields_to_mutasi_registrations',7),(53,'2026_08_28_000110_create_student_mutations_table',8),(54,'2026_08_28_000111_add_account_provisioning_columns',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mutasi_interests`
--

DROP TABLE IF EXISTS `mutasi_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mutasi_interests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mutasi_interests_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mutasi_interests`
--

LOCK TABLES `mutasi_interests` WRITE;
/*!40000 ALTER TABLE `mutasi_interests` DISABLE KEYS */;
/*!40000 ALTER TABLE `mutasi_interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mutasi_registrations`
--

DROP TABLE IF EXISTS `mutasi_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mutasi_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','submitted','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `rejection_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis_asal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `religion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Islam',
  `birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `previous_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hobby` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ambition` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `child_order` tinyint unsigned DEFAULT NULL,
  `sibling_count` tinyint unsigned DEFAULT NULL,
  `ever_tk` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ever_paud` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `origin_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_nsm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_npsn` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas_asal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas_tujuan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_pindah` text COLLATE utf8mb4_unicode_ci,
  `tanggal_mutasi` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_rekomendasi` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_rapor` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_kk` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_akta` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_photo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imm_hepb` tinyint(1) NOT NULL DEFAULT '0',
  `imm_polio` tinyint(1) NOT NULL DEFAULT '0',
  `imm_bcg` tinyint(1) NOT NULL DEFAULT '0',
  `imm_campak` tinyint(1) NOT NULL DEFAULT '0',
  `imm_dpt` tinyint(1) NOT NULL DEFAULT '0',
  `imm_covid` tinyint(1) NOT NULL DEFAULT '0',
  `dis_deaf` tinyint(1) NOT NULL DEFAULT '0',
  `dis_blind` tinyint(1) NOT NULL DEFAULT '0',
  `dis_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `dis_intellectual` tinyint(1) NOT NULL DEFAULT '0',
  `dis_behavioral` tinyint(1) NOT NULL DEFAULT '0',
  `dis_slow_learner` tinyint(1) NOT NULL DEFAULT '0',
  `dis_communication` tinyint(1) NOT NULL DEFAULT '0',
  `dis_gifted` tinyint(1) NOT NULL DEFAULT '0',
  `residence_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commute_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_head_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_birth_date` date DEFAULT NULL,
  `father_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_birth_date` date DEFAULT NULL,
  `mother_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_birth_date` date DEFAULT NULL,
  `guardian_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kks` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_pkh` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_ownership` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_kk_wali` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_ijazah` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mutasi_registrations_registration_no_unique` (`registration_no`),
  KEY `mutasi_registrations_academic_year_id_foreign` (`academic_year_id`),
  KEY `mutasi_registrations_student_id_foreign` (`student_id`),
  CONSTRAINT `mutasi_registrations_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mutasi_registrations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mutasi_registrations`
--

LOCK TABLES `mutasi_registrations` WRITE;
/*!40000 ALTER TABLE `mutasi_registrations` DISABLE KEYS */;
INSERT INTO `mutasi_registrations` VALUES (1,'MUT-2026-001','submitted',NULL,NULL,1,NULL,'127.0.0.1','2026-08-28 03:51:20','2026-08-28 03:51:20','RAIHAN PUTRA RAMADHAN','6172010101010021','0012345679','MTs.2024.015','L','Islam','Banjarmasin','2016-04-10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MTs Negeri 1 Banjarmasin','111262710007','00112234','Jl. Perintis No. 12, Banjarmasin','VIII-A','VIII-A','Mengikuti orang tua yang bertugas di Palangka Raya.','2026-07-20','Jl. RTA Milono No. 9','Kalimantan Tengah','Palangka Raya','Jekan Raya','Palangka','001','002','73112','085234567890','raihan@gmail.com','HENDRA GUNAWAN','6172010101010022','PNS','081211112222','DEWI LESTARI','6172010101010023','Ibu Rumah Tangga','081233334444',NULL,NULL,NULL,'https://drive.google.com/file/d/rekomendasi-raihan','https://drive.google.com/file/d/rapor-raihan','https://drive.google.com/file/d/kk-raihan','https://drive.google.com/file/d/akta-raihan','https://drive.google.com/file/d/foto-raihan',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'MUT-2026-002','submitted',NULL,NULL,1,NULL,'127.0.0.1','2026-08-28 03:51:20','2026-08-28 03:51:20','NUR HIKMAH','6172010101010031',NULL,NULL,'P','Islam','Palangka Raya','2017-02-14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MIS Al-Ikhlas Mulia',NULL,NULL,NULL,'VII-A','VII-A','Domisili pindah ke lingkungan madrasah.',NULL,'Jl. Garuda No. 5','Kalimantan Tengah','Palangka Raya','Pahandut','Pahandut','003','001','73111','085312345678',NULL,'DEDI KURNIAWAN',NULL,NULL,NULL,'SITI AMINAH',NULL,NULL,NULL,NULL,NULL,NULL,'https://drive.google.com/file/d/rekomendasi-hikmah',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'MUT-2026-003','submitted',NULL,NULL,1,NULL,'127.0.0.1','2026-08-28 03:51:20','2026-08-28 04:34:50','BIMA ARDIANSYAH','6172010101010041',NULL,NULL,'L','Islam','Sampit','2015-09-01',NULL,'Olah Raga','PNS',NULL,NULL,'PERNAH',NULL,NULL,'MTs Darul Ulum Sampit',NULL,NULL,NULL,'IX-B','IX-A','Pindah tempat tinggal orang tua.',NULL,'Jl. Beliang No. 3','Kalimantan Tengah','Palangka Raya','Bukit Batu','Tangkiling',NULL,NULL,NULL,'085398765432',NULL,'SUYONO',NULL,'01',NULL,'RATNA SARI',NULL,'01',NULL,NULL,NULL,NULL,'https://drive.google.com/file/d/rekomendasi-bima',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'Tinggal dgn Ortu','<5km','Jalan Kaki','1-10 menit',NULL,NULL,NULL,'Masih Hidup',NULL,NULL,'0','< Rp500rb','Masih Hidup',NULL,NULL,'0','< Rp500rb',NULL,'0',NULL,NULL,NULL,NULL,NULL,'Milik Sendiri',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `mutasi_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offenses`
--

DROP TABLE IF EXISTS `offenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poin` tinyint unsigned NOT NULL DEFAULT '0',
  `tanggal_kejadian` date NOT NULL,
  `kronologi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelapor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemanggilan_ortu` tinyint(1) NOT NULL DEFAULT '0',
  `surat_peringatan` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_penyelesaian` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'proses',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offenses_created_by_foreign` (`created_by`),
  KEY `offenses_student_id_tanggal_kejadian_index` (`student_id`,`tanggal_kejadian`),
  CONSTRAINT `offenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `offenses_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offenses`
--

LOCK TABLES `offenses` WRITE;
/*!40000 ALTER TABLE `offenses` DISABLE KEYS */;
INSERT INTO `offenses` VALUES (1,1,'Terlambat Masuk','ringan',2,'2026-08-20','Datang terlambat setelah bel masuk tanpa keterangan.','Guru Piket',NULL,'Pembinaan lisan oleh wali kelas.',0,NULL,'selesai',3,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `offenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizational_units`
--

DROP TABLE IF EXISTS `organizational_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizational_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizational_units_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizational_units`
--

LOCK TABLES `organizational_units` WRITE;
/*!40000 ALTER TABLE `organizational_units` DISABLE KEYS */;
INSERT INTO `organizational_units` VALUES (1,'PIMPINAN','Pimpinan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(2,'KURIKULUM','Kurikulum','2026-08-27 01:09:44','2026-08-27 01:09:44'),(3,'KESISWAAN','Kesiswaan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(4,'SARPRAS','Sarpras','2026-08-27 01:09:44','2026-08-27 01:09:44'),(5,'HUMAS','Humas','2026-08-27 01:09:44','2026-08-27 01:09:44'),(6,'TU','Tata Usaha','2026-08-27 01:09:44','2026-08-27 01:09:44'),(7,'GURU','Guru','2026-08-27 01:09:44','2026-08-27 01:09:44'),(8,'PERPUS','Perpustakaan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(9,'LAB','Laboratorium','2026-08-27 01:09:44','2026-08-27 01:09:44');
/*!40000 ALTER TABLE `organizational_units` ENABLE KEYS */;
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
-- Table structure for table `pembiasaan_materi`
--

DROP TABLE IF EXISTS `pembiasaan_materi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembiasaan_materi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `modul` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_urut` smallint unsigned NOT NULL,
  `nama_materi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembiasaan_materi_modul_no_urut_index` (`modul`,`no_urut`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembiasaan_materi`
--

LOCK TABLES `pembiasaan_materi` WRITE;
/*!40000 ALTER TABLE `pembiasaan_materi` DISABLE KEYS */;
INSERT INTO `pembiasaan_materi` VALUES (1,'ppi',1,'Do\'a Masuk Rumah/Ruangan',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,'ppi',2,'Do\'a Mau Tidur',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,'ppi',3,'Do\'a Bangun Tidur',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,'ppi',4,'Do\'a Masuk WC',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,'ppi',5,'Do\'a Keluar WC',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,'ppi',6,'Do\'a Bercermin',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,'ppi',7,'Do\'a Senandung Al-Qur\'an',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,'ppi',8,'Do\'a Naik Kendaraan Darat',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,'ppi',9,'Do\'a Naik Kendaraan Air/Laut',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,'ppi',10,'Do\'a Keluar Rumah',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,'ppi',11,'Do\'a Mau Belajar',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,'ppi',12,'Do\'a Masuk Masjid',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,'ppi',13,'Do\'a Keluar Masjid',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(14,'ppi',14,'Do\'a Untuk Kedua Orang Tua',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(15,'ppi',15,'Do\'a Kelancaran Berbicara',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(16,'ppi',16,'Do\'a Sesudah Adzan',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(17,'ppi',17,'Do\'a Sesudah Iqamah',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(18,'ppi',18,'Lafaz Niat Wudhu',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(19,'ppi',19,'Do\'a Sesudah Wudhu',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(20,'ppi',20,'Niat Tayamum',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(21,'ppi',21,'Lafaz Niat Shalat Fardhu',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(22,'ppi',22,'Do\'a Iftitah',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(23,'ppi',23,'Bacaan Rukuk',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(24,'ppi',24,'Bacaan I\'tidal',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(25,'ppi',25,'Bacaan Sujud',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(26,'ppi',26,'Bacaan Duduk diantara Dua Sujud',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(27,'ppi',27,'Tahyat Awal',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(28,'ppi',28,'Tahyat Akhir',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(29,'ppi',29,'Do\'a Qunut',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(30,'ppi',30,'Do\'a Sebelum Salam',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(31,'ppi',31,'Do\'a Salamat',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(32,'ppi',32,'Wirid Setelah Shalat',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(33,'ppi',33,'Niat Shalat Jenazah',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(34,'ppi',34,'Takbir Pertama',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(35,'ppi',35,'Takbir Kedua',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(36,'ppi',36,'Takbir Ketiga',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(37,'ppi',37,'Takbir Keempat',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(38,'tahfidz',1,'Al-Fatihah','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(39,'tahfidz',2,'An-Nas','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(40,'tahfidz',3,'Al-Falaq','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(41,'tahfidz',4,'Al-Ikhlas','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(42,'tahfidz',5,'Al-Lahab','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(43,'tahfidz',6,'An-Nashr','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(44,'tahfidz',7,'Al-Kafirun','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(45,'tahfidz',8,'Al-Kausar','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(46,'tahfidz',9,'Al-Asr','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(47,'tahfidz',10,'Al-Quraisy','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(48,'tahfidz',11,'Al-Fiil','surah','2026-08-27 01:09:45','2026-08-27 01:09:45'),(49,'tahfidz',12,'Al-Humazah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(50,'tahfidz',13,'Al-Ma\'un','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(51,'tahfidz',14,'At-Takasur','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(52,'tahfidz',15,'Al-Qari\'ah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(53,'tahfidz',16,'Al-Zalzalah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(54,'tahfidz',17,'Al-\'Adiyat','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(55,'tahfidz',18,'At-Tiin','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(56,'tahfidz',19,'Al-Qadr','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(57,'tahfidz',20,'Al-\'Alaq','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(58,'tahfidz',21,'Al-Bayyinah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(59,'tahfidz',22,'Al-Insyirah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(60,'tahfidz',23,'Ad-Dhuha','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(61,'tahfidz',24,'Al-Lail','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(62,'tahfidz',25,'Asy-Syams','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(63,'tahfidz',26,'Al-Balad','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(64,'tahfidz',27,'Al-Fajr','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(65,'tahfidz',28,'Al-A\'la','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(66,'tahfidz',29,'Al-Ghasyiyah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(67,'tahfidz',30,'At-Thariq','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(68,'tahfidz',31,'Al-Buruj','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(69,'tahfidz',32,'Al-Insyiqaq','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(70,'tahfidz',33,'Al-Mutaffifin','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(71,'tahfidz',34,'Al-Infitar','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(72,'tahfidz',35,'At-Takwir','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(73,'tahfidz',36,'\'Abasa','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(74,'tahfidz',37,'An-Nazi\'at','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(75,'tahfidz',38,'An-Naba\'','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(76,'tahfidz',39,'Al-Mulk','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(77,'tahfidz',40,'Al-Waqi\'ah','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(78,'tahfidz',41,'Yaasin','surah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(79,'tahfidz',42,'Hadits tentang Menyayangi Anak Yatim','hadits','2026-08-27 01:09:46','2026-08-27 01:09:46'),(80,'tahfidz',43,'Hadits tentang Taqwa','hadits','2026-08-27 01:09:46','2026-08-27 01:09:46'),(81,'tahfidz',44,'Hadits Ciri-ciri Orang Munafiq','hadits','2026-08-27 01:09:46','2026-08-27 01:09:46'),(82,'tahfidz',45,'Hadits tentang Keutamaan Memberi','hadits','2026-08-27 01:09:46','2026-08-27 01:09:46'),(83,'tahfidz',46,'Hadits tentang Amal Sholeh','hadits','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `pembiasaan_materi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembiasaan_materi_periode`
--

DROP TABLE IF EXISTS `pembiasaan_materi_periode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembiasaan_materi_periode` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `materi_id` bigint unsigned NOT NULL,
  `kelas` tinyint unsigned NOT NULL,
  `semester` tinyint unsigned NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembiasaan_materi_periode_materi_id_kelas_semester_unique` (`materi_id`,`kelas`,`semester`),
  KEY `pembiasaan_materi_periode_materi_id_aktif_index` (`materi_id`,`aktif`),
  CONSTRAINT `pembiasaan_materi_periode_materi_id_foreign` FOREIGN KEY (`materi_id`) REFERENCES `pembiasaan_materi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=997 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembiasaan_materi_periode`
--

LOCK TABLES `pembiasaan_materi_periode` WRITE;
/*!40000 ALTER TABLE `pembiasaan_materi_periode` DISABLE KEYS */;
INSERT INTO `pembiasaan_materi_periode` VALUES (1,1,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,1,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,1,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,1,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,1,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,1,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,1,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,1,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,1,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,1,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,2,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(14,2,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(15,2,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(16,2,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(17,2,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(18,2,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(19,2,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(20,2,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(21,2,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(22,2,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(23,2,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(24,2,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(25,3,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(26,3,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(27,3,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(28,3,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(29,3,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(30,3,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(31,3,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(32,3,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(33,3,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(34,3,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(35,3,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(36,3,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(37,4,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(38,4,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(39,4,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(40,4,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(41,4,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(42,4,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(43,4,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(44,4,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(45,4,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(46,4,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(47,4,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(48,4,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(49,5,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(50,5,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(51,5,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(52,5,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(53,5,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(54,5,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(55,5,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(56,5,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(57,5,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(58,5,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(59,5,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(60,5,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(61,6,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(62,6,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(63,6,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(64,6,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(65,6,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(66,6,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(67,6,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(68,6,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(69,6,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(70,6,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(71,6,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(72,6,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(73,7,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(74,7,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(75,7,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(76,7,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(77,7,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(78,7,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(79,7,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(80,7,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(81,7,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(82,7,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(83,7,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(84,7,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(85,8,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(86,8,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(87,8,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(88,8,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(89,8,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(90,8,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(91,8,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(92,8,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(93,8,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(94,8,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(95,8,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(96,8,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(97,9,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(98,9,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(99,9,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(100,9,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(101,9,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(102,9,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(103,9,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(104,9,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(105,9,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(106,9,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(107,9,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(108,9,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(109,10,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(110,10,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(111,10,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(112,10,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(113,10,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(114,10,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(115,10,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(116,10,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(117,10,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(118,10,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(119,10,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(120,10,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(121,11,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(122,11,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(123,11,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(124,11,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(125,11,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(126,11,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(127,11,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(128,11,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(129,11,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(130,11,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(131,11,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(132,11,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(133,12,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(134,12,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(135,12,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(136,12,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(137,12,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(138,12,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(139,12,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(140,12,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(141,12,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(142,12,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(143,12,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(144,12,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(145,13,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(146,13,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(147,13,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(148,13,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(149,13,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(150,13,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(151,13,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(152,13,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(153,13,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(154,13,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(155,13,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(156,13,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(157,14,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(158,14,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(159,14,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(160,14,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(161,14,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(162,14,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(163,14,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(164,14,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(165,14,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(166,14,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(167,14,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(168,14,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(169,15,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(170,15,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(171,15,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(172,15,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(173,15,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(174,15,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(175,15,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(176,15,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(177,15,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(178,15,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(179,15,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(180,15,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(181,16,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(182,16,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(183,16,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(184,16,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(185,16,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(186,16,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(187,16,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(188,16,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(189,16,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(190,16,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(191,16,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(192,16,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(193,17,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(194,17,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(195,17,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(196,17,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(197,17,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(198,17,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(199,17,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(200,17,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(201,17,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(202,17,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(203,17,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(204,17,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(205,18,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(206,18,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(207,18,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(208,18,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(209,18,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(210,18,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(211,18,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(212,18,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(213,18,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(214,18,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(215,18,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(216,18,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(217,19,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(218,19,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(219,19,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(220,19,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(221,19,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(222,19,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(223,19,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(224,19,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(225,19,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(226,19,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(227,19,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(228,19,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(229,20,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(230,20,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(231,20,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(232,20,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(233,20,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(234,20,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(235,20,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(236,20,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(237,20,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(238,20,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(239,20,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(240,20,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(241,21,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(242,21,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(243,21,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(244,21,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(245,21,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(246,21,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(247,21,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(248,21,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(249,21,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(250,21,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(251,21,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(252,21,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(253,22,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(254,22,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(255,22,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(256,22,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(257,22,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(258,22,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(259,22,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(260,22,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(261,22,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(262,22,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(263,22,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(264,22,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(265,23,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(266,23,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(267,23,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(268,23,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(269,23,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(270,23,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(271,23,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(272,23,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(273,23,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(274,23,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(275,23,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(276,23,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(277,24,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(278,24,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(279,24,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(280,24,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(281,24,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(282,24,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(283,24,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(284,24,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(285,24,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(286,24,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(287,24,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(288,24,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(289,25,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(290,25,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(291,25,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(292,25,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(293,25,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(294,25,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(295,25,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(296,25,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(297,25,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(298,25,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(299,25,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(300,25,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(301,26,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(302,26,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(303,26,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(304,26,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(305,26,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(306,26,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(307,26,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(308,26,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(309,26,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(310,26,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(311,26,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(312,26,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(313,27,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(314,27,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(315,27,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(316,27,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(317,27,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(318,27,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(319,27,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(320,27,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(321,27,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(322,27,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(323,27,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(324,27,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(325,28,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(326,28,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(327,28,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(328,28,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(329,28,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(330,28,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(331,28,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(332,28,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(333,28,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(334,28,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(335,28,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(336,28,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(337,29,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(338,29,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(339,29,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(340,29,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(341,29,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(342,29,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(343,29,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(344,29,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(345,29,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(346,29,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(347,29,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(348,29,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(349,30,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(350,30,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(351,30,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(352,30,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(353,30,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(354,30,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(355,30,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(356,30,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(357,30,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(358,30,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(359,30,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(360,30,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(361,31,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(362,31,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(363,31,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(364,31,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(365,31,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(366,31,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(367,31,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(368,31,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(369,31,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(370,31,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(371,31,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(372,31,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(373,32,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(374,32,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(375,32,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(376,32,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(377,32,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(378,32,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(379,32,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(380,32,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(381,32,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(382,32,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(383,32,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(384,32,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(385,33,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(386,33,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(387,33,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(388,33,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(389,33,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(390,33,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(391,33,4,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(392,33,4,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(393,33,5,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(394,33,5,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(395,33,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(396,33,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(397,34,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(398,34,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(399,34,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(400,34,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(401,34,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(402,34,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(403,34,4,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(404,34,4,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(405,34,5,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(406,34,5,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(407,34,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(408,34,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(409,35,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(410,35,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(411,35,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(412,35,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(413,35,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(414,35,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(415,35,4,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(416,35,4,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(417,35,5,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(418,35,5,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(419,35,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(420,35,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(421,36,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(422,36,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(423,36,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(424,36,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(425,36,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(426,36,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(427,36,4,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(428,36,4,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(429,36,5,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(430,36,5,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(431,36,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(432,36,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(433,37,1,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(434,37,1,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(435,37,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(436,37,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(437,37,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(438,37,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(439,37,4,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(440,37,4,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(441,37,5,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(442,37,5,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(443,37,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(444,37,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(445,38,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(446,38,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(447,38,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(448,38,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(449,38,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(450,38,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(451,38,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(452,38,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(453,38,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(454,38,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(455,38,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(456,38,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(457,39,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(458,39,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(459,39,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(460,39,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(461,39,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(462,39,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(463,39,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(464,39,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(465,39,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(466,39,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(467,39,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(468,39,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(469,40,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(470,40,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(471,40,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(472,40,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(473,40,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(474,40,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(475,40,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(476,40,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(477,40,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(478,40,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(479,40,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(480,40,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(481,41,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(482,41,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(483,41,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(484,41,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(485,41,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(486,41,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(487,41,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(488,41,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(489,41,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(490,41,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(491,41,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(492,41,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(493,42,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(494,42,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(495,42,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(496,42,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(497,42,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(498,42,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(499,42,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(500,42,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(501,42,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(502,42,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(503,42,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(504,42,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(505,43,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(506,43,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(507,43,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(508,43,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(509,43,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(510,43,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(511,43,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(512,43,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(513,43,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(514,43,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(515,43,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(516,43,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(517,44,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(518,44,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(519,44,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(520,44,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(521,44,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(522,44,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(523,44,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(524,44,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(525,44,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(526,44,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(527,44,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(528,44,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(529,45,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(530,45,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(531,45,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(532,45,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(533,45,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(534,45,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(535,45,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(536,45,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(537,45,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(538,45,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(539,45,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(540,45,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(541,46,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(542,46,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(543,46,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(544,46,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(545,46,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(546,46,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(547,46,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(548,46,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(549,46,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(550,46,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(551,46,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(552,46,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(553,47,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(554,47,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(555,47,2,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(556,47,2,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(557,47,3,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(558,47,3,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(559,47,4,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(560,47,4,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(561,47,5,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(562,47,5,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(563,47,6,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(564,47,6,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(565,48,1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(566,48,1,2,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(567,48,2,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(568,48,2,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(569,48,3,1,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(570,48,3,2,0,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(571,48,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(572,48,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(573,48,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(574,48,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(575,48,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(576,48,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(577,49,1,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(578,49,1,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(579,49,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(580,49,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(581,49,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(582,49,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(583,49,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(584,49,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(585,49,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(586,49,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(587,49,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(588,49,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(589,50,1,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(590,50,1,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(591,50,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(592,50,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(593,50,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(594,50,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(595,50,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(596,50,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(597,50,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(598,50,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(599,50,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(600,50,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(601,51,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(602,51,1,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(603,51,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(604,51,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(605,51,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(606,51,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(607,51,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(608,51,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(609,51,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(610,51,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(611,51,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(612,51,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(613,52,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(614,52,1,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(615,52,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(616,52,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(617,52,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(618,52,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(619,52,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(620,52,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(621,52,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(622,52,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(623,52,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(624,52,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(625,53,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(626,53,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(627,53,2,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(628,53,2,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(629,53,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(630,53,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(631,53,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(632,53,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(633,53,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(634,53,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(635,53,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(636,53,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(637,54,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(638,54,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(639,54,2,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(640,54,2,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(641,54,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(642,54,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(643,54,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(644,54,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(645,54,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(646,54,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(647,54,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(648,54,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(649,55,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(650,55,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(651,55,2,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(652,55,2,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(653,55,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(654,55,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(655,55,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(656,55,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(657,55,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(658,55,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(659,55,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(660,55,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(661,56,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(662,56,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(663,56,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(664,56,2,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(665,56,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(666,56,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(667,56,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(668,56,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(669,56,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(670,56,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(671,56,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(672,56,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(673,57,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(674,57,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(675,57,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(676,57,2,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(677,57,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(678,57,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(679,57,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(680,57,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(681,57,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(682,57,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(683,57,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(684,57,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(685,58,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(686,58,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(687,58,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(688,58,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(689,58,3,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(690,58,3,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(691,58,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(692,58,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(693,58,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(694,58,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(695,58,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(696,58,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(697,59,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(698,59,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(699,59,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(700,59,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(701,59,3,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(702,59,3,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(703,59,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(704,59,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(705,59,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(706,59,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(707,59,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(708,59,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(709,60,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(710,60,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(711,60,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(712,60,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(713,60,3,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(714,60,3,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(715,60,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(716,60,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(717,60,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(718,60,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(719,60,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(720,60,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(721,61,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(722,61,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(723,61,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(724,61,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(725,61,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(726,61,3,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(727,61,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(728,61,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(729,61,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(730,61,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(731,61,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(732,61,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(733,62,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(734,62,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(735,62,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(736,62,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(737,62,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(738,62,3,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(739,62,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(740,62,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(741,62,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(742,62,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(743,62,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(744,62,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(745,63,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(746,63,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(747,63,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(748,63,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(749,63,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(750,63,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(751,63,4,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(752,63,4,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(753,63,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(754,63,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(755,63,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(756,63,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(757,64,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(758,64,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(759,64,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(760,64,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(761,64,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(762,64,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(763,64,4,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(764,64,4,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(765,64,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(766,64,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(767,64,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(768,64,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(769,65,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(770,65,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(771,65,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(772,65,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(773,65,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(774,65,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(775,65,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(776,65,4,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(777,65,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(778,65,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(779,65,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(780,65,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(781,66,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(782,66,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(783,66,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(784,66,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(785,66,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(786,66,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(787,66,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(788,66,4,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(789,66,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(790,66,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(791,66,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(792,66,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(793,67,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(794,67,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(795,67,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(796,67,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(797,67,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(798,67,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(799,67,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(800,67,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(801,67,5,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(802,67,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(803,67,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(804,67,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(805,68,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(806,68,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(807,68,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(808,68,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(809,68,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(810,68,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(811,68,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(812,68,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(813,68,5,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(814,68,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(815,68,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(816,68,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(817,69,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(818,69,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(819,69,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(820,69,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(821,69,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(822,69,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(823,69,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(824,69,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(825,69,5,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(826,69,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(827,69,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(828,69,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(829,70,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(830,70,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(831,70,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(832,70,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(833,70,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(834,70,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(835,70,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(836,70,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(837,70,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(838,70,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(839,70,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(840,70,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(841,71,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(842,71,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(843,71,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(844,71,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(845,71,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(846,71,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(847,71,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(848,71,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(849,71,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(850,71,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(851,71,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(852,71,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(853,72,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(854,72,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(855,72,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(856,72,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(857,72,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(858,72,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(859,72,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(860,72,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(861,72,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(862,72,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(863,72,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(864,72,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(865,73,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(866,73,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(867,73,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(868,73,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(869,73,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(870,73,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(871,73,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(872,73,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(873,73,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(874,73,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(875,73,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(876,73,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(877,74,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(878,74,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(879,74,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(880,74,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(881,74,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(882,74,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(883,74,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(884,74,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(885,74,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(886,74,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(887,74,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(888,74,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(889,75,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(890,75,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(891,75,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(892,75,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(893,75,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(894,75,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(895,75,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(896,75,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(897,75,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(898,75,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(899,75,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(900,75,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(901,76,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(902,76,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(903,76,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(904,76,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(905,76,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(906,76,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(907,76,4,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(908,76,4,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(909,76,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(910,76,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(911,76,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(912,76,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(913,77,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(914,77,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(915,77,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(916,77,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(917,77,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(918,77,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(919,77,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(920,77,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(921,77,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(922,77,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(923,77,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(924,77,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(925,78,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(926,78,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(927,78,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(928,78,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(929,78,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(930,78,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(931,78,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(932,78,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(933,78,5,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(934,78,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(935,78,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(936,78,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(937,79,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(938,79,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(939,79,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(940,79,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(941,79,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(942,79,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(943,79,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(944,79,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(945,79,5,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(946,79,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(947,79,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(948,79,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(949,80,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(950,80,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(951,80,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(952,80,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(953,80,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(954,80,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(955,80,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(956,80,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(957,80,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(958,80,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(959,80,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(960,80,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(961,81,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(962,81,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(963,81,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(964,81,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(965,81,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(966,81,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(967,81,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(968,81,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(969,81,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(970,81,5,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(971,81,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(972,81,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(973,82,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(974,82,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(975,82,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(976,82,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(977,82,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(978,82,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(979,82,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(980,82,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(981,82,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(982,82,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(983,82,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(984,82,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(985,83,1,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(986,83,1,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(987,83,2,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(988,83,2,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(989,83,3,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(990,83,3,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(991,83,4,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(992,83,4,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(993,83,5,1,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(994,83,5,2,0,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(995,83,6,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(996,83,6,2,1,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `pembiasaan_materi_periode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembiasaan_nilai`
--

DROP TABLE IF EXISTS `pembiasaan_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembiasaan_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `materi_id` bigint unsigned NOT NULL,
  `kelas` tinyint unsigned NOT NULL,
  `semester` tinyint unsigned NOT NULL,
  `tahun_pelajaran` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembiasaan_nilai_siswa_id_materi_id_kelas_semester_unique` (`siswa_id`,`materi_id`,`kelas`,`semester`),
  KEY `pembiasaan_nilai_materi_id_foreign` (`materi_id`),
  KEY `pembiasaan_nilai_siswa_id_materi_id_index` (`siswa_id`,`materi_id`),
  CONSTRAINT `pembiasaan_nilai_materi_id_foreign` FOREIGN KEY (`materi_id`) REFERENCES `pembiasaan_materi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembiasaan_nilai_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembiasaan_nilai`
--

LOCK TABLES `pembiasaan_nilai` WRITE;
/*!40000 ALTER TABLE `pembiasaan_nilai` DISABLE KEYS */;
INSERT INTO `pembiasaan_nilai` VALUES (1,1,1,1,1,'2026/2027',90,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,2,1,1,'2026/2027',70,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,3,1,1,'2026/2027',77,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,4,1,1,'2026/2027',76,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,5,1,1,'2026/2027',76,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,18,1,1,'2026/2027',93,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,1,38,1,1,'2026/2027',88,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,1,39,1,1,'2026/2027',92,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,1,40,1,1,'2026/2027',77,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(10,1,41,1,1,'2026/2027',79,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(11,1,42,1,1,'2026/2027',79,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(12,1,43,1,1,'2026/2027',85,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `pembiasaan_nilai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `people` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `religion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Islam',
  `birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `people_nik_unique` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
INSERT INTO `people` VALUES (1,'3508120503850001','Drs. H. Ahmad Fauzi, M.Pd.','L','Islam','Banyuwangi','1985-03-12','81234500001','kepala@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-28 18:42:58'),(2,'3508131002870002','Dra. Siti Nurhayati','P','Islam','Jember','1987-02-10','081234500002','kurikulum@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(3,'3508141501900003','Bapak Umar Hakim, S.Pd.','L','Islam','Bondowoso','1990-01-15','081234500003','guru.umar@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(4,'3508152107960004','Ratna Dewi, S.E.','P','Islam','Situbondo','1996-07-21','081234500004','bendahara@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(5,'3508160504990005','Sari Indah Puspitasari, A.Md.','P','Islam','Probolinggo','1999-04-05','081234500005','tu@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(6,'3508171205930006','Imam Syafii, S.Pd.','L','Islam','Lumajang','1993-05-12','081234500006','guru.imam@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(7,'3508180107900007','Nurul Aini, S.Pd.','P','Islam','Pasuruan','1990-07-01','081234500007','guru.bk@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(8,'3508193008980008','Hasan Basri, S.Kom.','L','Islam','Banyuwangi','1998-08-30','081234500008','pustaka@madrasah.sch.id',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(9,'0544753655300023','ERNA, S.Ag.','P','Islam','Babai','1975-12-12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(10,'6939752654300012','ESTI MUNIARTINI, A.Ma, S.Pd.','P','Islam','Sruweng','1974-07-07',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(11,'2740746648300032','SRI HARYATI, S.Pd.','P','Islam','Pekalongan','1968-04-08',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(12,'2953755657200012','ANWARI ANAS, A.Ma, S.Pd.I.','L','Islam','Negara','1977-06-21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(13,'0337756658200063','IBRAHIM, S.Pd.I, M.Pd.','L','Islam','Kuala Kapuas','1978-05-10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(14,'8838757658300022','MELY ASTUTI, S.Pd.','P','Islam','Pontianak','1979-05-06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(15,'6444746648300012','SAIDAH, S.Ag.','P','Islam','Kab Banjar','1968-01-12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(16,'2044763663200003','MAHMUDAH, M.Pd.','P','Islam','Anjir Serapat','1985-07-12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(17,'1944749651300092','SUWARNI, S.Pd','P','Islam','Barabai','1971-06-12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(18,'3757761662300032','NIDA RAHMAWATI, S.Pd.','P','Islam','Palangka Raya','1983-04-25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(19,'4154747650300013','SITI ISTIKHAROH','P','Islam','Madiun','1969-08-22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(20,'5538748652200002','ABDUL SANI, S.Ag.','L','Islam','Pandamaan','1970-02-06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(21,'3936743643200002','H. MUHAMMAD MAHLAN','L','Islam','Lok Gabang','1965-05-25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(22,'1559763663200003','RAHMAN, S.Pd.I, M.Pd.','L','Islam','Baru','1985-12-27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(23,'6746760661300162','RUSHANA SULISTIANI, S.Pd.','P','Islam','Kuala Kapuas','1982-04-14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(24,'6135764665200013','AHMAD BAIHAKI, S.Pd.I.','L','Islam','Palangka Raya','1986-08-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(25,'6201000000000017','MELIA AYU LINDASARI, S.Pd.I.','P','Islam','Palangka Raya','1988-07-13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(26,'6201000000000018','WIWIN ELPIRA, S.Pd.','P','Islam','Baru','1989-11-04',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(27,'5938767668200012','SALAMAT, S.Pd.I.','L','Islam','Sei Lunuk','1989-06-06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(28,'6201000000000020','FELIA DESINTIAWATI, S.Pd.','P','Islam','Banjarmasin','1999-12-15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(29,'6201000000000021','RASIDAH, S.Pd.','P','Islam','Tamban Raya','1998-12-19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(30,'6201000000000022','NURUL AZIZAH, S.Pd.','P','Islam','Palangka Raya','1999-06-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(31,'6201000000000023','AHMADI MAULANA, S.Pd.','L','Islam','Palangka Raya','1998-05-06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(32,'6201000000000024','ALWAFA AMRULLAH, S.Pd.','L','Islam','Palangka Raya','2000-05-14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(33,'6201000000000025','MUHAMMAD NOOR RAHMAN, S.Pd.','L','Islam','Barabai','1971-06-12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(34,'6201000000000026','FITRIANI, S.Pd','P','Islam','Palangka Raya','2002-12-01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(35,'6201000000000027','AKHMAD HULAIFI, S.Pd','L','Islam','Samba Katung','2000-06-23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(36,'6201000000000028','MUHAMMAD ARSYAD, A.Ma','L','Islam','Palangka Raya','1989-11-04',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(37,'6201000000000029','M. DEDE MAULANA, S.Pd','L','Islam','Palangka Raya','1996-07-16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(38,'6201000000000030','YULIA AMELIA','P','Islam','Palangka Raya','2004-06-19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44'),(39,'6201000000000031','ZAHRATUNNISA, S.Pd','P','Islam','Palangka Raya','1999-07-16',NULL,'zahra@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:44','2026-08-28 18:49:59'),(40,'351000000000','Aisyah Nur Azizah','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(41,'351000000001','Bilal Ramadhan','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(42,'351000000002','Cinta Lestari Putri','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(43,'351000000003','Dimas Prasetyo','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(44,'351000000004','Eka Salsabila','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(45,'351000000005','Fathir Rahman','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(46,'351000000006','Ghina Aulia Rahma','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(47,'351000000007','Hafizh Akbar','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(48,'351000000008','Intan Permatasari','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(49,'351000000009','Jaka Setiawan','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(50,'351000000010','Khalifah Nur Hidayah','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(51,'351000000011','Lukman Hakim','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(52,'351000000012','Maya Anggraini','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(53,'351000000013','Naufal Rizky','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(54,'351000000014','Nabila Putri','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(55,'351000000015','Raihan Al-Farisi','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(56,'351000000016','Salsabila Zahra','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(57,'351000000017','Taufik Hidayat','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(58,'351000000018','Umi Kulsum','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(59,'351000000019','Vino Pratama','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(60,'351000000020','Wulan Dari','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(61,'3510000000213425','Yusuf Maulana','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 22:23:24'),(62,'351000000022','Zahra Aulia','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(63,'351000000023','Bintang Ramadhan','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(64,'351000000024','Citra Ayu','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(65,'352000000001','Aisyah Nur Azizah','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(66,'352000000002','Bilal Ramadhan','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(67,'352000000003','Cinta Lestari Putri','P','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(68,'6172010101010011','MUHAMMAD FARHAN RAMADHAN','L','Islam','PALANGKA RAYA','2018-03-14','081234567890','farhan@gmail.com','Jl. Rajawali No. 45 RT.002 RW.004','Kalimantan Tengah','Palangka Raya','Jekan Raya','MENTENG','002','004','73112','0513-4567890','2026-08-27 18:31:28','2026-08-27 18:31:28'),(69,'6172010101010001','SISWA TES','L','Islam',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 21:59:01','2026-08-27 21:59:01'),(70,'6172010101010003','SITI NURHALIZA','P','Islam','Palangka Raya','2018-08-22',NULL,NULL,'Jl. Pahlawan No. 5','Kalimantan Tengah','Palangka Raya','Bukit Batu','Bukit Batu','002','003','73121',NULL,'2026-08-27 22:20:51','2026-08-27 22:20:51'),(71,'1234567890123456','testing guru','L','Katolik','palangka','1992-08-29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-28 18:48:34','2026-08-28 18:48:34');
/*!40000 ALTER TABLE `people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'KEPALA_MADRASAH','Kepala Madrasah','2026-08-27 01:09:44','2026-08-27 01:09:44'),(2,'WAKAMAD_KURIKULUM','Wakamad Kurikulum','2026-08-27 01:09:44','2026-08-27 01:09:44'),(3,'WAKAMAD_KESISWAAN','Wakamad Kesiswaan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(4,'WAKAMAD_SARPRAS','Wakamad Sarpras','2026-08-27 01:09:44','2026-08-27 01:09:44'),(5,'WAKAMAD_HUMAS','Wakamad Humas','2026-08-27 01:09:44','2026-08-27 01:09:44'),(6,'GURU_MAPEL','Guru Mata Pelajaran','2026-08-27 01:09:44','2026-08-27 01:09:44'),(7,'GURU_BK','Guru BK','2026-08-27 01:09:44','2026-08-27 01:09:44'),(8,'BENDAHARA','Bendahara','2026-08-27 01:09:44','2026-08-27 01:09:44'),(9,'TATA_USAHA','Tata Usaha','2026-08-27 01:09:44','2026-08-27 01:09:44'),(10,'PUSTAKAWAN','Petugas Perpustakaan','2026-08-27 01:09:44','2026-08-27 01:09:44'),(11,'LABORAN','Petugas Laboratorium','2026-08-27 01:09:44','2026-08-27 01:09:44'),(12,'SATPAM','Satpam / Jaga Malam','2026-08-27 01:09:44','2026-08-27 01:09:44'),(13,'OPERATOR','Operator Madrasah','2026-08-27 01:09:44','2026-08-27 01:09:44');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppdb_interests`
--

DROP TABLE IF EXISTS `ppdb_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppdb_interests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppdb_interests_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppdb_interests`
--

LOCK TABLES `ppdb_interests` WRITE;
/*!40000 ALTER TABLE `ppdb_interests` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppdb_interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppdb_registrations`
--

DROP TABLE IF EXISTS `ppdb_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppdb_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','submitted','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `rejection_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `religion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Islam',
  `birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `previous_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hobby` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ambition` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `child_order` tinyint unsigned DEFAULT NULL,
  `sibling_count` tinyint unsigned DEFAULT NULL,
  `ever_tk` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `ever_paud` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `entry_date` date DEFAULT NULL,
  `scanned_kk` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_kk_wali` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_akta` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_ijazah` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_photo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imm_hepb` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `imm_polio` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `imm_bcg` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `imm_campak` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `imm_dpt` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `imm_covid` enum('PERNAH','TIDAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TIDAK',
  `dis_deaf` tinyint(1) NOT NULL DEFAULT '0',
  `dis_blind` tinyint(1) NOT NULL DEFAULT '0',
  `dis_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `dis_intellectual` tinyint(1) NOT NULL DEFAULT '0',
  `dis_behavioral` tinyint(1) NOT NULL DEFAULT '0',
  `dis_slow_learner` tinyint(1) NOT NULL DEFAULT '0',
  `dis_communication` tinyint(1) NOT NULL DEFAULT '0',
  `dis_gifted` tinyint(1) NOT NULL DEFAULT '0',
  `residence_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'Kalimantan Tengah',
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'Palangka Raya',
  `district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commute_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_head_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_birth_date` date DEFAULT NULL,
  `father_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_birth_date` date DEFAULT NULL,
  `mother_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_birth_place` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_birth_date` date DEFAULT NULL,
  `guardian_education` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_job` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_income` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kks` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_pkh` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_ownership` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'Kalimantan Tengah',
  `parent_city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'Palangka Raya',
  `parent_district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_nsm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_npsn` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rombel` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis_nism` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis_last6` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppdb_registrations_registration_no_unique` (`registration_no`),
  KEY `ppdb_registrations_academic_year_id_foreign` (`academic_year_id`),
  KEY `ppdb_registrations_student_id_foreign` (`student_id`),
  CONSTRAINT `ppdb_registrations_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ppdb_registrations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppdb_registrations`
--

LOCK TABLES `ppdb_registrations` WRITE;
/*!40000 ALTER TABLE `ppdb_registrations` DISABLE KEYS */;
INSERT INTO `ppdb_registrations` VALUES (1,'PPDB-2026-001','submitted',NULL,NULL,1,NULL,'127.0.0.1','2026-08-27 01:09:46','2026-08-27 01:09:46','AHMAD RIZKY PRATAMA','6172010101010001',NULL,'L','Islam','Palangka Raya','2018-05-15',NULL,'Olah Raga','PNS',1,2,'PERNAH','TIDAK',NULL,'https://drive.google.com/file/d/demo-kk',NULL,'https://drive.google.com/file/d/demo-akta',NULL,NULL,'PERNAH','PERNAH','PERNAH','PERNAH','PERNAH','TIDAK',0,0,0,0,0,0,0,0,'Tinggal dgn Ortu/Wali','Jl. Merdeka No. 10','Kalimantan Tengah','Palangka Raya','Pahandut','Pahandut','001','001','73111','<5km','Sepeda Motor','10-19 menit',NULL,NULL,NULL,'6172010101010000','BUDI PRATAMA','BUDI PRATAMA','Masih Hidup',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'SITI RAHMAWATI','Masih Hidup','6172010101010002',NULL,'1990-03-20','7','05','Rp2jt-3jt',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Milik Sendiri','Jl. Merdeka No. 10','Kalimantan Tengah','Palangka Raya','Pahandut','Pahandut','001','001','73111','TK Harapan Bunda',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'PPDB-2026-002','accepted',NULL,NULL,1,31,'127.0.0.1','2026-08-27 01:09:46','2026-08-27 22:20:51','SITI NURHALIZA','6172010101010003',NULL,'P','Islam','Palangka Raya','2018-08-22',NULL,'Olah Raga','PNS',1,2,'PERNAH','TIDAK',NULL,'https://drive.google.com/file/d/demo-kk',NULL,'https://drive.google.com/file/d/demo-akta',NULL,NULL,'PERNAH','PERNAH','PERNAH','PERNAH','PERNAH','TIDAK',0,0,0,0,0,0,0,0,'Tinggal dgn Ortu/Wali','Jl. Pahlawan No. 5','Kalimantan Tengah','Palangka Raya','Bukit Batu','Bukit Batu','002','003','73121','<5km','Sepeda Motor','10-19 menit',NULL,NULL,NULL,'6172010101010005','HASANUDIN','HASANUDIN','Masih Hidup',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'NURUL Hidayah','Masih Hidup','6172010101010004',NULL,'1991-07-10','3','06','Rp1jt-2jt',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Milik Sendiri','Jl. Pahlawan No. 5','Kalimantan Tengah','Palangka Raya','Bukit Batu','Bukit Batu','002','003','73121','TK Miftahul Jannah',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'PPDB-2026-003','accepted',NULL,NULL,1,29,'127.0.0.1','2026-08-27 01:09:46','2026-08-27 18:31:28','MUHAMMAD FARHAN RAMADHAN','6172010101010011','0012345678','L','Islam','PALANGKA RAYA','2018-03-14','TK Islam Al-Falah','Membaca','Guru/Dosen',2,3,'PERNAH','PERNAH','2026-07-13','https://drive.google.com/file/d/kk-farhan','https://drive.google.com/file/d/kk-wali-farhan','https://drive.google.com/file/d/akta-farhan','https://drive.google.com/file/d/ijazah-farhan','https://drive.google.com/file/d/foto-farhan','PERNAH','PERNAH','PERNAH','PERNAH','PERNAH','TIDAK',0,0,0,0,0,0,0,0,'Tinggal dgn Ortu/Wali','Jl. Rajawali No. 45 RT.002 RW.004','Kalimantan Tengah','Palangka Raya','Jekan Raya','MENTENG','002','004','73112','5-10km','Antar Jemput Sekolah','20-29 menit','0513-4567890','081234567890','farhan@gmail.com','6172010101010012','ABDUL RAHMAN','ABDUL RAHMAN','Masih Hidup','6172010101010013','BANJARMASIN','1982-05-20','7','03','Rp3jt – 5jt','081255556666','NURHAYATI','Masih Hidup','6172010101010014','PALANGKA RAYA','1985-09-12','3','07','Rp1jt – 2jt','081277778888','H. MOHAMMAD YUSUF','6172010101010015','BANJARMASIN','1965-11-02','2','12','Rp2jt – 3jt','081399991111','6172010101010016','PKH-2026-001','KIP-2026-0001','Milik Sendiri','Jl. Rajawali No. 45 RT.002 RW.004','Kalimantan Tengah','Palangka Raya','Jekan Raya','MENTENG','002','004','73112','TK Islam Al-Falah','111262710006','00112233','Jl. Pendidikan No. 10, Palangka Raya',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ppdb_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_archives`
--

DROP TABLE IF EXISTS `ppi_exam_archives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_archives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_siswa` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rata_p1` decimal(5,2) DEFAULT NULL,
  `rata_p2` decimal(5,2) DEFAULT NULL,
  `rata_p3` decimal(5,2) DEFAULT NULL,
  `rata_hafalan` decimal(5,2) DEFAULT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `predikat` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_lulus` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rombel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_archives_exam_period_id_foreign` (`exam_period_id`),
  CONSTRAINT `ppi_exam_archives_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_archives`
--

LOCK TABLES `ppi_exam_archives` WRITE;
/*!40000 ALTER TABLE `ppi_exam_archives` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppi_exam_archives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_aspect_categories`
--

DROP TABLE IF EXISTS `ppi_exam_aspect_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_aspect_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penguji_urutan` tinyint unsigned NOT NULL,
  `urutan` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_aspect_categories_exam_period_id_urutan_index` (`exam_period_id`,`urutan`),
  CONSTRAINT `ppi_exam_aspect_categories_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_aspect_categories`
--

LOCK TABLES `ppi_exam_aspect_categories` WRITE;
/*!40000 ALTER TABLE `ppi_exam_aspect_categories` DISABLE KEYS */;
INSERT INTO `ppi_exam_aspect_categories` VALUES (1,1,'1','Wudhu',1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'2','Praktik Shalat',1,2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,'3','Tilawatil Qur\'an',2,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,'4','Shalat Jenazah',2,4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,'5','Hafalan Hadis',2,5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,'6','Do\'a-Do\'a Harian',3,6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,1,'7','Pengetahuan Agama',3,7,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_aspect_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_aspects`
--

DROP TABLE IF EXISTS `ppi_exam_aspects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_aspects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `aspect_category_id` bigint unsigned NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_aspects_aspect_category_id_foreign` (`aspect_category_id`),
  CONSTRAINT `ppi_exam_aspects_aspect_category_id_foreign` FOREIGN KEY (`aspect_category_id`) REFERENCES `ppi_exam_aspect_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_aspects`
--

LOCK TABLES `ppi_exam_aspects` WRITE;
/*!40000 ALTER TABLE `ppi_exam_aspects` DISABLE KEYS */;
INSERT INTO `ppi_exam_aspects` VALUES (1,1,'1','Niat Wudhu',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'2','Praktik Wudhu',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,'3','Do\'a Sesudah Wudhu',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,'4','Niat Tayamum',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,2,'1','Lafaz azan',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,2,'2','Lafaz iqamah',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,2,'3','Do\'a sesudah azan',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,2,'4','Do\'a sesudah iqamah',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,2,'5','Niat shalat subuh',5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(10,2,'6','Niat shalat zuhur',6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(11,2,'7','Niat shalat asar',7,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(12,2,'8','Niat shalat magrib',8,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(13,2,'9','Niat shalat isya',9,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(14,2,'10','Do\'a iftitah',10,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(15,2,'11','Al-fatihah',11,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(16,2,'12','Bacaan ruku\'',12,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(17,2,'13','Bacaan i\'tidal',13,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(18,2,'14','Do\'a Qunut',14,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(19,2,'15','Bacaan sujud',15,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(20,2,'16','Bacaan duduk antara 2 sujud',16,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(21,2,'17','Bacaan tahiyat awal',17,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(22,2,'18','Bacaan tahiyat akhir',18,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(23,2,'19','Salam',19,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(24,2,'20','Do\'a sebelum salam',20,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(25,2,'21','Wirid / Dzikir Pendek bada shalat',21,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(26,2,'22','Do\'a selamat',22,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(27,3,'1','Makhorijul huruf',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(28,3,'2','Hukum Bacaan',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(29,3,'3','Kelancaran',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(30,4,'1','Niat salat Jenazah untuk laki-laki Dewasa',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(31,4,'2','Niat salat Jenazah untuk Perempuan Dewasa',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(32,4,'3','Niat Salat Jenazah untuk Anak laki-laki',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(33,4,'4','Niat Salat Jenazah Untuk Anak Perempuan',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(34,4,'5','Bacaan Takbir Pertama',5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(35,4,'6','Bacaan Takbir Kedua',6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(36,4,'7','Bacaan Takbir Ketiga',7,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(37,4,'8','Bacaan Takbir Keempat',8,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(38,5,'1','Hadis tentang amal Shaleh',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(39,5,'2','Hadis tentang keutamaan memberi',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(40,6,'1','Do\'a Senandung Al-Qur\'an',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(41,6,'2','Do\'a mau Belajar',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(42,6,'3','Do\'a Mau makan',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(43,6,'4','Do\'a sesudah makan',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(44,6,'5','Do\'a masuk WC',5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(45,6,'6','Do\'a keluar WC',6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(46,6,'7','Do\'a Masuk rumah',7,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(47,6,'8','Do\'a Keluar rumah',8,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(48,6,'9','Do\'a Mau tidur',9,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(49,6,'10','Do\'a bangun tidur',10,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(50,6,'11','Do\'a masuk mesjid',11,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(51,6,'12','Do\'a Keluar mesjid',12,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(52,6,'13','Do\'a untuk Kedua Orang Tua',13,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(53,6,'14','Niat Puasa Ramadhan',14,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(54,6,'15','Do\'a Berbuka Puasa',15,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(55,6,'16','Do\'a bercermin',16,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(56,6,'17','Do\'a Naik Kendaraan Darat',17,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(57,6,'18','Do\'a Naik Kendaraan Air',18,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(58,7,'1','Rukun islam',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(59,7,'2','Rukun iman',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(60,7,'3','Rukun wudhu',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(61,7,'4','Rukun shalat',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(62,7,'5','Shalat Sunnah',5,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_aspects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_examiners`
--

DROP TABLE IF EXISTS `ppi_exam_examiners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_examiners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `exam_room_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `urutan` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppi_exam_examiners_exam_period_id_employee_id_unique` (`exam_period_id`,`employee_id`),
  UNIQUE KEY `ppi_exam_examiners_exam_room_id_urutan_unique` (`exam_room_id`,`urutan`),
  KEY `ppi_exam_examiners_employee_id_foreign` (`employee_id`),
  CONSTRAINT `ppi_exam_examiners_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_examiners_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ppi_exam_examiners_exam_room_id_foreign` FOREIGN KEY (`exam_room_id`) REFERENCES `ppi_exam_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_examiners`
--

LOCK TABLES `ppi_exam_examiners` WRITE;
/*!40000 ALTER TABLE `ppi_exam_examiners` DISABLE KEYS */;
INSERT INTO `ppi_exam_examiners` VALUES (1,1,1,3,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,1,6,2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,1,7,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,2,12,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,2,13,2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,2,14,3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_examiners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_groups`
--

DROP TABLE IF EXISTS `ppi_exam_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembimbing_employee_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_groups_exam_period_id_foreign` (`exam_period_id`),
  KEY `ppi_exam_groups_pembimbing_employee_id_foreign` (`pembimbing_employee_id`),
  CONSTRAINT `ppi_exam_groups_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ppi_exam_groups_pembimbing_employee_id_foreign` FOREIGN KEY (`pembimbing_employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_groups`
--

LOCK TABLES `ppi_exam_groups` WRITE;
/*!40000 ALTER TABLE `ppi_exam_groups` DISABLE KEYS */;
INSERT INTO `ppi_exam_groups` VALUES (1,1,'Grup A',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'Grup B',6,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_hafalan_materi`
--

DROP TABLE IF EXISTS `ppi_exam_hafalan_materi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_hafalan_materi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_hafalan_materi_exam_period_id_foreign` (`exam_period_id`),
  CONSTRAINT `ppi_exam_hafalan_materi_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_hafalan_materi`
--

LOCK TABLES `ppi_exam_hafalan_materi` WRITE;
/*!40000 ALTER TABLE `ppi_exam_hafalan_materi` DISABLE KEYS */;
INSERT INTO `ppi_exam_hafalan_materi` VALUES (1,1,'Yaasin',1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'Al-Waqi\'ah',2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,'Ad-Dhuha',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,'Al-Insyirah',4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,'At-Tiin',5,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,'Al-`Alaq',6,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,1,'Al-Qadar',7,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,1,'Al-Bayyinah',8,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,1,'Al-Zalzalah',9,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(10,1,'Al-`Adiyat',10,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(11,1,'Al-Qari\'ah',11,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(12,1,'At-Takasur',12,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(13,1,'Al-`Ashr',13,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(14,1,'Al-Humazah',14,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(15,1,'Al-Fiil',15,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(16,1,'Al-Quraisy',16,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(17,1,'Al-Ma`un',17,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(18,1,'Al-Kausar',18,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(19,1,'Al-Kafirun',19,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(20,1,'An-Nasr',20,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(21,1,'Al-Lahab',21,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(22,1,'Al-Ikhlas',22,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(23,1,'Al-Falaq',23,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(24,1,'An-Naas',24,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_hafalan_materi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_hafalan_scores`
--

DROP TABLE IF EXISTS `ppi_exam_hafalan_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_hafalan_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` bigint unsigned NOT NULL,
  `hafalan_materi_id` bigint unsigned NOT NULL,
  `nilai` tinyint unsigned NOT NULL,
  `tanggal_setor` date DEFAULT NULL,
  `dinilai_oleh_employee_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppi_exam_hafalan_scores_participant_id_hafalan_materi_id_unique` (`participant_id`,`hafalan_materi_id`),
  KEY `ppi_exam_hafalan_scores_hafalan_materi_id_foreign` (`hafalan_materi_id`),
  KEY `ppi_exam_hafalan_scores_dinilai_oleh_employee_id_foreign` (`dinilai_oleh_employee_id`),
  CONSTRAINT `ppi_exam_hafalan_scores_dinilai_oleh_employee_id_foreign` FOREIGN KEY (`dinilai_oleh_employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_hafalan_scores_hafalan_materi_id_foreign` FOREIGN KEY (`hafalan_materi_id`) REFERENCES `ppi_exam_hafalan_materi` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_hafalan_scores_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `ppi_exam_participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_hafalan_scores`
--

LOCK TABLES `ppi_exam_hafalan_scores` WRITE;
/*!40000 ALTER TABLE `ppi_exam_hafalan_scores` DISABLE KEYS */;
INSERT INTO `ppi_exam_hafalan_scores` VALUES (1,1,1,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,2,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,3,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,4,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,5,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,6,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,1,7,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,1,8,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,1,9,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(10,1,10,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(11,1,11,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(12,1,12,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(13,1,13,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(14,1,14,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(15,1,15,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(16,1,16,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(17,1,17,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(18,1,18,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(19,1,19,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(20,1,20,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(21,1,21,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(22,1,22,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(23,1,23,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(24,1,24,88,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(25,2,1,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(26,2,2,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(27,2,3,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(28,2,4,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(29,2,5,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(30,2,6,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(31,2,7,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(32,2,8,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(33,2,9,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(34,2,10,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(35,2,11,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(36,2,12,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(37,2,13,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(38,2,14,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(39,2,15,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(40,2,16,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(41,2,17,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(42,2,18,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(43,2,19,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(44,2,20,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(45,2,21,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(46,2,22,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(47,2,23,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(48,2,24,80,'2026-08-27',3,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_hafalan_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_participants`
--

DROP TABLE IF EXISTS `ppi_exam_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `exam_room_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned DEFAULT NULL,
  `class_group_id` bigint unsigned NOT NULL,
  `no_urut` smallint unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `jumlah_p1` smallint unsigned DEFAULT NULL,
  `rata_p1` decimal(5,2) DEFAULT NULL,
  `jumlah_p2` smallint unsigned DEFAULT NULL,
  `rata_p2` decimal(5,2) DEFAULT NULL,
  `jumlah_p3` smallint unsigned DEFAULT NULL,
  `rata_p3` decimal(5,2) DEFAULT NULL,
  `jumlah_ujian_lisan` smallint unsigned DEFAULT NULL,
  `rata_ujian_lisan` decimal(5,2) DEFAULT NULL,
  `rata_hafalan` decimal(5,2) DEFAULT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `predicate_scale_id` bigint unsigned DEFAULT NULL,
  `status_lulus` tinyint(1) DEFAULT NULL,
  `rank_total` smallint unsigned DEFAULT NULL,
  `rank_lokal` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppi_exam_participants_exam_period_id_student_id_unique` (`exam_period_id`,`student_id`),
  KEY `ppi_exam_participants_student_id_foreign` (`student_id`),
  KEY `ppi_exam_participants_exam_room_id_foreign` (`exam_room_id`),
  KEY `ppi_exam_participants_group_id_foreign` (`group_id`),
  KEY `ppi_exam_participants_class_group_id_foreign` (`class_group_id`),
  KEY `ppi_exam_participants_predicate_scale_id_foreign` (`predicate_scale_id`),
  CONSTRAINT `ppi_exam_participants_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_participants_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ppi_exam_participants_exam_room_id_foreign` FOREIGN KEY (`exam_room_id`) REFERENCES `ppi_exam_rooms` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_participants_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `ppi_exam_groups` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_participants_predicate_scale_id_foreign` FOREIGN KEY (`predicate_scale_id`) REFERENCES `ppi_exam_predicate_scales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ppi_exam_participants_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_participants`
--

LOCK TABLES `ppi_exam_participants` WRITE;
/*!40000 ALTER TABLE `ppi_exam_participants` DISABLE KEYS */;
INSERT INTO `ppi_exam_participants` VALUES (1,1,27,1,1,13,1,'aktif',2080,80.00,1105,85.00,2070,90.00,5255,85.00,88.00,85.75,2,1,1,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,28,1,1,13,2,'aktif',1820,70.00,975,75.00,1794,78.00,4589,74.33,80.00,75.75,3,1,2,2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,25,2,2,12,3,'aktif',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,26,2,2,12,4,'aktif',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_periods`
--

DROP TABLE IF EXISTS `ppi_exam_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_setoran_mulai` date DEFAULT NULL,
  `tanggal_setoran_selesai` date DEFAULT NULL,
  `tanggal_ujian` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `config_locked_at` timestamp NULL DEFAULT NULL,
  `bobot_p1` tinyint unsigned NOT NULL DEFAULT '25',
  `bobot_p2` tinyint unsigned NOT NULL DEFAULT '25',
  `bobot_p3` tinyint unsigned NOT NULL DEFAULT '25',
  `bobot_hafalan` tinyint unsigned NOT NULL DEFAULT '25',
  `teks_mc` text COLLATE utf8mb4_unicode_ci,
  `teks_ba` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_periods_academic_year_id_status_index` (`academic_year_id`,`status`),
  CONSTRAINT `ppi_exam_periods_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_periods`
--

LOCK TABLES `ppi_exam_periods` WRITE;
/*!40000 ALTER TABLE `ppi_exam_periods` DISABLE KEYS */;
INSERT INTO `ppi_exam_periods` VALUES (1,1,'Ujian PPI Kelas VI TP 2026/2027','2026-08-17','2026-08-24','2026-09-03','berlangsung','2026-08-27 01:09:46',25,25,25,25,'TEKS PEMBAWA ACARA\n\n1. PEMBUKAAN\n\nAssalamu\'alaikum Warahmatullahi wabarakatuh.\n\nAlhamdulillah Asholatu wassalamu ala sayidina maulana muhammadin wa \'ala alihi\nshohbihi ajma\'in, amma ba\'du\n\nSidang Asesmen PPI atas nama {{NAMA_SISWA}} Bin/Binti {{NAMA_AYAH}}\n\nSecara resmi di buka dengan ucapan Basmalah\n\nKami persilahkan kepada Penguji Pertama untuk mengawali pertanyaan\n\n2. PEMBACAAN BERITA ACARA (terlampir)\n\n3. PENUTUP\n\nSebelum kita akhiri sidang Asesmen Praktek Pengamalan Ibadah\n\nkami mohon kepada penguji {{NAMA_PENGUJI_PENUTUP}} untuk memberikan pesan/nasehat.\n\nKepada bapak/ibu {{NAMA_PENGUJI_PENUTUP}} dipersilahkan.\n\nDemikian sidang asesmen PPI pada hari ini\n\napabila kami segenap penguji ada khilaf dalam ucapan dan perbuatan mohon di maafkan\n\nwallahul muwafiq ila aqwamit thoriq, Wassalamu\'alaikum Wr.Wb.','BERITA ACARA\nASESMEN PRAKTEK PENGAMALAN IBADAH (PPI)\nSISWA KELAS VI\n{{NAMA_MADRASAH}}\nTAHUN PELAJARAN {{TAHUN_AJARAN}}\n\nDengan mengucap Bismillahirrahmanirrahim\n\nPada hari {{HARI}} tanggal {{TANGGAL}}\npukul {{JAM}} WIB. telah terlaksana Asesmen Praktek Pengamalan Ibadah (PPI)\natas nama {{NAMA_SISWA}}\nbin/binti {{NAMA_AYAH}}\n\ndengan Tim Penguji yang terdiri dari :\n\nPenguji I  : {{NAMA_PENGUJI_1}}\nPenguji II : {{NAMA_PENGUJI_2}}\nPenguji III: {{NAMA_PENGUJI_3}}\n\nDari hasil beberapa pertanyaan dari tim penguji ananda\nmemperoleh sejumlah nilai sebagai berikut :\n\nPenguji I   nilai rata-rata yang diperoleh  {{RATA_P1}}\nPenguji II  nilai rata-rata yang diperoleh  {{RATA_P2}}\nPenguji III nilai rata-rata yang diperoleh  {{RATA_P3}}\n\nDari ketiga penguji ditambah nilai hafalan surah-surah Yasin, Waqi\'ah\ndan surah-surah pendek sebelumnya (dihitung sesuai bobot masing-masing).\nMaka ananda memperoleh nilai rata-rata akhir adalah {{NILAI_AKHIR}}\ndan di nyatakan {{STATUS_LULUS}}\npada sidang Asesmen PPI ini dengan predikat {{PREDIKAT}}\ndan dengan deskripsi {{DESKRIPSI}}\n\nDi tetapkan di {{KOTA}} pada tanggal {{TANGGAL}}\n\n{{TANDA_TANGAN}}','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_predicate_scales`
--

DROP TABLE IF EXISTS `ppi_exam_predicate_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_predicate_scales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `predikat` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_min` tinyint unsigned NOT NULL,
  `nilai_max` tinyint unsigned NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_tidak_lulus` tinyint(1) NOT NULL DEFAULT '0',
  `urutan` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppi_exam_predicate_scales_exam_period_id_urutan_unique` (`exam_period_id`,`urutan`),
  CONSTRAINT `ppi_exam_predicate_scales_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_predicate_scales`
--

LOCK TABLES `ppi_exam_predicate_scales` WRITE;
/*!40000 ALTER TABLE `ppi_exam_predicate_scales` DISABLE KEYS */;
INSERT INTO `ppi_exam_predicate_scales` VALUES (1,1,'A+',90,100,'Sangat Baik — penguasaan materi luar biasa',0,1,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'A',80,89,'Sangat Baik',0,2,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,'B',70,79,'Baik',0,3,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,'C',60,69,'Cukup',0,4,'2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,'D',0,59,'Belum Tuntas',1,5,'2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_predicate_scales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_rooms`
--

DROP TABLE IF EXISTS `ppi_exam_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_period_id` bigint unsigned NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ppi_exam_rooms_exam_period_id_foreign` (`exam_period_id`),
  CONSTRAINT `ppi_exam_rooms_exam_period_id_foreign` FOREIGN KEY (`exam_period_id`) REFERENCES `ppi_exam_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_rooms`
--

LOCK TABLES `ppi_exam_rooms` WRITE;
/*!40000 ALTER TABLE `ppi_exam_rooms` DISABLE KEYS */;
INSERT INTO `ppi_exam_rooms` VALUES (1,1,'Ruang 1','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'Ruang 2','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppi_exam_scores`
--

DROP TABLE IF EXISTS `ppi_exam_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppi_exam_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` bigint unsigned NOT NULL,
  `aspect_id` bigint unsigned NOT NULL,
  `nilai` tinyint unsigned NOT NULL,
  `examiner_employee_id` bigint unsigned NOT NULL,
  `input_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ppi_exam_scores_participant_id_aspect_id_unique` (`participant_id`,`aspect_id`),
  KEY `ppi_exam_scores_aspect_id_foreign` (`aspect_id`),
  KEY `ppi_exam_scores_examiner_employee_id_foreign` (`examiner_employee_id`),
  CONSTRAINT `ppi_exam_scores_aspect_id_foreign` FOREIGN KEY (`aspect_id`) REFERENCES `ppi_exam_aspects` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_scores_examiner_employee_id_foreign` FOREIGN KEY (`examiner_employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ppi_exam_scores_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `ppi_exam_participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppi_exam_scores`
--

LOCK TABLES `ppi_exam_scores` WRITE;
/*!40000 ALTER TABLE `ppi_exam_scores` DISABLE KEYS */;
INSERT INTO `ppi_exam_scores` VALUES (1,1,1,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,2,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,1,3,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(4,1,4,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(5,1,5,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(6,1,6,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(7,1,7,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(8,1,8,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(9,1,9,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(10,1,10,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(11,1,11,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(12,1,12,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(13,1,13,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(14,1,14,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(15,1,15,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(16,1,16,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(17,1,17,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(18,1,18,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(19,1,19,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(20,1,20,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(21,1,21,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(22,1,22,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(23,1,23,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(24,1,24,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(25,1,25,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(26,1,26,80,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(27,1,27,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(28,1,28,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(29,1,29,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(30,1,30,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(31,1,31,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(32,1,32,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(33,1,33,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(34,1,34,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(35,1,35,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(36,1,36,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(37,1,37,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(38,1,38,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(39,1,39,85,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(40,1,40,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(41,1,41,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(42,1,42,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(43,1,43,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(44,1,44,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(45,1,45,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(46,1,46,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(47,1,47,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(48,1,48,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(49,1,49,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(50,1,50,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(51,1,51,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(52,1,52,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(53,1,53,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(54,1,54,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(55,1,55,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(56,1,56,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(57,1,57,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(58,1,58,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(59,1,59,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(60,1,60,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(61,1,61,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(62,1,62,90,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(63,2,1,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(64,2,2,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(65,2,3,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(66,2,4,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(67,2,5,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(68,2,6,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(69,2,7,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(70,2,8,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(71,2,9,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(72,2,10,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(73,2,11,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(74,2,12,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(75,2,13,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(76,2,14,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(77,2,15,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(78,2,16,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(79,2,17,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(80,2,18,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(81,2,19,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(82,2,20,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(83,2,21,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(84,2,22,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(85,2,23,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(86,2,24,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(87,2,25,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(88,2,26,70,3,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(89,2,27,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(90,2,28,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(91,2,29,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(92,2,30,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(93,2,31,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(94,2,32,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(95,2,33,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(96,2,34,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(97,2,35,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(98,2,36,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(99,2,37,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(100,2,38,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(101,2,39,75,6,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(102,2,40,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(103,2,41,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(104,2,42,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(105,2,43,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(106,2,44,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(107,2,45,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(108,2,46,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(109,2,47,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(110,2,48,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(111,2,49,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(112,2,50,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(113,2,51,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(114,2,52,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(115,2,53,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(116,2,54,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(117,2,55,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(118,2,56,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(119,2,57,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(120,2,58,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(121,2,59,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(122,2,60,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(123,2,61,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46'),(124,2,62,78,7,'2026-08-27 01:09:46','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `ppi_exam_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_items`
--

DROP TABLE IF EXISTS `report_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint unsigned NOT NULL,
  `subject_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_group_id` bigint unsigned DEFAULT NULL,
  `class_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teacher_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` tinyint unsigned DEFAULT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_item_unique` (`report_id`,`subject_code`),
  KEY `report_items_class_group_id_foreign` (`class_group_id`),
  KEY `report_items_report_id_index` (`report_id`),
  CONSTRAINT `report_items_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `report_items_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_items`
--

LOCK TABLES `report_items` WRITE;
/*!40000 ALTER TABLE `report_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ganjil',
  `snapshot` json NOT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `version` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_unique` (`student_id`,`academic_year_id`,`semester`),
  KEY `reports_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `reports_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ruangan',
  `building` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `floor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` smallint unsigned NOT NULL DEFAULT '0',
  `employee_id` bigint unsigned DEFAULT NULL,
  `condition` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_code_unique` (`code`),
  KEY `rooms_employee_id_foreign` (`employee_id`),
  KEY `rooms_created_by_foreign` (`created_by`),
  CONSTRAINT `rooms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rooms_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'R-001','Ruang Guru','ruangan','Gedung Utama','Lantai 1',30,9,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(2,'R-002','Kantor Kepala Madrasah','ruangan','Gedung Utama','Lantai 1',5,9,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(3,'R-003','Kantor Tata Usaha','ruangan','Gedung Utama','Lantai 1',8,39,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(4,'R-004','Ruang BK','ruangan','Gedung Utama','Lantai 1',4,NULL,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(5,'R-005','Ruang Perpustakaan','ruangan','Gedung Utama','Lantai 1',20,NULL,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(6,'R-006','Ruang Kelas I','ruangan','Gedung A','Lantai 1',36,NULL,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(7,'R-007','Ruang Kelas II','ruangan','Gedung A','Lantai 1',36,NULL,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(8,'R-008','Aula','ruangan','Gedung Utama','Lantai 2',200,36,'rusak_ringan',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(9,'R-009','Lab IPA','laboratorium','Gedung B','Lantai 1',30,12,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(10,'R-010','Lab Komputer','laboratorium','Gedung B','Lantai 2',25,37,'baik',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25'),(11,'R-011','Lab Bahasa','laboratorium','Gedung B','Lantai 1',30,NULL,'dalam_perbaikan',NULL,NULL,'2026-08-27 01:47:25','2026-08-27 01:47:25');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_cells`
--

DROP TABLE IF EXISTS `schedule_cells`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_cells` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_model_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `class_group_id` bigint unsigned NOT NULL,
  `day` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_no` tinyint unsigned NOT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_cell_unique` (`schedule_model_id`,`academic_year_id`,`class_group_id`,`day`,`period_no`),
  KEY `schedule_cells_academic_year_id_foreign` (`academic_year_id`),
  KEY `schedule_cells_class_group_id_foreign` (`class_group_id`),
  KEY `schedule_cells_teacher_id_foreign` (`teacher_id`),
  KEY `schedule_cells_subject_id_foreign` (`subject_id`),
  CONSTRAINT `schedule_cells_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_cells_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_cells_schedule_model_id_foreign` FOREIGN KEY (`schedule_model_id`) REFERENCES `schedule_models` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_cells_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_cells_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_cells`
--

LOCK TABLES `schedule_cells` WRITE;
/*!40000 ALTER TABLE `schedule_cells` DISABLE KEYS */;
INSERT INTO `schedule_cells` VALUES (1,1,1,1,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,1,2,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,1,1,'senin',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,1,1,1,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,1,1,2,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,1,1,1,'selasa',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,1,1,1,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,1,1,2,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,1,1,1,'rabu',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,2,1,4,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,2,1,5,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,2,1,4,'senin',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,2,1,4,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(14,2,1,5,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(15,2,1,4,'selasa',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(16,2,1,4,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(17,2,1,5,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(18,2,1,4,'rabu',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(19,3,1,10,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(20,3,1,11,'senin',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(21,3,1,10,'senin',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(22,3,1,10,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(23,3,1,11,'selasa',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(24,3,1,10,'selasa',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(25,3,1,10,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(26,3,1,11,'rabu',1,1,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(27,3,1,10,'rabu',2,4,2,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `schedule_cells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_model_grade_levels`
--

DROP TABLE IF EXISTS `schedule_model_grade_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_model_grade_levels` (
  `schedule_model_id` bigint unsigned NOT NULL,
  `grade_level` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`schedule_model_id`,`grade_level`),
  CONSTRAINT `schedule_model_grade_levels_schedule_model_id_foreign` FOREIGN KEY (`schedule_model_id`) REFERENCES `schedule_models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_model_grade_levels`
--

LOCK TABLES `schedule_model_grade_levels` WRITE;
/*!40000 ALTER TABLE `schedule_model_grade_levels` DISABLE KEYS */;
INSERT INTO `schedule_model_grade_levels` VALUES (1,'I'),(2,'II'),(2,'III'),(2,'IV'),(3,'V'),(3,'VI');
/*!40000 ALTER TABLE `schedule_model_grade_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_model_slots`
--

DROP TABLE IF EXISTS `schedule_model_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_model_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_model_id` bigint unsigned NOT NULL,
  `period_no` tinyint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_break` tinyint(1) NOT NULL DEFAULT '0',
  `label` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_model_slots_schedule_model_id_period_no_unique` (`schedule_model_id`,`period_no`),
  CONSTRAINT `schedule_model_slots_schedule_model_id_foreign` FOREIGN KEY (`schedule_model_id`) REFERENCES `schedule_models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_model_slots`
--

LOCK TABLES `schedule_model_slots` WRITE;
/*!40000 ALTER TABLE `schedule_model_slots` DISABLE KEYS */;
INSERT INTO `schedule_model_slots` VALUES (1,1,1,'07:00:00','07:35:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,2,'07:35:00','08:10:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,3,'08:10:00','08:45:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,1,4,'08:45:00','09:20:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,1,5,'09:20:00','09:55:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,1,6,'09:55:00','10:30:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,2,1,'07:00:00','07:35:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,2,2,'07:35:00','08:10:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,2,3,'08:10:00','08:45:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,2,4,'08:45:00','09:20:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,2,5,'09:20:00','09:55:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,2,6,'09:55:00','10:30:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,2,7,'10:30:00','11:05:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(14,3,1,'07:00:00','07:40:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(15,3,2,'07:40:00','08:20:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(16,3,3,'08:20:00','09:00:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(17,3,4,'09:00:00','09:40:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(18,3,5,'09:40:00','10:20:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(19,3,6,'10:20:00','11:00:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(20,3,7,'11:00:00','11:40:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(21,3,8,'11:40:00','12:20:00',0,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `schedule_model_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_models`
--

DROP TABLE IF EXISTS `schedule_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_models` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `max_hours_per_day` tinyint unsigned NOT NULL DEFAULT '6',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_models_academic_year_id_foreign` (`academic_year_id`),
  KEY `schedule_models_created_by_foreign` (`created_by`),
  CONSTRAINT `schedule_models_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_models_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_models`
--

LOCK TABLES `schedule_models` WRITE;
/*!40000 ALTER TABLE `schedule_models` DISABLE KEYS */;
INSERT INTO `schedule_models` VALUES (1,1,'Kurikulum Kelas I','07:00:00',6,1,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,'Kurikulum Kelas II–IV','07:00:00',7,1,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,'Kurikulum Kelas V–VI','07:00:00',8,1,NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `schedule_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ganjil',
  `score` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `score_unique` (`student_enrollment_id`,`subject_id`,`semester`),
  KEY `scores_subject_id_foreign` (`subject_id`),
  KEY `scores_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `scores_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scores_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scores_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scores`
--

LOCK TABLES `scores` WRITE;
/*!40000 ALTER TABLE `scores` DISABLE KEYS */;
INSERT INTO `scores` VALUES (1,1,1,1,'ganjil',88,'2026-08-27 01:09:43','2026-08-27 01:09:43'),(2,2,1,1,'ganjil',76,'2026-08-27 01:09:43','2026-08-27 01:09:43');
/*!40000 ALTER TABLE `scores` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('9vhMaV9cvgPbcSXa2MIGdCLzthq5Svq9l2dUHwTz',3,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJQVDRPdzhTMEJ0S1pYdGZSZ3l4OEhmeGZ6dVRDU0Q0NE1RM2xtcHVQIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2ZvbmRhc2lcL3BlbmdndW5hIiwicm91dGUiOiJwZW5nZ3VuYS5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MywiYWt0aXZhc2lfY3JlZGVudGlhbHMiOltdLCJha3RpdmFzaV9mYWlsZWQiOlt7Im5hbWEiOiJCaWxhbCBSYW1hZGhhbiIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiQ2ludGEgTGVzdGFyaSBQdXRyaSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiQWlzeWFoIE51ciBBeml6YWgiLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6IkJpbGFsIFJhbWFkaGFuIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJDaW50YSBMZXN0YXJpIFB1dHJpIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJEaW1hcyBQcmFzZXR5byIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiRWthIFNhbHNhYmlsYSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiRmF0aGlyIFJhaG1hbiIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiR2hpbmEgQXVsaWEgUmFobWEiLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6IkhhZml6aCBBa2JhciIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiSW50YW4gUGVybWF0YXNhcmkiLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6Ikpha2EgU2V0aWF3YW4iLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6IktoYWxpZmFoIE51ciBIaWRheWFoIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJMdWttYW4gSGFraW0iLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6Ik1heWEgQW5nZ3JhaW5pIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJOYXVmYWwgUml6a3kiLCJhbGFzYW4iOiJUYW5nZ2FsIGxhaGlyIHNpc3dhIGtvc29uZyAtIHBhc3N3b3JkIGRlZmF1bHQgdGlkYWsgZGFwYXQgZGlidWF0LiJ9LHsibmFtYSI6Ik5hYmlsYSBQdXRyaSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiUmFpaGFuIEFsLUZhcmlzaSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiU2Fsc2FiaWxhIFphaHJhIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJUYXVmaWsgSGlkYXlhdCIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiVW1pIEt1bHN1bSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiVmlubyBQcmF0YW1hIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJXdWxhbiBEYXJpIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJZdXN1ZiBNYXVsYW5hIiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifSx7Im5hbWEiOiJaYWhyYSBBdWxpYSIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiQmludGFuZyBSYW1hZGhhbiIsImFsYXNhbiI6IlRhbmdnYWwgbGFoaXIgc2lzd2Ega29zb25nIC0gcGFzc3dvcmQgZGVmYXVsdCB0aWRhayBkYXBhdCBkaWJ1YXQuIn0seyJuYW1hIjoiQ2l0cmEgQXl1IiwiYWxhc2FuIjoiVGFuZ2dhbCBsYWhpciBzaXN3YSBrb3NvbmcgLSBwYXNzd29yZCBkZWZhdWx0IHRpZGFrIGRhcGF0IGRpYnVhdC4ifV19',1787970346);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'madrasah_name','MTs Al-Ikhlas Mulia','2026-08-27 01:09:42','2026-08-27 01:09:42'),(2,'madrasah_nsm','11111111','2026-08-27 01:09:42','2026-08-27 01:09:42'),(3,'madrasah_npsn','12345678','2026-08-27 01:09:42','2026-08-27 01:09:42'),(4,'madrasah_jenjang','MTs','2026-08-27 01:09:42','2026-08-27 01:09:42'),(5,'madrasah_status','swasta','2026-08-27 01:09:42','2026-08-27 01:09:42'),(6,'madrasah_tahun_berdiri','2000','2026-08-27 01:09:42','2026-08-27 01:09:42'),(7,'madrasah_jalan','Jl. Pendidikan No. 123','2026-08-27 01:09:42','2026-08-27 01:09:42'),(8,'madrasah_desa','Kel. Ilmu','2026-08-27 01:09:42','2026-08-27 01:09:42'),(9,'madrasah_kecamatan','Kec. Semangat','2026-08-27 01:09:42','2026-08-27 01:09:42'),(10,'madrasah_kabupaten','Kota Cerdas','2026-08-27 01:09:42','2026-08-27 01:09:42'),(11,'madrasah_provinsi','Jawa Barat','2026-08-27 01:09:42','2026-08-27 01:09:42'),(12,'madrasah_kode_pos','40123','2026-08-27 01:09:42','2026-08-27 01:09:42'),(13,'madrasah_latitude','-6.9175','2026-08-27 01:09:42','2026-08-27 01:09:42'),(14,'madrasah_longitude','107.6191','2026-08-27 01:09:42','2026-08-27 01:09:42'),(15,'madrasah_phone','(022) 1234567','2026-08-27 01:09:42','2026-08-27 01:09:42'),(16,'madrasah_email','info@alikhlas.sch.id','2026-08-27 01:09:42','2026-08-27 01:09:42'),(17,'madrasah_website','https://alikhlas.sch.id','2026-08-27 01:09:42','2026-08-27 01:09:42'),(18,'madrasah_sk_pendirian','001/SK/2000','2026-08-27 01:09:42','2026-08-27 01:09:42'),(19,'madrasah_tgl_sk_pendirian','2000-01-15','2026-08-27 01:09:42','2026-08-27 01:09:42'),(20,'madrasah_sk_operasional','002/SK/2000','2026-08-27 01:09:42','2026-08-27 01:09:42'),(21,'madrasah_akreditasi','terakreditasi','2026-08-27 01:09:42','2026-08-27 01:09:42'),(22,'madrasah_nilai_akreditasi','B','2026-08-27 01:09:42','2026-08-27 01:09:42'),(23,'madrasah_naungan','Kementerian Agama','2026-08-27 01:09:42','2026-08-27 01:09:42'),(24,'madrasah_logo','','2026-08-27 01:09:42','2026-08-27 01:09:42'),(25,'ppdb_status','closed','2026-08-27 01:09:42','2026-08-27 01:09:42'),(26,'ppdb_tanggal_buka','','2026-08-27 01:09:42','2026-08-27 01:09:42'),(27,'ppdb_tanggal_tutup','','2026-08-27 01:09:42','2026-08-27 01:09:42'),(28,'ppdb_tanggal_pengumuman','','2026-08-27 01:09:42','2026-08-27 01:09:42'),(29,'ppdb_tanggal_daftar_ulang','','2026-08-27 01:09:42','2026-08-27 01:09:42'),(30,'ppdb_usia_min','6','2026-08-27 01:09:42','2026-08-27 01:09:42'),(31,'ppdb_usia_ket','per 1 Juli tahun berjalan','2026-08-27 01:09:42','2026-08-27 01:09:42'),(32,'ppdb_dokumen','Scan Kartu Keluarga (KK)\nScan Akta Kelahiran\nKIA / Kartu Identitas Orang Tua\nIjazah / Rapor TK (jika ada)\nPas Foto (jika diminta)','2026-08-27 01:09:42','2026-08-28 03:51:20'),(33,'ppdb_kuota','28','2026-08-27 01:09:42','2026-08-27 01:09:42'),(34,'ppdb_jalur','Reguler','2026-08-27 01:09:42','2026-08-27 01:09:42'),(35,'ppdb_biaya','Gratis (tidak dipungut biaya pendaftaran)','2026-08-27 01:09:42','2026-08-27 01:09:42'),(36,'ppdb_kontak_wa','','2026-08-27 01:09:42','2026-08-28 03:51:20'),(37,'ppdb_kontak_telepon','','2026-08-27 01:09:42','2026-08-28 03:51:20'),(38,'ppdb_jam_layanan','Senin–Jumat, 08.00–14.00 WIB','2026-08-27 01:09:42','2026-08-27 01:09:42'),(39,'ppdb_faq','[]','2026-08-27 01:09:42','2026-08-28 03:51:20'),(40,'mutasi_status','open','2026-08-28 03:51:20','2026-08-28 04:04:18'),(41,'mutasi_tanggal_buka','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(42,'mutasi_tanggal_tutup','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(43,'mutasi_tanggal_pengumuman','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(44,'mutasi_tanggal_daftar_ulang','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(45,'mutasi_syarat','Surat Rekomendasi Madrasah dari madrasah asal untuk siswa bersangkutan\r\nSalinan Rapor / Transkrip Nilai\r\nKartu Keluarga (KK)\r\nAkta Kelahiran\r\nPas Foto (jika diminta)','2026-08-28 03:51:20','2026-08-28 04:04:18'),(46,'mutasi_kuota','10','2026-08-28 03:51:20','2026-08-28 03:51:20'),(47,'mutasi_kelas_tersedia','Kelas I-A\r\nKelas II-A\r\nKelas III-A','2026-08-28 03:51:20','2026-08-28 04:04:18'),(48,'mutasi_biaya','Gratis (tidak dipungut biaya pendaftaran)','2026-08-28 03:51:20','2026-08-28 03:51:20'),(49,'mutasi_kontak_wa','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(50,'mutasi_kontak_telepon','','2026-08-28 03:51:20','2026-08-28 03:51:20'),(51,'mutasi_jam_layanan','Senin–Jumat, 08.00–14.00 WIB','2026-08-28 03:51:20','2026-08-28 03:51:20'),(52,'mutasi_faq','[]','2026-08-28 03:51:20','2026-08-28 04:04:18');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_enrollments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `class_group_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollment_unique` (`academic_year_id`,`class_group_id`,`student_id`),
  KEY `student_enrollments_class_group_id_foreign` (`class_group_id`),
  KEY `student_enrollments_student_id_foreign` (`student_id`),
  CONSTRAINT `student_enrollments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_enrollments_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_enrollments`
--

LOCK TABLES `student_enrollments` WRITE;
/*!40000 ALTER TABLE `student_enrollments` DISABLE KEYS */;
INSERT INTO `student_enrollments` VALUES (1,1,1,1,'aktif','2026-08-27 01:09:43','2026-08-27 01:09:43'),(2,1,1,2,'aktif','2026-08-27 01:09:43','2026-08-27 01:09:43'),(3,1,1,3,'aktif','2026-08-27 01:09:43','2026-08-27 01:09:43'),(4,1,1,4,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,1,1,5,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,1,1,6,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,1,1,7,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,1,2,8,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,1,2,9,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,1,2,10,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(11,1,4,11,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(12,1,4,12,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(13,1,4,13,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(14,1,5,14,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(15,1,5,15,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(16,1,6,16,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(17,1,6,17,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(18,1,7,18,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(19,1,8,19,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(20,1,8,20,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(21,1,9,21,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(22,1,10,22,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(23,1,10,23,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(24,1,11,24,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(25,1,12,25,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(26,1,12,26,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(27,1,13,27,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45'),(28,1,13,28,'aktif','2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `student_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_mutations`
--

DROP TABLE IF EXISTS `student_mutations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_mutations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `tanggal_mutasi` date NOT NULL,
  `sekolah_tujuan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan_nsm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tujuan_npsn` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_pindah` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `no_surat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_mutations_academic_year_id_foreign` (`academic_year_id`),
  KEY `student_mutations_created_by_foreign` (`created_by`),
  KEY `student_mutations_student_id_academic_year_id_index` (`student_id`,`academic_year_id`),
  CONSTRAINT `student_mutations_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_mutations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_mutations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_mutations`
--

LOCK TABLES `student_mutations` WRITE;
/*!40000 ALTER TABLE `student_mutations` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_mutations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nisn` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_nsm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_npsn` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `hobby` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ambition` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `child_order` tinyint unsigned DEFAULT NULL,
  `sibling_count` tinyint unsigned DEFAULT NULL,
  `ever_tk` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ever_paud` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residence_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commute_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kk_head_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kks` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_pkh` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_kip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_ownership` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_province` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_district` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_village` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rt` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_rw` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_postal_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `imm_hepb` tinyint(1) NOT NULL DEFAULT '0',
  `imm_polio` tinyint(1) NOT NULL DEFAULT '0',
  `imm_bcg` tinyint(1) NOT NULL DEFAULT '0',
  `imm_campak` tinyint(1) NOT NULL DEFAULT '0',
  `imm_dpt` tinyint(1) NOT NULL DEFAULT '0',
  `imm_covid` tinyint(1) NOT NULL DEFAULT '0',
  `dis_deaf` tinyint(1) NOT NULL DEFAULT '0',
  `dis_blind` tinyint(1) NOT NULL DEFAULT '0',
  `dis_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `dis_intellectual` tinyint(1) NOT NULL DEFAULT '0',
  `dis_behavioral` tinyint(1) NOT NULL DEFAULT '0',
  `dis_slow_learner` tinyint(1) NOT NULL DEFAULT '0',
  `dis_communication` tinyint(1) NOT NULL DEFAULT '0',
  `dis_gifted` tinyint(1) NOT NULL DEFAULT '0',
  `documents` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_nis_unique` (`nis`),
  KEY `students_person_id_foreign` (`person_id`),
  CONSTRAINT `students_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,65,'240101',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aisyah Nur Azizah','P','2026-08-27 01:09:43','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(2,66,'240102',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bilal Ramadhan','L','2026-08-27 01:09:43','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(3,67,'240103',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cinta Lestari Putri','P','2026-08-27 01:09:43','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(4,40,'241000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aisyah Nur Azizah','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(5,41,'241001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bilal Ramadhan','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(6,42,'241002',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cinta Lestari Putri','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(7,43,'241003',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dimas Prasetyo','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(8,44,'241004',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Eka Salsabila','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(9,45,'241005',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Fathir Rahman','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(10,46,'241006',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ghina Aulia Rahma','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(11,47,'241007',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hafizh Akbar','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(12,48,'241008',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Intan Permatasari','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(13,49,'241009',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jaka Setiawan','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(14,50,'241010',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Khalifah Nur Hidayah','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(15,51,'241011',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lukman Hakim','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(16,52,'241012',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Maya Anggraini','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(17,53,'241013',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Naufal Rizky','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(18,54,'241014',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nabila Putri','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(19,55,'241015',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Raihan Al-Farisi','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(20,56,'241016',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Salsabila Zahra','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(21,57,'241017',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Taufik Hidayat','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(22,58,'241018',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Umi Kulsum','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(23,59,'241019',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Vino Pratama','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(24,60,'241020',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Wulan Dari','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(25,61,'241021',NULL,'tk al furqon',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Yusuf Maulana','L','2026-08-27 01:09:45','2026-08-27 22:23:24',0,0,0,0,0,0,0,0,0,0,0,0,0,0,'[]'),(26,62,'241022',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Zahra Aulia','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(27,63,'241023',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bintang Ramadhan','L','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(28,64,'241024',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Citra Ayu','P','2026-08-27 01:09:45','2026-08-27 01:09:45',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(29,68,NULL,'0012345678','TK Islam Al-Falah','TK Islam Al-Falah','111262710006','00112233','Jl. Pendidikan No. 10, Palangka Raya','2026-07-13','Membaca','Guru/Dosen',2,3,'PERNAH','PERNAH','Tinggal dgn Ortu/Wali','5-10km','Antar Jemput Sekolah','20-29 menit','6172010101010012','ABDUL RAHMAN','6172010101010016','PKH-2026-001','KIP-2026-0001','Milik Sendiri','Jl. Rajawali No. 45 RT.002 RW.004','Kalimantan Tengah','Palangka Raya','Jekan Raya','MENTENG','002','004','73112','MUHAMMAD FARHAN RAMADHAN','L','2026-08-27 18:31:28','2026-08-27 21:57:39',1,1,1,1,1,0,0,0,0,0,0,0,0,0,'{\"kk\": \"https://drive.google.com/file/d/kk-farhan\", \"akta\": \"https://drive.google.com/file/d/akta-farhan\", \"photo\": \"https://drive.google.com/file/d/foto-farhan\", \"ijazah\": \"https://drive.google.com/file/d/ijazah-farhan\", \"kk_wali\": \"https://drive.google.com/file/d/kk-wali-farhan\"}'),(30,69,'251001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'SISWA TES','L','2026-08-27 21:59:01','2026-08-27 21:59:01',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(31,70,NULL,NULL,NULL,'TK Miftahul Jannah',NULL,NULL,NULL,NULL,'Olah Raga','PNS',1,2,'PERNAH','TIDAK','Tinggal dgn Ortu/Wali','<5km','Sepeda Motor','10-19 menit','6172010101010005','HASANUDIN',NULL,NULL,NULL,'Milik Sendiri','Jl. Pahlawan No. 5','Kalimantan Tengah','Palangka Raya','Bukit Batu','Bukit Batu','002','003','73121','SITI NURHALIZA','P','2026-08-27 22:20:51','2026-08-27 22:20:51',1,1,1,1,1,0,0,0,0,0,0,0,0,0,'{\"kk\": \"https://drive.google.com/file/d/demo-kk\", \"akta\": \"https://drive.google.com/file/d/demo-akta\"}');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'MAT','Matematika',1,'2026-08-27 01:09:42','2026-08-27 01:09:45'),(2,'BIN','Bahasa Indonesia',2,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,'BING','Bahasa Inggris',3,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,'IPA','Ilmu Pengetahuan Alam',4,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,'IPS','Ilmu Pengetahuan Sosial',5,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,'PAI','Pendidikan Agama Islam',6,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(7,'SBK','Seni Budaya dan Keterampilan',7,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(8,'PJOK','Pendidikan Jasmani, Olahraga, dan Kesehatan',8,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(9,'PKN','Pendidikan Kewarganegaraan',9,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(10,'BTA','Baca Tulis Al-Qur\'an',10,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_assignments`
--

DROP TABLE IF EXISTS `teacher_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `class_group_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_unique` (`academic_year_id`,`class_group_id`,`subject_id`),
  KEY `teacher_assignments_class_group_id_foreign` (`class_group_id`),
  KEY `teacher_assignments_subject_id_foreign` (`subject_id`),
  KEY `teacher_assignments_user_id_foreign` (`user_id`),
  CONSTRAINT `teacher_assignments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_assignments_class_group_id_foreign` FOREIGN KEY (`class_group_id`) REFERENCES `class_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_assignments`
--

LOCK TABLES `teacher_assignments` WRITE;
/*!40000 ALTER TABLE `teacher_assignments` DISABLE KEYS */;
INSERT INTO `teacher_assignments` VALUES (1,1,1,1,1,'2026-08-27 01:09:43','2026-08-27 01:09:43'),(2,1,1,4,1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,1,2,4,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(4,1,2,1,4,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(5,1,2,3,5,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(6,1,4,4,1,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `teacher_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teaching_journals`
--

DROP TABLE IF EXISTS `teaching_journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teaching_journals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `teacher_assignment_id` bigint unsigned NOT NULL,
  `journal_date` date NOT NULL,
  `period_no` tinyint unsigned DEFAULT NULL,
  `materi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan` text COLLATE utf8mb4_unicode_ci,
  `metode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `tindak_lanjut` text COLLATE utf8mb4_unicode_ci,
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terisi',
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teaching_journals_academic_year_id_foreign` (`academic_year_id`),
  KEY `teaching_journals_recorded_by_foreign` (`recorded_by`),
  KEY `teaching_journals_teacher_assignment_id_journal_date_index` (`teacher_assignment_id`,`journal_date`),
  CONSTRAINT `teaching_journals_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teaching_journals_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teaching_journals_teacher_assignment_id_foreign` FOREIGN KEY (`teacher_assignment_id`) REFERENCES `teacher_assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teaching_journals`
--

LOCK TABLES `teaching_journals` WRITE;
/*!40000 ALTER TABLE `teaching_journals` DISABLE KEYS */;
INSERT INTO `teaching_journals` VALUES (1,1,1,'2026-08-27',1,'Membilang dan menulis bilangan 1 sampai 20','Siswa dapat menghitung dan menyebutkan hasilnya dengan benar.','Ceramah interaktif dan permainan kartu bilangan','Sebagian besar siswa sudah lancar, 3 siswa perlu bimbingan tambahan.','Latihan soal di rumah halaman 12.',NULL,'terisi',1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,1,'2026-08-26',2,'Membandingkan dua kumpulan benda (lebih banyak / lebih sedikit)','Siswa dapat menghitung dan menyebutkan hasilnya dengan benar.','Ceramah interaktif dan permainan kartu bilangan','Sebagian besar siswa sudah lancar, 3 siswa perlu bimbingan tambahan.','Latihan soal di rumah halaman 12.',NULL,'terisi',1,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(3,1,1,'2026-08-25',1,'Penjumlahan dua bilangan tanpa menyimpan','Siswa dapat menghitung dan menyebutkan hasilnya dengan benar.','Ceramah interaktif dan permainan kartu bilangan',NULL,NULL,NULL,'draft',1,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `teaching_journals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tuition_overrides`
--

DROP TABLE IF EXISTS `tuition_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tuition_overrides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `nominal` int unsigned NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tuition_override_unique` (`student_enrollment_id`,`academic_year_id`),
  KEY `tuition_overrides_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `tuition_overrides_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tuition_overrides_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tuition_overrides`
--

LOCK TABLES `tuition_overrides` WRITE;
/*!40000 ALTER TABLE `tuition_overrides` DISABLE KEYS */;
/*!40000 ALTER TABLE `tuition_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tuition_payments`
--

DROP TABLE IF EXISTS `tuition_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tuition_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_enrollment_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ganjil',
  `bulan` tinyint unsigned NOT NULL,
  `nominal` int unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `tanggal_bayar` date DEFAULT NULL,
  `metode` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tuition_payment_unique` (`student_enrollment_id`,`academic_year_id`,`bulan`),
  KEY `tuition_payments_recorded_by_foreign` (`recorded_by`),
  KEY `tuition_payments_academic_year_id_student_enrollment_id_index` (`academic_year_id`,`student_enrollment_id`),
  CONSTRAINT `tuition_payments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tuition_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tuition_payments_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tuition_payments`
--

LOCK TABLES `tuition_payments` WRITE;
/*!40000 ALTER TABLE `tuition_payments` DISABLE KEYS */;
INSERT INTO `tuition_payments` VALUES (1,1,1,'ganjil',7,100000,'lunas','2026-08-05','tunai',NULL,8,'2026-08-27 01:09:45','2026-08-27 01:09:45'),(2,1,1,'ganjil',8,100000,'lunas','2026-08-05','tunai',NULL,8,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `tuition_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tuition_settings`
--

DROP TABLE IF EXISTS `tuition_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tuition_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `nominal` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tuition_settings_academic_year_id_unique` (`academic_year_id`),
  CONSTRAINT `tuition_settings_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tuition_settings`
--

LOCK TABLES `tuition_settings` WRITE;
/*!40000 ALTER TABLE `tuition_settings` DISABLE KEYS */;
INSERT INTO `tuition_settings` VALUES (1,1,100000,'2026-08-27 01:09:45','2026-08-27 01:09:45');
/*!40000 ALTER TABLE `tuition_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_id_role_unique` (`user_id`,`role`),
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,3,'kepala_madrasah','2026-08-27 01:09:46','2026-08-27 01:09:46'),(2,1,'wakamad_kurikulum','2026-08-27 01:09:46','2026-08-27 01:09:46'),(3,2,'tata_usaha','2026-08-27 01:09:46','2026-08-27 01:09:46');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'super_admin',
  `student_id` bigint unsigned DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_student_id_unique` (`student_id`),
  CONSTRAINT `users_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Bapak Umar Hakim','guru.umar','guru',NULL,1,1,'guru@madrasah.sch.id',NULL,'$2y$12$wpTjlqc/nQ8yFbZduENWx.1XEaMyCEZ5LgCNUkTXV.WxckEeLSUHS',NULL,'2026-08-27 01:09:43','2026-08-27 01:09:43',NULL),(2,'Ibu Ratna Dewi','ibu.aisy','orang_tua',NULL,1,1,'ortu@madrasah.sch.id',NULL,'$2y$12$4GUDH0mKit1Flem0cZ.g5.rMwvhwkuhk/KM7jrGhHP7Rtz6joWR1G',NULL,'2026-08-27 01:09:43','2026-08-27 01:09:43',NULL),(3,'Admin Madrasah','admin','super_admin',NULL,0,1,'admin@madrasah.sch.id',NULL,'$2y$12$xPsQs/4x58YLkQf7ch29Uu4bPOSJUlW9Mb7SoiqU0Oa8Z1uZicI2u','7628K6BJg3uU72eNkjPFv1jCgx1T0m9M1G5UMOdyGjInnNzUqNk18Ow7lx8t','2026-08-27 01:09:43','2026-08-28 18:21:22',NULL),(4,'Imam Syafii, S.Pd.','guru.imam','guru',NULL,1,1,'guru.imam2@madrasah.sch.id',NULL,'$2y$12$dbKs06tyKzr99GLtoOGQzuN8Kka.dS/GIw.UIDUDuXBGrLm0NysXS',NULL,'2026-08-27 01:09:43','2026-08-27 01:09:43',NULL),(5,'Nurul Aini, S.Pd.','guru.nurul','guru',NULL,1,1,'guru.nurul@madrasah.sch.id',NULL,'$2y$12$t26C2r0datp/4JeVjpXFduhXFQjC8/YL6yz7UUb0oW/kRvKVU21e.',NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44',NULL),(6,'Pustakawan Demo','pustakawan','pustakawan',NULL,1,1,'pustakawan@madrasah.sch.id',NULL,'$2y$12$VvLUsA0wc/SSfFiywkqs6OMstm7Z4IFounrKDmlVnuJ9kDpymEePO',NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44',NULL),(7,'Aisyah Nur Azizah','siswa.aisy','siswa',1,1,1,'siswa.aisy@madrasah.sch.id',NULL,'$2y$12$Ap7cOCIuZfCvIL5l2YeAd.gk9Nj/EZYR73yl35L2mPwq8i3.iDy76',NULL,'2026-08-27 01:09:44','2026-08-27 01:09:44',NULL),(8,'Ibu Fitri Bendahara','bendahara','bendahara',NULL,1,1,'bendahara@madrasah.sch.id',NULL,'$2y$12$hbz5/e7qaN8eT2ij6U9jGOdjQ.JyQKM1WrGvJATOI6.8uJYYuHu3i',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45',NULL),(9,'Humas Madrasah','editor.humas','editor_berita',NULL,1,1,'humas@madrasah.sch.id',NULL,'$2y$12$Hk/YWso7LyvGMqEfm0bCj.VzSojOTJ9CJsl8t/l7.35ijbuPYBIeu',NULL,'2026-08-27 01:09:45','2026-08-27 01:09:45',NULL),(10,'Kepala Madrasah Demo','kepala','kepala_madrasah',NULL,1,1,'kepala@madrasah.sch.id',NULL,'$2y$12$293AY/rmNYIt21be2MhkMug7zVS591aiNSHpxfvIDyjYziuVgkCdm',NULL,'2026-08-27 01:09:46','2026-08-27 01:09:46',NULL),(11,'testing guru','1234567890123456','guru',NULL,1,1,'1234567890123456@akun.madrasah.local',NULL,'$2y$12$3nY/qF2pUs09weVkS7rUHeU7eTIRhB/mJM02WFxHyounrZR3nXf/6',NULL,'2026-08-28 18:48:34','2026-08-28 18:48:34',NULL),(12,'Dra. Siti Nurhayati','198702102011012004','wakamad_kurikulum',NULL,1,1,'kurikulum@madrasah.sch.id',NULL,'$2y$12$1VOi634IwTOd1ovr4m7E6Ohxp2.rTNAtORxPwWrcDS4O8e31Jo0Fe',NULL,'2026-08-28 19:18:35','2026-08-28 19:18:35',NULL),(13,'Sari Indah Puspitasari, A.Md.','3508160504990005','tata_usaha',NULL,1,1,'tu@madrasah.sch.id',NULL,'$2y$12$VsaKB8Hqo5ySX1rrgROcmOJwYItYQ3EN5mzpMXPIJ9qvmDNHVRRkO',NULL,'2026-08-28 19:18:35','2026-08-28 19:18:35',NULL),(14,'Hasan Basri, S.Kom.','3508193008980008','pustakawan',NULL,1,1,'pustaka@madrasah.sch.id',NULL,'$2y$12$dg/5G1zbW33GDxxfmlXudeyTEJGiR34tIKEN3GSVsiXhbqd8if6tW',NULL,'2026-08-28 19:18:35','2026-08-28 19:18:35',NULL),(15,'ERNA, S.Ag.','197512122007012044','kepala_madrasah',NULL,1,1,'197512122007012044@akun.madrasah.local',NULL,'$2y$12$sTzWBmHEzi.HcibZoRT7FuNPgcev2wqdhXrjDSvbVkqbj5y7OhZ8W',NULL,'2026-08-28 19:18:35','2026-08-28 19:18:35',NULL),(16,'ESTI MUNIARTINI, A.Ma, S.Pd.','197406071999032001','guru',NULL,1,1,'197406071999032001@akun.madrasah.local',NULL,'$2y$12$AARt30DJ4zN.wjgEq7plCehHhvlmn6sIbAyrlKJCR4/.6ktm9PTHm',NULL,'2026-08-28 19:18:36','2026-08-28 19:18:36',NULL),(17,'SRI HARYATI, S.Pd.','196804081999032004','guru',NULL,1,1,'196804081999032004@akun.madrasah.local',NULL,'$2y$12$B8hfgI.TK9oz.4OP.l7CDeguoSO7N4O2c44F2ze4Sk4374k2Ilv8O',NULL,'2026-08-28 19:18:36','2026-08-28 19:18:36',NULL),(18,'ANWARI ANAS, A.Ma, S.Pd.I.','197706212007011017','guru',NULL,1,1,'197706212007011017@akun.madrasah.local',NULL,'$2y$12$FnlLmEKvlgaoi5kXRCx9Ye4g0YDUmNr5eWnXZd8tWjo9bhqjKKgZO',NULL,'2026-08-28 19:18:36','2026-08-28 19:18:36',NULL),(19,'IBRAHIM, S.Pd.I, M.Pd.','197810051999031003','guru',NULL,1,1,'197810051999031003@akun.madrasah.local',NULL,'$2y$12$a9zpxoooDBSSDPYptkPl8u4k1jlYeJAxS0xui8T7dlUw0Lx7XqxQK',NULL,'2026-08-28 19:18:36','2026-08-28 19:18:36',NULL),(20,'MELY ASTUTI, S.Pd.','197905062007102008','guru',NULL,1,1,'197905062007102008@akun.madrasah.local',NULL,'$2y$12$v5wbswK1Xa5hP3bDnByC.OoZ4.KcF5ed2SpM170bAa5pHp47nAf7K',NULL,'2026-08-28 19:18:37','2026-08-28 19:18:37',NULL),(21,'SAIDAH, S.Ag.','196801121997032002','guru',NULL,1,1,'196801121997032002@akun.madrasah.local',NULL,'$2y$12$z3HlFVK07EevWpzYQcVvoeLZMlL9WFVYsYiijiqn9tQnpxgvIWmg6',NULL,'2026-08-28 19:18:37','2026-08-28 19:18:37',NULL),(22,'MAHMUDAH, M.Pd.','198507122005012001','guru',NULL,1,1,'198507122005012001@akun.madrasah.local',NULL,'$2y$12$sRGexWJoCgt/w7SOH4ASouV9ruz2Ap77E1wTzNk7EQN0V3EJM.iVa',NULL,'2026-08-28 19:18:37','2026-08-28 19:18:37',NULL),(23,'SUWARNI, S.Pd','197106122007012034','guru',NULL,1,1,'197106122007012034@akun.madrasah.local',NULL,'$2y$12$w16tOA1gaD7wgmLcAsgle.tT35TnxbxtJu4HKsy2hNKrnM7sUuvn6',NULL,'2026-08-28 19:18:37','2026-08-28 19:18:37',NULL),(24,'NIDA RAHMAWATI, S.Pd.','198304252007102001','guru',NULL,1,1,'198304252007102001@akun.madrasah.local',NULL,'$2y$12$VQ1IpGkG3znEzrp3.R6zfuKFv4Bzdh9Kdwn4K/kWH00d2YVLUoYSa',NULL,'2026-08-28 19:18:38','2026-08-28 19:18:38',NULL),(25,'SITI ISTIKHAROH','4154747650300013','guru',NULL,1,1,'4154747650300013@akun.madrasah.local',NULL,'$2y$12$2JAjcO/1AdPrauwK0avWZu7IsG0FN0JvNC3ff1RKmbj2aMDoYNCku',NULL,'2026-08-28 19:18:38','2026-08-28 19:18:38',NULL),(26,'ABDUL SANI, S.Ag.','5538748652200002','guru',NULL,1,1,'5538748652200002@akun.madrasah.local',NULL,'$2y$12$1faKQfz38790y2D5iFbPlO7EJT.iFjzNDaWbBpL1csP9TDXqSBvpG',NULL,'2026-08-28 19:18:38','2026-08-28 19:18:38',NULL),(27,'H. MUHAMMAD MAHLAN','3936743643200002','guru',NULL,1,1,'3936743643200002@akun.madrasah.local',NULL,'$2y$12$gQlks1dD9gQvBkCNTURtdu2ayc6O7biDGg1py3Z2uNqNVeJgh/uky',NULL,'2026-08-28 19:18:38','2026-08-28 19:18:38',NULL),(28,'RAHMAN, S.Pd.I, M.Pd.','1559763663200003','guru',NULL,1,1,'1559763663200003@akun.madrasah.local',NULL,'$2y$12$1zrJRE0OapnVgflkMUphwuBCd58aPbXjfikt18IwSg0iSS7YQKXQO',NULL,'2026-08-28 19:18:39','2026-08-28 19:18:39',NULL),(29,'RUSHANA SULISTIANI, S.Pd.','6746760661300162','guru',NULL,1,1,'6746760661300162@akun.madrasah.local',NULL,'$2y$12$MQA2tLUIcSakDc4sOzBsxevT0nhkHIXXYMcDC7eMNSfj7pZEE/1Kq',NULL,'2026-08-28 19:18:39','2026-08-28 19:18:39',NULL),(30,'AHMAD BAIHAKI, S.Pd.I.','6135764665200013','guru',NULL,1,1,'6135764665200013@akun.madrasah.local',NULL,'$2y$12$fbJ311a7DQREVw6m2B4Au.lWy7Ip/dI1HsdEhIFZbBj.QSbYqC8/e',NULL,'2026-08-28 19:18:39','2026-08-28 19:18:39',NULL),(31,'MELIA AYU LINDASARI, S.Pd.I.','6201000000000017','guru',NULL,1,1,'6201000000000017@akun.madrasah.local',NULL,'$2y$12$rR4e7r36nuDaVMmUGRSXw.RAtFKM9PvfElT1898HKUzZ1c/1pXioC',NULL,'2026-08-28 19:18:39','2026-08-28 19:18:39',NULL),(32,'WIWIN ELPIRA, S.Pd.','6201000000000018','guru',NULL,1,1,'6201000000000018@akun.madrasah.local',NULL,'$2y$12$TBjMdaD1nl1nudxcz7a1fOfR/nl/jIkjMBSGecXO1TWsn2nYhTxMW',NULL,'2026-08-28 19:18:40','2026-08-28 19:18:40',NULL),(33,'SALAMAT, S.Pd.I.','5938767668200012','guru',NULL,1,1,'5938767668200012@akun.madrasah.local',NULL,'$2y$12$hIIDyjo0.3Y8sBy9xDpjAOatbvEgdFd6ZWsouu2.JW4avmHbdw6/G',NULL,'2026-08-28 19:18:40','2026-08-28 19:18:40',NULL),(34,'FELIA DESINTIAWATI, S.Pd.','6201000000000020','guru',NULL,1,1,'6201000000000020@akun.madrasah.local',NULL,'$2y$12$8Sk4QpDVjl1W.AjkNTuEgeeGNhHI.i5LCbf/HVsupj6gjFKCnvTa2',NULL,'2026-08-28 19:18:40','2026-08-28 19:18:40',NULL),(35,'RASIDAH, S.Pd.','6201000000000021','guru',NULL,1,1,'6201000000000021@akun.madrasah.local',NULL,'$2y$12$XvPNzxkV1OcRt01w03ISzuSA9DtvxHbk/IlnQHfh.3.OefQvxQZ/e',NULL,'2026-08-28 19:18:41','2026-08-28 19:18:41',NULL),(36,'NURUL AZIZAH, S.Pd.','6201000000000022','guru',NULL,1,1,'6201000000000022@akun.madrasah.local',NULL,'$2y$12$NYsXfYtqGOU6uMh1Yg00HOFW4ylFe7HGk/9/PbqIVRpeVOZDEE6mu',NULL,'2026-08-28 19:18:41','2026-08-28 19:18:41',NULL),(37,'AHMADI MAULANA, S.Pd.','6201000000000023','guru',NULL,1,1,'6201000000000023@akun.madrasah.local',NULL,'$2y$12$PliuJFrK7DvwgiSkvlH49e7YNaX9TSWnrsnkSGrMW.PQKSuyiyAEm',NULL,'2026-08-28 19:18:41','2026-08-28 19:18:41',NULL),(38,'ALWAFA AMRULLAH, S.Pd.','6201000000000024','guru',NULL,1,1,'6201000000000024@akun.madrasah.local',NULL,'$2y$12$1hRJNfjm6LFixKsCTsElyOoXWN6p8eBHwFXlbFtAtxpR/aO1xlC..',NULL,'2026-08-28 19:18:41','2026-08-28 19:18:41',NULL),(39,'MUHAMMAD NOOR RAHMAN, S.Pd.','6201000000000025','guru',NULL,1,1,'6201000000000025@akun.madrasah.local',NULL,'$2y$12$m2lj4PpwSmeKnZbwWOwbgu59g.M3Tsrp9qAIU5KyIQP1ouY10xYAW',NULL,'2026-08-28 19:18:42','2026-08-28 19:18:42',NULL),(40,'FITRIANI, S.Pd','6201000000000026','guru',NULL,1,1,'6201000000000026@akun.madrasah.local',NULL,'$2y$12$bPcoMbf8TlacWyeVviPF9ehPCfqNiRvC0P25Wp14OopxAWCqyLrZO',NULL,'2026-08-28 19:18:42','2026-08-28 19:18:42',NULL),(41,'AKHMAD HULAIFI, S.Pd','6201000000000027','guru',NULL,1,1,'6201000000000027@akun.madrasah.local',NULL,'$2y$12$XWbVxRiU2Ec/4zUKSv3i3u5xFOtTtN1u7mslWL/Aq1zNOH6FWmZdq',NULL,'2026-08-28 19:18:42','2026-08-28 19:18:42',NULL),(42,'MUHAMMAD ARSYAD, A.Ma','6201000000000028','tata_usaha',NULL,1,1,'6201000000000028@akun.madrasah.local',NULL,'$2y$12$l.FibkJw1oMNLDcJcG8htuIKa4a07L867q3IovybK7yk/G2FBAkya',NULL,'2026-08-28 19:18:42','2026-08-28 19:18:42',NULL),(43,'M. DEDE MAULANA, S.Pd','6201000000000029','tata_usaha',NULL,1,1,'6201000000000029@akun.madrasah.local',NULL,'$2y$12$vk4V5cJedyifiXr..V//Ce.8lUf.X.10JsipAicACRIVZ9tR1aKG.',NULL,'2026-08-28 19:18:43','2026-08-28 19:18:43',NULL),(44,'YULIA AMELIA','6201000000000030','guru',NULL,1,1,'6201000000000030@akun.madrasah.local',NULL,'$2y$12$BsruuxqItlkpYue0/FQsGONepMeGSkrCfePtWflygwf5N8jjU2TCK',NULL,'2026-08-28 19:18:43','2026-08-28 19:18:43',NULL),(45,'ZAHRATUNNISA, S.Pd','6201000000000031','tata_usaha',NULL,1,1,'zahra@gmail.com',NULL,'$2y$12$NMeYe1ABudQ2dFuE9hB4quPtbYdxjOel1JfWCgDQQ27Uc46erIWkK',NULL,'2026-08-28 19:18:43','2026-08-28 19:18:43',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-29 11:50:25
