<!DOCTYPE html>
<html>
<head>
    <title>CV - {{ $data->detail->nama ?? 'Nama Pelamar' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.3;
            color: #2c3e50;
            background: #ffffff;
        }
        
        .cv-container {
            width: 100%;
            min-height: 100vh;
        }
        
        /* Header dengan gradient simulasi */
        .cv-header {
            background: #34495e;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
            margin-bottom: 20px;
        }
        
        .cv-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.9;
        }
        
        .cv-header-content {
            position: relative;
            z-index: 2;
        }
        
        .cv-header h1 {
            font-size: 24px;
            margin: 0 0 8px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .cv-subtitle {
            font-size: 12px;
            margin: 0;
            font-style: italic;
            opacity: 0.95;
        }
        
        /* Layout utama */
        .cv-body {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .cv-row {
            display: table-row;
        }
        
        .cv-col-left {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            padding-right: 15px;
            background: #f8f9fa;
            padding: 15px;
        }
        
        .cv-col-right {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-left: 15px;
            padding: 15px;
        }
        
        /* Sections */
        .cv-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        
        .cv-section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 3px;
            border-bottom: 2px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Profile photo */
        .profile-photo {
            width: 120px;
            height: 150px;
            object-fit: cover;
            margin: 0 auto 15px auto;
            display: block;
            border: 3px solid #3498db;
            border-radius: 8px;
        }
        
        /* Contact info */
        .contact-info {
            margin-bottom: 15px;
        }
        
        .contact-item {
            margin-bottom: 6px;
            line-height: 1.4;
        }
        
        .contact-label {
            font-weight: bold;
            color: #34495e;
            width: 70px;
            display: inline-block;
            vertical-align: top;
        }
        
        .contact-value {
            display: inline-block;
            width: calc(100% - 75px);
            vertical-align: top;
        }
        
        /* Personal info */
        .personal-info {
            margin-bottom: 15px;
        }
        
        .info-row {
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .info-label {
            font-weight: bold;
            color: #34495e;
            width: 80px;
            display: inline-block;
            vertical-align: top;
            font-size: 9px;
        }
        
        .info-value {
            display: inline-block;
            width: calc(100% - 85px);
            vertical-align: top;
            font-size: 9px;
        }
        
        /* Experience items */
        .experience-item {
            margin-bottom: 15px;
            padding: 12px;
            border-left: 3px solid #3498db;
            background: #f8f9fa;
            page-break-inside: avoid;
        }
        
        .experience-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        
        .experience-company {
            font-size: 10px;
            color: #3498db;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .experience-period {
            font-size: 9px;
            color: #7f8c8d;
            font-style: italic;
            margin-bottom: 6px;
        }
        
        .experience-description {
            font-size: 9px;
            color: #5d6d7e;
            line-height: 1.2;
        }
        
        /* Text blocks */
        .text-block {
            background: #ecf0f1;
            padding: 10px;
            border-left: 3px solid #27ae60;
            margin-bottom: 12px;
            text-align: justify;
            font-size: 9px;
            line-height: 1.3;
        }
        
        /* Skills */
        .skills-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .skill-item {
            background: #3498db;
            color: white;
            padding: 4px 8px;
            margin: 3px 0;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
            margin-right: 5px;
        }
        
        /* Footer */
        .cv-footer {
            margin-top: 30px;
            padding: 15px;
            background: #ecf0f1;
            text-align: center;
            border-top: 2px solid #bdc3c7;
        }
        
        .cv-footer-text {
            font-size: 8px;
            color: #7f8c8d;
        }
        
        /* Utilities */
        .mb-small { margin-bottom: 8px; }
        .mb-medium { margin-bottom: 12px; }
        .mb-large { margin-bottom: 20px; }
        
        .text-bold { font-weight: bold; }
        .text-italic { font-style: italic; }
        .text-center { text-align: center; }
        
        /* Print specific */
        .page-break {
            page-break-before: always;
        }
        
        /* Responsive text untuk content yang panjang */
        .long-text {
            font-size: 9px;
            line-height: 1.2;
            text-align: justify;
        }
        
        /* Address styling */
        .address-block {
            background: #f1f2f6;
            padding: 8px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 9px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="cv-container">
        <!-- Header -->
        <div class="cv-header">
            <div class="cv-header-content">
                <h1>{{ $data->detail->nama ?? 'NAMA LENGKAP' }}</h1>
                <div class="cv-subtitle">
                    {{ $data->detail->pekerjaan ?? 'Posisi yang Dilamar' }}
                    @if($data->detail->negara_tujuan)
                        • Target: {{ $data->detail->negara_tujuan }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="cv-body">
            <div class="cv-row">
                <!-- Left Column -->
                <div class="cv-col-left">
                    <!-- Profile Photo -->
                    @if($data->detail->foto)
                        <div class="text-center mb-medium">
                            <img src="{{ public_path($data->detail->foto) }}" alt="Foto Profil" class="profile-photo">
                        </div>
                    @endif

                    <!-- Contact Information -->
                    <div class="cv-section">
                        <div class="cv-section-title">Kontak</div>
                        <div class="contact-info">
                            @if($data->detail->phone)
                            <div class="contact-item">
                                <span class="contact-label">Telepon:</span>
                                <span class="contact-value">{{ $data->detail->phone }}</span>
                            </div>
                            @endif
                            
                            @if($data->email)
                            <div class="contact-item">
                                <span class="contact-label">Email:</span>
                                <span class="contact-value">{{ $data->email }}</span>
                            </div>
                            @endif
                            
                            @if($data->detail->email_alternatif)
                            <div class="contact-item">
                                <span class="contact-label">Email Alt:</span>
                                <span class="contact-value">{{ $data->detail->email_alternatif }}</span>
                            </div>
                            @endif
                        </div>
                        
                        @if($data->detail->alamat)
                        <div class="address-block">
                            <div class="text-bold mb-small">Alamat:</div>
                            {{ $data->detail->alamat }}
                            @if($data->detail->kecamatan || $data->detail->kabupaten_kota)
                                <br>{{ $data->detail->kecamatan ?? '' }}{{ $data->detail->kecamatan && $data->detail->kabupaten_kota ? ', ' : '' }}{{ $data->detail->kabupaten_kota ?? '' }}
                            @endif
                            @if($data->detail->provinsi)
                                <br>{{ $data->detail->provinsi ?? '' }} {{ $data->detail->kode_pos ?? '' }}
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Personal Information -->
                    <div class="cv-section">
                        <div class="cv-section-title">Data Pribadi</div>
                        <div class="personal-info">
                            @if($data->detail->nik)
                            <div class="info-row">
                                <span class="info-label">NIK:</span>
                                <span class="info-value">{{ $data->detail->nik }}</span>
                            </div>
                            @endif
                            
                            @if($data->detail->jenis_kelamin)
                            <div class="info-row">
                                <span class="info-label">Gender:</span>
                                <span class="info-value">{{ $data->detail->jenis_kelamin }}</span>
                            </div>
                            @endif
                            
                            @if($data->detail->tempat_lahir || $data->detail->tanggal_lahir)
                            <div class="info-row">
                                <span class="info-label">TTL:</span>
                                <span class="info-value">
                                    {{ $data->detail->tempat_lahir ?? '' }}{{ $data->detail->tempat_lahir && $data->detail->tanggal_lahir ? ', ' : '' }}{{ $data->detail->tanggal_lahir ? \Carbon\Carbon::parse($data->detail->tanggal_lahir)->format('d M Y') : '' }}
                                </span>
                            </div>
                            @endif
                            
                            @if($data->detail->agama)
                            <div class="info-row">
                                <span class="info-label">Agama:</span>
                                <span class="info-value">{{ $data->detail->agama }}</span>
                            </div>
                            @endif
                            
                            @if($data->detail->status_perkawinan)
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value">{{ ucfirst($data->detail->status_perkawinan) }}</span>
                            </div>
                            @endif
                            
                            @if($data->detail->tinggi_badan || $data->detail->berat_badan)
                            <div class="info-row">
                                <span class="info-label">TB/BB:</span>
                                <span class="info-value">{{ $data->detail->tinggi_badan ?? '-' }} cm / {{ $data->detail->berat_badan ?? '-' }} kg</span>
                            </div>
                            @endif
                            
                            @if($data->detail->golongan_darah)
                            <div class="info-row">
                                <span class="info-label">Gol. Darah:</span>
                                <span class="info-value">{{ $data->detail->golongan_darah }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Skills -->
                    @if($data->detail->keahlian_khusus)
                    <div class="cv-section">
                        <div class="cv-section-title">Keahlian</div>
                        <div class="text-block">
                            {{ $data->detail->keahlian_khusus }}
                        </div>
                    </div>
                    @endif

                    <!-- Emergency Contact -->
                    @if($data->detail->kontak_darurat_nama)
                    <div class="cv-section">
                        <div class="cv-section-title">Kontak Darurat</div>
                        <div class="personal-info">
                            <div class="info-row">
                                <span class="info-label">Nama:</span>
                                <span class="info-value">{{ $data->detail->kontak_darurat_nama }}</span>
                            </div>
                            @if($data->detail->kontak_darurat_hubungan)
                            <div class="info-row">
                                <span class="info-label">Hubungan:</span>
                                <span class="info-value">{{ $data->detail->kontak_darurat_hubungan }}</span>
                            </div>
                            @endif
                            @if($data->detail->kontak_darurat_phone)
                            <div class="info-row">
                                <span class="info-label">Telepon:</span>
                                <span class="info-value">{{ $data->detail->kontak_darurat_phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="cv-col-right">
                    <!-- Professional Summary -->
                    @if($data->detail->ringkasan_profil)
                    <div class="cv-section">
                        <div class="cv-section-title">Ringkasan Profil</div>
                        <div class="text-block long-text">
                            {{ $data->detail->ringkasan_profil }}
                        </div>
                    </div>
                    @endif

                    <!-- Career Objective -->
                    @if($data->detail->objektif_karir)
                    <div class="cv-section">
                        <div class="cv-section-title">Objektif Karir</div>
                        <div class="text-block long-text">
                            {{ $data->detail->objektif_karir }}
                        </div>
                    </div>
                    @endif

                    <!-- Education -->
                    @if(is_array($data->detail->pendidikan) && count($data->detail->pendidikan) > 0)
                    <div class="cv-section">
                        <div class="cv-section-title">Riwayat Pendidikan</div>
                        @foreach($data->detail->pendidikan as $pendidikan)
                        <div class="experience-item">
                            <div class="experience-title">{{ $pendidikan['tingkat'] ?? '-' }}</div>
                            <div class="experience-company">{{ $pendidikan['nama_sekolah'] ?? '-' }}</div>
                            @if(isset($pendidikan['jurusan']) && $pendidikan['jurusan'])
                            <div class="experience-description">Jurusan: {{ $pendidikan['jurusan'] }}</div>
                            @endif
                            <div class="experience-period">Tahun Lulus: {{ $pendidikan['tahun_lulus'] ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Work Experience -->
                    @if(is_array($data->detail->pengalaman) && count($data->detail->pengalaman) > 0)
                    <div class="cv-section">
                        <div class="cv-section-title">Pengalaman Kerja</div>
                        @foreach($data->detail->pengalaman as $pengalaman)
                        <div class="experience-item">
                            <div class="experience-title">{{ $pengalaman['posisi'] ?? '-' }}</div>
                            <div class="experience-company">{{ $pengalaman['nama'] ?? '-' }}</div>
                            <div class="experience-period">
                                {{ $pengalaman['tahun_masuk'] ?? '-' }} - {{ $pengalaman['tahun_keluar'] ?? 'Sekarang' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Additional Information -->
                    @if($data->detail->hobi || $data->detail->motto_hidup)
                    <div class="cv-section">
                        <div class="cv-section-title">Informasi Tambahan</div>
                        
                        @if($data->detail->hobi)
                        <div class="mb-medium">
                            <div class="text-bold mb-small">Hobi & Minat:</div>
                            <div class="long-text">{{ $data->detail->hobi }}</div>
                        </div>
                        @endif
                        
                        @if($data->detail->motto_hidup)
                        <div class="mb-medium">
                            <div class="text-bold mb-small">Motto Hidup:</div>
                            <div class="long-text text-italic">"{{ $data->detail->motto_hidup }}"</div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Health Information -->
                    @if($data->detail->kondisi_kesehatan || $data->detail->medical_checkup_terakhir)
                    <div class="cv-section">
                        <div class="cv-section-title">Informasi Kesehatan</div>
                        @if($data->detail->kondisi_kesehatan)
                        <div class="mb-small">
                            <span class="text-bold">Kondisi Kesehatan:</span> {{ $data->detail->kondisi_kesehatan }}
                        </div>
                        @endif
                        @if($data->detail->medical_checkup_terakhir)
                        <div class="mb-small">
                            <span class="text-bold">MCU Terakhir:</span>
                            {{ \Carbon\Carbon::parse($data->detail->medical_checkup_terakhir)->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="cv-footer">
            <div class="cv-footer-text">
                CV dibuat pada {{ date('d M Y') }} • PT. Agafia Adda Mandiri - Tenaga Kerja Indonesia<br>
                Dokumen ini dibuat secara otomatis oleh sistem manajemen TKI
            </div>
        </div>
    </div>
</body>
</html>