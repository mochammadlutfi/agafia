-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 07:44 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_tki`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('operational_manager','talent_manager','admin','owner') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `nama`, `username`, `email`, `password`, `level`, `remember_token`, `created_at`, `updated_at`, `email_verified_at`) VALUES
(1, 'Owner', 'owner', 'owner@demo.com', '$2y$12$.XArAST2dxNbKpvuSckNoOkFHtYDGPOhafif7vHO0OM1wvUTKvCqu', 'operational_manager', 'biPoM1AJZ2DKHDFFjNRohYCFLK7lIcwfIt1rXXAuKAmML8GUzAj7R46Wlndt', '2025-06-10 19:22:32', '2025-06-10 19:22:35', '2025-06-10 19:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hasil_interview`
--

CREATE TABLE `hasil_interview` (
  `id` bigint UNSIGNED NOT NULL,
  `jadwal_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `skor_interview` int DEFAULT NULL COMMENT 'Skor interview (1-100)',
  `skor_psikotes` int DEFAULT NULL COMMENT 'Skor test psikologi (1-100)',
  `kemampuan_komunikasi` enum('kurang','cukup','baik','sangat_baik') DEFAULT NULL,
  `kemampuan_teknis` enum('kurang','cukup','baik','sangat_baik') DEFAULT NULL,
  `penilaian_kepribadian` text,
  `rekomendasi` enum('tidak_direkomendasikan','bersyarat','direkomendasikan','sangat_direkomendasikan') NOT NULL,
  `catatan` text,
  `dinilai_oleh` bigint UNSIGNED NOT NULL,
  `tanggal_penilaian` timestamp NOT NULL,
  `disetujui_oleh` bigint UNSIGNED DEFAULT NULL COMMENT 'Manajer Operasional yang approve',
  `tanggal_persetujuan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil_interview`
--

INSERT INTO `hasil_interview` (`id`, `jadwal_id`, `user_id`, `skor_interview`, `skor_psikotes`, `kemampuan_komunikasi`, `kemampuan_teknis`, `penilaian_kepribadian`, `rekomendasi`, `catatan`, `dinilai_oleh`, `tanggal_penilaian`, `disetujui_oleh`, `tanggal_persetujuan`, `created_at`, `updated_at`) VALUES
(1, 3, 51, 12, 21, 'kurang', 'cukup', 'wqe', 'bersyarat', 'qwe', 1, '2025-06-12 08:22:34', NULL, NULL, '2025-06-12 08:22:34', '2025-06-12 08:22:34'),
(2, 3, 51, 12, 21, 'kurang', 'cukup', 'wqe', 'bersyarat', 'qwe', 1, '2025-06-12 08:22:47', NULL, NULL, '2025-06-12 08:22:47', '2025-06-12 08:22:47'),
(5, 10, 52, 20, 20, 'baik', 'baik', 'wqsa', 'bersyarat', 'sas', 1, '2025-06-16 04:03:21', NULL, NULL, '2025-06-16 04:03:21', '2025-06-16 04:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_interview`
--

CREATE TABLE `jadwal_interview` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tanggal` datetime NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `pewawancara_id` bigint UNSIGNED NOT NULL,
  `catatan` text,
  `status` enum('dijadwalkan','selesai','dibatalkan','dijadwal_ulang') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'dijadwalkan',
  `dibuat_oleh` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_interview`
--

INSERT INTO `jadwal_interview` (`id`, `user_id`, `tanggal`, `waktu`, `lokasi`, `pewawancara_id`, `catatan`, `status`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES
(3, 51, '2025-06-11 00:00:00', '10:31:22', 'Kantor', 1, NULL, 'selesai', 1, '2025-06-12 03:31:39', '2025-06-12 08:22:34'),
(10, 52, '2025-06-15 17:00:00', '11:01:44', 'Kantor', 1, 'Tidak ada', 'selesai', 1, '2025-06-16 04:02:26', '2025-06-16 04:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_dokumen`
--

CREATE TABLE `kategori_dokumen` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text,
  `wajib` tinyint(1) DEFAULT '1',
  `jenis_dokumen` enum('medical','keberangkatan','umum') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_dokumen`
--

INSERT INTO `kategori_dokumen` (`id`, `nama_kategori`, `deskripsi`, `wajib`, `jenis_dokumen`, `created_at`, `updated_at`) VALUES
(1, 'KTP', 'Kartu Tanda Penduduk', 1, 'umum', NULL, NULL),
(2, 'Ijazah', 'Ijazah Pendidikan Terakhir', 1, 'umum', NULL, NULL),
(3, 'Kartu Keluarga', 'Kartu Keluarga', 1, 'umum', NULL, NULL),
(4, 'Pas Foto', 'Pas Foto 4x6', 1, 'umum', NULL, NULL),
(5, 'Medical Check Up', 'Hasil Medical Check Up Lengkap', 1, 'medical', NULL, NULL),
(6, 'Rontgen Dada', 'Hasil Rontgen Dada', 1, 'medical', NULL, NULL),
(7, 'Tes Darah', 'Hasil Tes Darah Lengkap', 1, 'medical', NULL, NULL),
(8, 'Visa', 'Visa Kerja', 1, 'keberangkatan', NULL, NULL),
(9, 'Paspor', 'Paspor', 1, 'keberangkatan', NULL, NULL),
(10, 'Tiket Pesawat', 'Tiket Pesawat', 1, 'keberangkatan', NULL, NULL),
(11, 'Asuransi', 'Asuransi Perjalanan', 1, 'keberangkatan', NULL, NULL),
(12, 'Kontrak Kerja', 'Kontrak Kerja dengan Employer', 1, 'keberangkatan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id` bigint UNSIGNED NOT NULL,
  `perusahaan` varchar(191) NOT NULL,
  `posisi` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `skill` varchar(191) NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kuota` tinyint NOT NULL,
  `lokasi` varchar(191) NOT NULL,
  `status` enum('buka','tutup') DEFAULT 'buka',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_checkups`
--

CREATE TABLE `medical_checkups` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(255) NOT NULL,
  `hasil` varchar(50) DEFAULT 'belum dikonfirmasi',
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `catatan` text,
  `status` enum('pending','valid','tidak valid') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_checkups`
--

INSERT INTO `medical_checkups` (`id`, `user_id`, `tanggal`, `nama`, `hasil`, `file`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(2, 52, '2025-06-16', 'Klinik Mayapada', 'Sehat', 'document/52/BUGyvKE9unvqWyunjsB0lrTlh21Q31md.jpeg', NULL, 'pending', '2025-06-16 04:04:49', '2025-06-16 04:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2024_09_12_062305_create_settings_table', 1),
(3, '2014_10_12_000000_create_users_table', 2),
(4, '2014_10_12_100000_create_password_resets_table', 2),
(5, '2019_08_19_000000_create_failed_jobs_table', 2),
(6, '2019_12_14_000001_create_personal_access_tokens_table', 2),
(7, '2023_05_23_130148_create_permission_tables', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `program_id` bigint UNSIGNED NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('terdaftar','sedang_pelatihan','selesai','mengundurkan_diri') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'terdaftar',
  `nilai_akhir` int DEFAULT NULL COMMENT 'Nilai akhir pelatihan (1-100)',
  `sertifikat_diterbitkan` tinyint(1) DEFAULT '0',
  `nomor_sertifikat` varchar(100) DEFAULT NULL,
  `catatan` text,
  `didaftarkan_oleh` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`id`, `user_id`, `program_id`, `tanggal_daftar`, `tanggal_mulai`, `tanggal_selesai`, `status`, `nilai_akhir`, `sertifikat_diterbitkan`, `nomor_sertifikat`, `catatan`, `didaftarkan_oleh`, `created_at`, `updated_at`) VALUES
(4, 52, 5, '2025-06-16', '2025-06-15', '2025-06-20', 'terdaftar', NULL, 0, NULL, NULL, 1, '2025-06-16 04:06:25', '2025-06-16 04:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `training_program`
--

CREATE TABLE `training_program` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text,
  `durasi` int NOT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `instruktur` varchar(255) DEFAULT NULL,
  `persyaratan` text,
  `aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_program`
--

INSERT INTO `training_program` (`id`, `nama`, `deskripsi`, `durasi`, `lokasi`, `kapasitas`, `instruktur`, `persyaratan`, `aktif`, `created_at`, `updated_at`) VALUES
(1, 'Bahasa Arab Dasar', 'Pelatihan bahasa Arab untuk TKI ke negara Arab', 30, 'Jakarta Training Center', 25, 'Ustadz Ahmad', NULL, 1, NULL, NULL),
(2, 'Bahasa Inggris Dasar', 'Pelatihan bahasa Inggris untuk TKI ke negara berbahasa Inggris', 30, 'Jakarta Training Center', 25, 'Ms. Sarah', NULL, 1, NULL, NULL),
(3, 'Keterampilan Housekeeping', 'Pelatihan keterampilan housekeeping dan domestic worker', 14, 'Jakarta Training Center', 30, 'Ibu Sari', NULL, 1, NULL, NULL),
(4, 'Perawatan Lansia', 'Pelatihan merawat orang tua/lansia', 21, 'Jakarta Training Center', 20, 'dr. Budi', NULL, 1, NULL, NULL),
(5, 'Keterampilan Dapur', 'Pelatihan memasak dan mengurus dapur', 14, 'Jakarta Training Center', 20, 'Chef Anton', NULL, 1, NULL, NULL),
(6, 'wqeqw', 'qweqw', 12, 'ewqe', 21, 'wqeqw', NULL, 1, '2025-06-11 12:03:03', '2025-06-11 12:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `aktif` tinyint(1) DEFAULT '1',
  `remember_token` varchar(100) DEFAULT NULL,
  `status` enum('pending','diterima','interview','medical','pelatihan','siap','selesai','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `email_verified_at`, `password`, `aktif`, `remember_token`, `status`, `created_at`, `updated_at`) VALUES
(42, 'Tomi Prasetyo M.Ak', 'nsihotang@example.net', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '8Ht5G4Ec6k', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(43, 'Hani Ulva Hastuti', 'epuspasari@example.net', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'uaSWyGuObV', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(44, 'Betania Hastuti S.Psi', 'aris.siregar@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '4ymBHutjpT', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(45, 'Nadine Riyanti', 'vinsen08@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'pGfFQmLsMu', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(46, 'Putri Novi Utami', 'balidin.uwais@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'joCOlBCCYS', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(47, 'Mustika Kairav Napitupulu S.T.', 'himawan76@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'UhI9iAbNnh', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(48, 'Tantri Winarsih', 'dagel14@example.org', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'bq9qXuJ5Oz', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(49, 'Candrakanta Wibisono S.Gz', 'dmandasari@example.net', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'bHwxhv1KVK', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(50, 'Ghani Sihombing', 'qpuspasari@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'vlXxVhmDWG', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(51, 'Irma Suartini', 'nalar28@example.org', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'xEwjwPGGa1', 'pending', '2025-06-12 03:30:58', '2025-06-12 03:30:58'),
(52, 'tsadas', 'asdas@gmail.com', '2025-06-12 22:21:27', '$2y$10$YQ2jzx6x/FSu36QxXUsbae5nRDZEn8g5fgsLD4pUwnDMLcHR1EJiu', 1, 'O4OSzwpejmjbFz7ng97EkynCr3Sqp94bIiBVUAF24vn7k7Gg9uX4HTkYHjSz', 'medical', '2025-06-12 22:14:27', '2025-06-16 04:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` text NOT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_izin` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status_izin` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_ayah` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_ibu` varchar(191) DEFAULT NULL,
  `phone_ortu` varchar(191) DEFAULT NULL,
  `alamat_ortu` varchar(255) DEFAULT NULL,
  `pendidikan` json DEFAULT NULL,
  `pengalaman` json DEFAULT NULL,
  `skill_bahasa` json DEFAULT NULL,
  `skill_teknis` json DEFAULT NULL,
  `pekerjaan` varchar(191) DEFAULT NULL,
  `negara_tujuan` varchar(191) DEFAULT NULL,
  `ktp` varchar(255) DEFAULT NULL,
  `kk` varchar(255) DEFAULT NULL,
  `akte_lahir` varchar(255) DEFAULT NULL,
  `buku_nikah` varchar(255) DEFAULT NULL,
  `surat_keterangan_sehat` varchar(255) DEFAULT NULL,
  `surat_izin_keluarga` varchar(255) DEFAULT NULL,
  `kompetensi` varchar(255) DEFAULT NULL,
  `ijazah` varchar(255) DEFAULT NULL,
  `paspor` varchar(255) DEFAULT NULL,
  `surat_pengalaman_kerja` varchar(255) DEFAULT NULL,
  `skck` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('draft','tervalidasi','sudah_interview','medical','pelatihan','siap','tolak','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'draft',
  `catatan` text COMMENT 'Catatan dari talent manager/admin',
  `divalidasi_tanggal` timestamp NULL DEFAULT NULL,
  `divalidasi_oleh` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `phone`, `nama_izin`, `status_izin`, `nama_ayah`, `nama_ibu`, `phone_ortu`, `alamat_ortu`, `pendidikan`, `pengalaman`, `skill_bahasa`, `skill_teknis`, `pekerjaan`, `negara_tujuan`, `ktp`, `kk`, `akte_lahir`, `buku_nikah`, `surat_keterangan_sehat`, `surat_izin_keluarga`, `kompetensi`, `ijazah`, `paspor`, `surat_pengalaman_kerja`, `skck`, `foto`, `status`, `catatan`, `divalidasi_tanggal`, `divalidasi_oleh`, `created_at`, `updated_at`) VALUES
(1, 52, '32021021312', 'Udin Bro', 'Bandung', '2020-06-16', 'Laki-Laki', 'Bandung Aja', '9882611818', 'Bapa aing', 'Gubernur', 'Bapak Ainhg', 'Masasih', '212121212121', 'sadz', '[{\"jurusan\": \"wqwq\", \"tingkat\": \"SD\", \"tahun_lulus\": \"2024-12-31T17:00:00.000Z\", \"nama_sekolah\": \"3121\"}, {\"jurusan\": \"21\", \"tingkat\": \"SD\", \"tahun_lulus\": \"2024-12-31T17:00:00.000Z\", \"nama_sekolah\": \"21\"}]', '[{\"nama\": \"121\", \"posisi\": \"wq\", \"tahun_masuk\": \"2024-12-31T17:00:00.000Z\", \"tahun_keluar\": \"2024-12-31T17:00:00.000Z\"}]', NULL, NULL, 'sad', 'asdasd', 'document/52/hZBIswsQxhGWV0gjuIA4OJFl9NAiEO3I.jpeg', 'document/52/ozOC69F0GOZRtqiFBCNC1UH18eIZoiEF.png', 'document/52/ACQREndj4i7dIui8X8xbCIVj62uZHfLo.jpeg', 'document/52/GvVYTail0j4fleuMQr6EN74rgyq3VL9a.jpeg', 'document/52/T6RxgIhoXXYM6Juw4xbptObcF0Tt909a.jpeg', 'document/52/g267Ke4XuGxBH0sdbfy2tM7pNKoh5969.jpeg', NULL, 'document/52/1sSA9agF0X2GHsmGnU9jTSEGw4g3H658.jpeg', 'document/52/npXJa805ndZoTB7X80MzZh6F7DCFTBhk.jpeg', 'document/52/1lXxyzxfUIlwH9tNHpBr2dwtXuwvaO8V.jpeg', 'document/52/qsRfYCzCLoGeg5szoKSwPlVKYhAkCWM5.jpeg', 'document/52/FOPPYTOrUxAfTLianbKH5q4ZvfnKY7XS.jpeg', 'draft', 'Ditolak', NULL, NULL, '2025-06-14 01:27:39', '2025-06-16 04:01:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hasil_interview`
--
ALTER TABLE `hasil_interview`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_id` (`jadwal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hasil_interview_ibfk_3` (`dinilai_oleh`),
  ADD KEY `hasil_interview_ibfk_4` (`disetujui_oleh`);

--
-- Indexes for table `jadwal_interview`
--
ALTER TABLE `jadwal_interview`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jadwal_interview_talent_id` (`user_id`),
  ADD KEY `idx_jadwal_interview_tanggal` (`tanggal`),
  ADD KEY `jadwal_interview_ibfk_2` (`dibuat_oleh`),
  ADD KEY `jadwal_interview_ibfk_3` (`pewawancara_id`);

--
-- Indexes for table `kategori_dokumen`
--
ALTER TABLE `kategori_dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_checkups`
--
ALTER TABLE `medical_checkups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_medical_checkups_user_id` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `talent_program_unik` (`user_id`,`program_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `idx_pendaftaran_pelatihan_talent_id` (`user_id`),
  ADD KEY `idx_pendaftaran_pelatihan_status` (`status`),
  ADD KEY `pendaftaran_pelatihan_ibfk_3` (`didaftarkan_oleh`);

--
-- Indexes for table `training_program`
--
ALTER TABLE `training_program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `divalidasi_oleh` (`divalidasi_oleh`),
  ADD KEY `idx_talents_status` (`status`),
  ADD KEY `idx_talents_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hasil_interview`
--
ALTER TABLE `hasil_interview`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jadwal_interview`
--
ALTER TABLE `jadwal_interview`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kategori_dokumen`
--
ALTER TABLE `kategori_dokumen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_checkups`
--
ALTER TABLE `medical_checkups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `training_program`
--
ALTER TABLE `training_program`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_interview`
--
ALTER TABLE `hasil_interview`
  ADD CONSTRAINT `hasil_interview_ibfk_1` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_interview` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_interview_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `hasil_interview_ibfk_3` FOREIGN KEY (`dinilai_oleh`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `hasil_interview_ibfk_4` FOREIGN KEY (`disetujui_oleh`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `jadwal_interview`
--
ALTER TABLE `jadwal_interview`
  ADD CONSTRAINT `jadwal_interview_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `jadwal_interview_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `jadwal_interview_ibfk_3` FOREIGN KEY (`pewawancara_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `medical_checkups`
--
ALTER TABLE `medical_checkups`
  ADD CONSTRAINT `fk_medical_checkups_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training`
--
ALTER TABLE `training`
  ADD CONSTRAINT `training_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `training_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `training_program` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_ibfk_3` FOREIGN KEY (`didaftarkan_oleh`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_details_ibfk_2` FOREIGN KEY (`divalidasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
