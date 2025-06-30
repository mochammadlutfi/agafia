-- Contoh data untuk field JSON yang baru ditambahkan
-- File: database/seeds/cv_field_examples.sql

-- ==========================================
-- CONTOH STRUKTUR JSON UNTUK FIELD BARU
-- ==========================================

-- 1. MEDIA SOSIAL (media_sosial)
-- Struktur: {"platform": "username/url"}
/*
{
  "instagram": "@john_doe_worker",
  "facebook": "John Doe",
  "linkedin": "https://linkedin.com/in/johndoe",
  "whatsapp": "628123456789",
  "telegram": "@johndoe"
}
*/

-- 2. SERTIFIKAT (sertifikat)
-- Struktur: Array of objects
/*
[
  {
    "nama": "Sertifikat Housekeeping Professional",
    "penerbit": "LSPP - Lembaga Sertifikasi Profesi Pariwisata",
    "tahun": "2023",
    "nomor": "HK-2023-001234",
    "tanggal_terbit": "2023-03-15",
    "tanggal_expired": "2026-03-15",
    "file_path": "/uploads/sertifikat/housekeeping_2023.pdf",
    "deskripsi": "Sertifikat kompetensi housekeeping untuk hotel dan resort"
  },
  {
    "nama": "TOEFL Certificate",
    "penerbit": "ETS - Educational Testing Service",
    "tahun": "2023",
    "nomor": "TOEFL-2023-567890",
    "skor": "520",
    "tanggal_terbit": "2023-06-20",
    "tanggal_expired": "2025-06-20",
    "file_path": "/uploads/sertifikat/toefl_2023.pdf"
  }
]
*/

-- 3. PRESTASI (prestasi)
-- Struktur: Array of objects
/*
[
  {
    "nama": "Juara 1 Lomba Memasak Tradisional",
    "penyelenggara": "Dinas Pariwisata Kota Bandung",
    "tingkat": "Kota",
    "tahun": "2022",
    "bulan": "08",
    "deskripsi": "Juara pertama lomba memasak masakan tradisional Sunda tingkat kota",
    "hadiah": "Trofi + Uang Pembinaan Rp 5.000.000"
  },
  {
    "nama": "Best Employee of the Month",
    "penyelenggara": "Hotel Grand Sahid",
    "tingkat": "Perusahaan",
    "tahun": "2021",
    "bulan": "12",
    "deskripsi": "Karyawan terbaik bulan Desember di departemen housekeeping"
  }
]
*/

-- 4. ORGANISASI (organisasi)
-- Struktur: Array of objects
/*
[
  {
    "nama": "Asosiasi Pekerja Migran Indonesia (APMI)",
    "jabatan": "Anggota Aktif",
    "tahun_mulai": "2020",
    "tahun_selesai": "sekarang",
    "deskripsi": "Aktif dalam kegiatan sosialisasi hak-hak pekerja migran",
    "aktivitas": ["Seminar ketenagakerjaan", "Pelatihan soft skill", "Advokasi pekerja"]
  },
  {
    "nama": "Karang Taruna Desa Sukamaju",
    "jabatan": "Sekretaris",
    "tahun_mulai": "2018",
    "tahun_selesai": "2020",
    "deskripsi": "Mengorganisir kegiatan pemuda di desa",
    "aktivitas": ["Bakti sosial", "Pelatihan kewirausahaan", "Kegiatan olahraga"]
  }
]
*/

-- 5. REFERENSI (referensi)
-- Struktur: Array of objects
/*
[
  {
    "nama": "Siti Nurhaliza",
    "jabatan": "Supervisor Housekeeping",
    "perusahaan": "Hotel Grand Sahid Jakarta",
    "phone": "021-5551234",
    "email": "siti.supervisor@grandsahid.com",
    "hubungan": "Atasan langsung",
    "lama_kenal": "2 tahun",
    "deskripsi": "Mengenal kemampuan kerja dan dedikasi yang tinggi"
  },
  {
    "nama": "Budi Santoso",
    "jabatan": "Manager HRD",
    "perusahaan": "PT. Maju Bersama",
    "phone": "022-7778888",
    "email": "budi.hrd@majubersama.co.id",
    "hubungan": "Mantan atasan",
    "lama_kenal": "3 tahun",
    "deskripsi": "Mengenal etos kerja dan kemampuan beradaptasi yang baik"
  }
]
*/

-- 6. PENGALAMAN LUAR NEGERI (pengalaman_luar_negeri)
-- Struktur: Array of objects - tambahan untuk field pengalaman existing
/*
[
  {
    "negara": "Malaysia",
    "kota": "Kuala Lumpur",
    "perusahaan": "Sunway Resort Hotel & Spa",
    "posisi": "Room Attendant",
    "tahun_mulai": "2019",
    "bulan_mulai": "06",
    "tahun_selesai": "2021",
    "bulan_selesai": "12",
    "gaji": "RM 1,200",
    "mata_uang": "MYR",
    "deskripsi": "Bertanggung jawab atas kebersihan dan kerapihan kamar hotel",
    "prestasi": ["Employee of the month (3x)", "Zero complaint record"],
    "alasan_keluar": "Kontrak selesai",
    "kontak_supervisor": {
      "nama": "Ms. Lim Wei Ling",
      "phone": "+60123456789",
      "email": "wlim@sunwayhotels.com"
    }
  }
]
*/

