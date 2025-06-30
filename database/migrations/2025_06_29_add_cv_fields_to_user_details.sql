-- Migration untuk menambahkan field CV yang kurang di tabel user_details
-- File: database/migrations/2025_06_29_add_cv_fields_to_user_details.sql

-- Menambahkan field-field yang diperlukan untuk generate CV lengkap
ALTER TABLE `user_details` 
ADD COLUMN `foto` VARCHAR(255) NULL COMMENT 'Path foto profil untuk CV' AFTER `negara_tujuan`,
ADD COLUMN `agama` VARCHAR(50) NULL COMMENT 'Agama' AFTER `foto`,
ADD COLUMN `status_perkawinan` ENUM('lajang','menikah','cerai','janda','duda') NULL COMMENT 'Status perkawinan' AFTER `agama`,
ADD COLUMN `tinggi_badan` INT NULL COMMENT 'Tinggi badan dalam cm' AFTER `status_perkawinan`,
ADD COLUMN `berat_badan` INT NULL COMMENT 'Berat badan dalam kg' AFTER `tinggi_badan`,
ADD COLUMN `golongan_darah` ENUM('A','B','AB','O') NULL COMMENT 'Golongan darah' AFTER `berat_badan`,
ADD COLUMN `email_alternatif` VARCHAR(255) NULL COMMENT 'Email alternatif selain yang di tabel users' AFTER `golongan_darah`,

-- Data Alamat Tambahan
ADD COLUMN `alamat_domisili` TEXT NULL COMMENT 'Alamat domisili saat ini (berbeda dari alamat KTP)' AFTER `alamat`,
ADD COLUMN `kode_pos` VARCHAR(10) NULL COMMENT 'Kode pos' AFTER `alamat_domisili`,
ADD COLUMN `kecamatan` VARCHAR(100) NULL COMMENT 'Kecamatan' AFTER `kode_pos`,
ADD COLUMN `kabupaten_kota` VARCHAR(100) NULL COMMENT 'Kabupaten/Kota' AFTER `kecamatan`,
ADD COLUMN `provinsi` VARCHAR(100) NULL COMMENT 'Provinsi' AFTER `kabupaten_kota`,

-- Data Kontak Darurat
ADD COLUMN `kontak_darurat_nama` VARCHAR(255) NULL COMMENT 'Nama kontak darurat' AFTER `alamat_ortu`,
ADD COLUMN `kontak_darurat_phone` VARCHAR(15) NULL COMMENT 'Nomor telepon kontak darurat' AFTER `kontak_darurat_nama`,
ADD COLUMN `kontak_darurat_hubungan` VARCHAR(100) NULL COMMENT 'Hubungan dengan kontak darurat' AFTER `kontak_darurat_phone`,
ADD COLUMN `kontak_darurat_alamat` TEXT NULL COMMENT 'Alamat kontak darurat' AFTER `kontak_darurat_hubungan`,

-- Data Media Sosial (JSON)
ADD COLUMN `media_sosial` JSON NULL COMMENT 'Media sosial: {"instagram":"@username","facebook":"nama","linkedin":"profile_url","whatsapp":"nomor"}' AFTER `email_alternatif`,

-- Data Profesional Tambahan
ADD COLUMN `objektif_karir` TEXT NULL COMMENT 'Career objective/tujuan karir' AFTER `skill_teknis`,
ADD COLUMN `ringkasan_profil` TEXT NULL COMMENT 'Professional summary/ringkasan profil' AFTER `objektif_karir`,
ADD COLUMN `hobi` TEXT NULL COMMENT 'Hobi dan minat' AFTER `ringkasan_profil`,

-- Data Sertifikat & Prestasi (JSON)
ADD COLUMN `sertifikat` JSON NULL COMMENT 'Array sertifikat: [{"nama":"","penerbit":"","tahun":"","nomor":"","file_path":""}]' AFTER `hobi`,
ADD COLUMN `prestasi` JSON NULL COMMENT 'Array prestasi: [{"nama":"","tingkat":"","tahun":"","deskripsi":""}]' AFTER `sertifikat`,
ADD COLUMN `organisasi` JSON NULL COMMENT 'Array organisasi: [{"nama":"","jabatan":"","tahun_mulai":"","tahun_selesai":"","deskripsi":""}]' AFTER `prestasi`,

-- Data Referensi (JSON)
ADD COLUMN `referensi` JSON NULL COMMENT 'Array referensi: [{"nama":"","jabatan":"","perusahaan":"","phone":"","email":"","hubungan":""}]' AFTER `organisasi`,

-- Data Khusus TKI
ADD COLUMN `pengalaman_luar_negeri` JSON NULL COMMENT 'Pengalaman kerja luar negeri: [{"negara":"","perusahaan":"","posisi":"","tahun_mulai":"","tahun_selesai":"","deskripsi":"","gaji":""}]' AFTER `pengalaman`,
ADD COLUMN `bahasa_asing_skor` JSON NULL COMMENT 'Skor tes bahasa: {"toefl":"","ielts":"","jlpt":"","topik":"","arabic":"","mandarin":""}' AFTER `skill_bahasa`,
ADD COLUMN `visa_history` JSON NULL COMMENT 'Riwayat visa: [{"negara":"","jenis_visa":"","tahun":"","status":"","keterangan":""}]' AFTER `bahasa_asing_skor`,
ADD COLUMN `keahlian_khusus` TEXT NULL COMMENT 'Keahlian khusus untuk pekerjaan overseas' AFTER `skill_teknis`,

-- Data Kesehatan
ADD COLUMN `kondisi_kesehatan` TEXT NULL COMMENT 'Kondisi kesehatan khusus/alergi' AFTER `golongan_darah`,
ADD COLUMN `medical_checkup_terakhir` DATE NULL COMMENT 'Tanggal medical check up terakhir' AFTER `kondisi_kesehatan`,

-- Data Tambahan untuk CV
ADD COLUMN `motto_hidup` TEXT NULL COMMENT 'Motto hidup/quotes' AFTER `hobi`,
ADD COLUMN `achievement_highlights` TEXT NULL COMMENT 'Highlight pencapaian terpenting' AFTER `prestasi`,

-- Metadata CV
ADD COLUMN `cv_template` VARCHAR(50) NULL DEFAULT 'default' COMMENT 'Template CV yang dipilih' AFTER `divalidasi_oleh`,
ADD COLUMN `cv_bahasa` ENUM('id','en','ar','ja','ko') NULL DEFAULT 'id' COMMENT 'Bahasa CV yang akan di-generate' AFTER `cv_template`,
ADD COLUMN `cv_generated_at` TIMESTAMP NULL COMMENT 'Terakhir kali CV di-generate' AFTER `cv_bahasa`,
ADD COLUMN `cv_file_path` VARCHAR(255) NULL COMMENT 'Path file CV yang terakhir di-generate' AFTER `cv_generated_at`;

-- Menambahkan index untuk optimasi query
ALTER TABLE `user_details`
ADD INDEX `idx_user_details_status_perkawinan` (`status_perkawinan`),
ADD INDEX `idx_user_details_agama` (`agama`),
ADD INDEX `idx_user_details_provinsi` (`provinsi`),
ADD INDEX `idx_user_details_cv_template` (`cv_template`);

-- Komentar tabel update
ALTER TABLE `user_details` COMMENT = 'Tabel detail user dengan field lengkap untuk generate CV profesional TKI';