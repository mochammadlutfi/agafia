<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Training TKI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c5282;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #2c5282;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #4a5568;
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 10px;
        }
        
        .header .export-info {
            font-size: 10px;
            color: #718096;
        }
        
        .filter-section {
            background-color: #f7fafc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .filter-title {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 3px;
        }
        
        .filter-content {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-item {
            display: flex;
            align-items: center;
        }
        
        .filter-label {
            font-weight: bold;
            color: #4a5568;
            margin-right: 5px;
        }
        
        .statistics-section {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
        }
        
        .stat-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 12px;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c5282;
        }
        
        .status-breakdown {
            margin-top: 8px;
            font-size: 9px;
        }
        
        .status-item {
            display: inline-block;
            margin: 1px 3px;
            padding: 1px 4px;
            border-radius: 2px;
            color: white;
        }
        
        .status-terdaftar { background-color: #f59e0b; }
        .status-sedang_pelatihan { background-color: #3b82f6; }
        .status-selesai { background-color: #10b981; }
        .status-mengundurkan_diri { background-color: #ef4444; }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .main-table th {
            background-color: #4a5568;
            color: white;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #2d3748;
            font-weight: bold;
            font-size: 10px;
        }
        
        .main-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 9px;
        }
        
        .main-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .training-detail {
            margin-bottom: 25px;
            page-break-inside: avoid;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .training-header {
            background-color: #2c5282;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .training-body {
            padding: 12px;
            background-color: white;
        }
        
        .training-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .info-group {
            flex: 1;
            min-width: 200px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4a5568;
            font-size: 9px;
            margin-bottom: 2px;
        }
        
        .info-value {
            color: #2d3748;
            font-size: 10px;
            border-bottom: 1px dotted #cbd5e0;
            padding-bottom: 2px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }
        
        .score-section {
            margin-top: 10px;
            padding: 8px;
            background-color: #f7fafc;
            border-radius: 3px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { print-color-adjust: exact; }
            .training-detail { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA TRAINING</h1>
        <h2>Sistem Manajemen TKI (Tenaga Kerja Indonesia)</h2>
        <div class="export-info">
            Diekspor pada: {{ $exportDate->format('d F Y, H:i:s') }} WIB
        </div>
    </div>

    <!-- Filter Information -->
    @if(!empty($filterInfo))
    <div class="filter-section">
        <div class="filter-title">Filter yang Diterapkan:</div>
        <div class="filter-content">
            @foreach($filterInfo as $label => $value)
            <div class="filter-item">
                <span class="filter-label">{{ $label }}:</span>
                <span>{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistics Summary -->
    <div class="statistics-section">
        <div class="stat-card">
            <h3>Total Training</h3>
            <div class="stat-number">{{ $statistics['total'] }}</div>
        </div>
        
        <div class="stat-card">
            <h3>Status Training</h3>
            <div class="status-breakdown">
                @foreach($statusOptions as $status => $label)
                    @if(isset($statistics['by_status'][$status]))
                    <span class="status-item status-{{ $status }}">
                        {{ $label }}: {{ $statistics['by_status'][$status] }}
                    </span>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="stat-card">
            <h3>Program Training</h3>
            <div class="status-breakdown">
                @foreach($statistics['by_program'] as $program => $count)
                <div style="font-size: 8px; margin: 1px 0;">{{ $program }}: {{ $count }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 18%;">Nama Peserta</th>
                <th style="width: 15%;">Program</th>
                <th style="width: 10%;">Tgl Daftar</th>
                <th style="width: 10%;">Tgl Mulai</th>
                <th style="width: 10%;">Tgl Selesai</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 8%;">Nilai</th>
                <th style="width: 12%;">Sertifikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $training)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $training->user->detail->nama ?? $training->user->nama }}</strong>
                    @if($training->lamaran && $training->lamaran->lowongan)
                    <br><small>{{ $training->lamaran->lowongan->posisi }}</small>
                    @endif
                </td>
                <td>{{ $training->program->nama ?? '-' }}</td>
                <td>{{ $training->tanggal_daftar ? \Carbon\Carbon::parse($training->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                <td>{{ $training->tanggal_mulai ? \Carbon\Carbon::parse($training->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                <td>{{ $training->tanggal_selesai ? \Carbon\Carbon::parse($training->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="status-badge status-{{ $training->status }}">
                        {{ $statusOptions[$training->status] ?? $training->status }}
                    </span>
                </td>
                <td>{{ $training->nilai_akhir ?? '-' }}</td>
                <td>
                    @if($training->sertifikat_diterbitkan)
                        ✓ {{ $training->nomor_sertifikat }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; font-style: italic; color: #718096;">
                    Tidak ada data training sesuai filter yang diterapkan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Detailed Training Information -->
    @if($data->count() > 0)
    <div class="page-break"></div>
    <h3 style="margin-bottom: 15px; color: #2c5282; border-bottom: 2px solid #2c5282; padding-bottom: 5px;">
        DETAIL INFORMASI TRAINING
    </h3>

    @foreach($data as $index => $training)
    <div class="training-detail">
        <div class="training-header">
            {{ $index + 1 }}. {{ $training->user->detail->nama ?? $training->user->nama }} - {{ $training->program->nama ?? 'Program Tidak Diketahui' }}
        </div>
        <div class="training-body">
            <div class="training-info">
                <div class="info-group">
                    <div class="info-label">Data Peserta</div>
                    <div class="info-value">{{ $training->user->detail->nama ?? $training->user->nama }}</div>
                    
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $training->user->email }}</div>
                    
                    @if($training->user->detail && $training->user->detail->no_hp)
                    <div class="info-label">No. HP</div>
                    <div class="info-value">{{ $training->user->detail->no_hp }}</div>
                    @endif
                </div>
                
                <div class="info-group">
                    <div class="info-label">Program Training</div>
                    <div class="info-value">{{ $training->program->nama ?? '-' }}</div>
                    
                    @if($training->program && $training->program->deskripsi)
                    <div class="info-label">Deskripsi Program</div>
                    <div class="info-value">{{ $training->program->deskripsi }}</div>
                    @endif
                    
                    @if($training->program && $training->program->durasi)
                    <div class="info-label">Durasi</div>
                    <div class="info-value">{{ $training->program->durasi }} hari</div>
                    @endif
                </div>
                
                <div class="info-group">
                    <div class="info-label">Tanggal Pendaftaran</div>
                    <div class="info-value">{{ $training->tanggal_daftar ? \Carbon\Carbon::parse($training->tanggal_daftar)->format('d F Y') : '-' }}</div>
                    
                    <div class="info-label">Tanggal Mulai</div>
                    <div class="info-value">{{ $training->tanggal_mulai ? \Carbon\Carbon::parse($training->tanggal_mulai)->format('d F Y') : '-' }}</div>
                    
                    <div class="info-label">Tanggal Selesai</div>
                    <div class="info-value">{{ $training->tanggal_selesai ? \Carbon\Carbon::parse($training->tanggal_selesai)->format('d F Y') : '-' }}</div>
                </div>
            </div>

            <div class="training-info">
                <div class="info-group">
                    <div class="info-label">Status Training</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ $training->status }}">
                            {{ $statusOptions[$training->status] ?? $training->status }}
                        </span>
                    </div>
                    
                    @if($training->staff)
                    <div class="info-label">Didaftarkan Oleh</div>
                    <div class="info-value">{{ $training->staff->nama }}</div>
                    @endif
                </div>
                
                @if($training->nilai_akhir || $training->sertifikat_diterbitkan)
                <div class="info-group">
                    @if($training->nilai_akhir)
                    <div class="info-label">Nilai Akhir</div>
                    <div class="info-value">{{ $training->nilai_akhir }}/100</div>
                    @endif
                    
                    @if($training->sertifikat_diterbitkan)
                    <div class="info-label">Status Sertifikat</div>
                    <div class="info-value">Sudah Diterbitkan</div>
                    
                    @if($training->nomor_sertifikat)
                    <div class="info-label">Nomor Sertifikat</div>
                    <div class="info-value">{{ $training->nomor_sertifikat }}</div>
                    @endif
                    @endif
                </div>
                @endif
                
                @if($training->catatan)
                <div class="info-group">
                    <div class="info-label">Catatan</div>
                    <div class="info-value">{{ $training->catatan }}</div>
                </div>
                @endif
            </div>

            @if($training->lamaran)
            <div style="margin-top: 10px; padding: 8px; background-color: #edf2f7; border-radius: 3px;">
                <div class="info-label">Informasi Lamaran Terkait</div>
                @if($training->lamaran->lowongan)
                <div style="font-size: 9px; color: #4a5568;">
                    Posisi: {{ $training->lamaran->lowongan->posisi }} | 
                    Perusahaan: {{ $training->lamaran->lowongan->perusahaan }} |
                    Tanggal Lamaran: {{ \Carbon\Carbon::parse($training->lamaran->tanggal_lamaran)->format('d F Y') }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Manajemen TKI</p>
        <p>© {{ date('Y') }} TKI Management System. Semua hak dilindungi.</p>
        <p style="margin-top: 5px; font-size: 8px;">
            Dokumen ini berisi {{ $data->count() }} record training
            @if(!empty($filterInfo))
            dengan filter yang diterapkan
            @endif
        </p>
    </div>
</body>
</html>