-- 7. BAHASA ASING SKOR (bahasa_asing_skor)
-- Struktur: Object dengan key bahasa
/*
{
  "english": {
    "toefl": {
      "skor": "520",
      "tahun": "2023",
      "institusi": "ETS"
    },
    "ielts": {
      "skor": "6.5",
      "tahun": "2023",
      "institusi": "British Council"
    },
    "level": "Intermediate"
  },
  "arabic": {
    "level": "Basic",
    "keterangan": "Bisa percakapan sehari-hari",
    "sumber_belajar": "Kursus di LIPIA Jakarta"
  },
  "japanese": {
    "jlpt": {
      "level": "N4",
      "tahun": "2022"
    },
    "level": "Basic"
  },
  "mandarin": {
    "hsk": {
      "level": "HSK 3",
      "skor": "180",
      "tahun": "2022"
    },
    "level": "Elementary"
  }
}
*/

-- 8. VISA HISTORY (visa_history)
-- Struktur: Array of objects
/*
[
  {
    "negara": "Malaysia",
    "jenis_visa": "Work Permit",
    "nomor_visa": "DP1234567890",
    "tahun_mulai": "2019",
    "tahun_selesai": "2021",
    "status": "Expired",
    "sponsor": "Sunway Resort Hotel & Spa",
    "keterangan": "Kontrak 2 tahun sebagai Room Attendant",
    "multiple_entry": true
  },
  {
    "negara": "Singapura",
    "jenis_visa": "Tourist Visa",
    "nomor_visa": "SG2023001234",
    "tahun": "2023",
    "status": "Used",
    "durasi": "30 hari",
    "tujuan": "Liburan",
    "keterangan": "Visa turis untuk berlibur"
  }
]
*/

-- ==========================================
-- CONTOH UPDATE DATA EXISTING
-- ==========================================

-- Contoh update user existing dengan data CV lengkap
UPDATE `user_details` SET
  `foto` = '/uploads/foto/user_52_profile.jpg',
  `agama` = 'Islam',
  `status_perkawinan` = 'lajang',
  `tinggi_badan` = 165,
  `berat_badan` = 55,
  `golongan_darah` = 'A',
  `email_alternatif` = 'udin.worker@gmail.com',
  `alamat_domisili` = 'Jl. Sudirman No. 123, RT 01/RW 05',
  `kode_pos` = '40123',
  `kecamatan` = 'Babakan Ciparay',
  `kabupaten_kota` = 'Bandung',
  `provinsi` = 'Jawa Barat',
  `kontak_darurat_nama` = 'Siti Aisyah',
  `kontak_darurat_phone` = '081234567890',
  `kontak_darurat_hubungan` = 'Adik kandung',
  `kontak_darurat_alamat` = 'Jl. Merdeka No. 45, Bandung',
  `media_sosial` = JSON_OBJECT(
    'instagram', '@udin_professional',
    'whatsapp', '6281234567890',
    'facebook', 'Udin Professional Worker'
  ),
  `objektif_karir` = 'Mencari kesempatan kerja di luar negeri sebagai housekeeping professional dengan dedikasi tinggi dan kemampuan bahasa Inggris yang baik untuk mengembangkan karir internasional.',
  `ringkasan_profil` = 'Pekerja berpengalaman 3+ tahun di bidang housekeeping dengan rekam jejak yang baik. Memiliki sertifikat kompetensi dan kemampuan bahasa Inggris intermediate. Telah bekerja di Malaysia selama 2 tahun.',
  `hobi` = 'Memasak, Membaca, Olahraga (Badminton), Traveling',
  `motto_hidup` = 'Kerja keras, jujur, dan selalu belajar hal baru',
  `kondisi_kesehatan` = 'Sehat, tidak ada alergi khusus',
  `medical_checkup_terakhir` = '2023-06-15',
  `cv_template` = 'professional',
  `cv_bahasa` = 'id'
WHERE `user_id` = 52;

-- ==========================================
-- TIPS PENGGUNAAN
-- ==========================================

/*
1. Gunakan JSON_OBJECT() untuk insert data JSON:
   JSON_OBJECT('key1', 'value1', 'key2', 'value2')

2. Gunakan JSON_ARRAY() untuk array JSON:
   JSON_ARRAY(JSON_OBJECT('nama', 'Sertifikat A'), JSON_OBJECT('nama', 'Sertifikat B'))

3. Query JSON field:
   SELECT JSON_EXTRACT(sertifikat, '$[0].nama') FROM user_details;

4. Update JSON field:
   UPDATE user_details SET media_sosial = JSON_SET(media_sosial, '$.instagram', '@new_username');

5. Validasi JSON:
   SELECT * FROM user_details WHERE JSON_VALID(sertifikat) = 1;
*/