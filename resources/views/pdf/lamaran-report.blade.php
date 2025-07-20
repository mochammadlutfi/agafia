<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Lamaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-info h3 {
            margin-top: 0;
            font-size: 14px;
            color: #333;
        }
        .filter-row {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-size: 9px;
            text-align: center;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-diterima { background-color: #28a745; }
        .status-ditolak { background-color: #dc3545; }
        .status-interview { background-color: #007bff; }
        .status-medical { background-color: #17a2b8; }
        .status-pelatihan { background-color: #6f42c1; }
        .status-siap { background-color: #28a745; }
        .status-selesai { background-color: #20c997; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .text-center { text-align: center; }
        .text-nowrap { white-space: nowrap; }
        .summary {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA LAMARAN</h1>
        <h2>Sistem Manajemen TKI (Tenaga Kerja Indonesia)</h2>
        <p style="margin: 5px 0; font-size: 11px;">
            Diekspor pada: {{ $filterInfo['tanggal_export'] }}
        </p>
    </div>

    <div class="filter-info">
        <h3>Informasi Filter:</h3>
        <div class="filter-row">
            <span class="filter-label">Status:</span> {{ $filterInfo['status'] }}
        </div>
        @if($filterInfo['tanggal_dari'])
        <div class="filter-row">
            <span class="filter-label">Periode:</span> {{ $filterInfo['tanggal_dari'] }} - {{ $filterInfo['tanggal_sampai'] ?? 'Sekarang' }}
        </div>
        @endif
        <div class="filter-row">
            <span class="filter-label">Total Data:</span> {{ $filterInfo['total_data'] }} lamaran
        </div>
    </div>

    <!-- Summary Statistics -->
    @php
        $statusCounts = $data->groupBy('status')->map->count();
    @endphp
    <div class="summary">
        <h3 style="margin-top: 0;">Ringkasan Berdasarkan Status:</h3>
        @foreach($statusOptions as $status => $label)
            <div class="summary-item">
                <strong>{{ $label }}:</strong> {{ $statusCounts[$status] ?? 0 }}
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 18%;">Nama Pelamar</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 12%;">NIK</th>
                <th style="width: 15%;">Posisi</th>
                <th style="width: 12%;">Perusahaan</th>
                <th style="width: 10%;">Tgl Lamaran</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $lamaran)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $lamaran->user->detail->nama ?? $lamaran->user->nama }}</td>
                <td>{{ $lamaran->user->email }}</td>
                <td class="text-center">{{ $lamaran->user->detail->nik ?? '-' }}</td>
                <td>{{ $lamaran->lowongan->posisi }}</td>
                <td>{{ $lamaran->lowongan->perusahaan }}</td>
                <td class="text-center text-nowrap">{{ $lamaran->tanggal_lamaran->format('d/m/Y') }}</td>
                <td class="text-center">
                    <span class="status-badge status-{{ $lamaran->status }}">
                        {{ $statusOptions[$lamaran->status] ?? $lamaran->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #666;">
                    Tidak ada data lamaran yang sesuai dengan filter yang dipilih
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Additional Details Section -->
    @if($data->count() > 0)
    <div style="margin-top: 30px;">
        <h3>Detail Lengkap Lamaran</h3>
        @foreach($data as $index => $lamaran)
        <div style="border: 1px solid #ddd; margin-bottom: 15px; padding: 10px; page-break-inside: avoid;">
            <h4 style="margin: 0 0 10px 0; background-color: #f8f9fa; padding: 5px; border-radius: 3px;">
                {{ $index + 1 }}. {{ $lamaran->user->detail->nama ?? $lamaran->user->nama }}
            </h4>
            
            <table style="width: 100%; margin-bottom: 10px;">
                <tr>
                    <td style="width: 20%; border: none; padding: 2px;"><strong>Email:</strong></td>
                    <td style="border: none; padding: 2px;">{{ $lamaran->user->email }}</td>
                    <td style="width: 20%; border: none; padding: 2px;"><strong>Posisi:</strong></td>
                    <td style="border: none; padding: 2px;">{{ $lamaran->lowongan->posisi }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;"><strong>NIK:</strong></td>
                    <td style="border: none; padding: 2px;">{{ $lamaran->user->detail->nik ?? '-' }}</td>
                    <td style="border: none; padding: 2px;"><strong>Perusahaan:</strong></td>
                    <td style="border: none; padding: 2px;">{{ $lamaran->lowongan->perusahaan }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;"><strong>Status:</strong></td>
                    <td style="border: none; padding: 2px;">
                        <span class="status-badge status-{{ $lamaran->status }}">
                            {{ $statusOptions[$lamaran->status] ?? $lamaran->status }}
                        </span>
                    </td>
                    <td style="border: none; padding: 2px;"><strong>Tgl Lamaran:</strong></td>
                    <td style="border: none; padding: 2px;">{{ $lamaran->tanggal_lamaran->format('d/m/Y') }}</td>
                </tr>
            </table>

            <!-- Progress Information -->
            @if($lamaran->interview)
            <div style="margin-top: 8px;">
                <strong>Interview:</strong> 
                {{ $lamaran->interview->tanggal ? \Carbon\Carbon::parse($lamaran->interview->tanggal)->format('d/m/Y') : '-' }}
                ({{ $lamaran->interview->status }})
                @if($lamaran->interview->pewawancara)
                - Pewawancara: {{ $lamaran->interview->pewawancara->nama }}
                @endif
            </div>
            @endif

            @if($lamaran->medical)
            <div style="margin-top: 3px;">
                <strong>Medical Checkup:</strong> 
                {{ \Carbon\Carbon::parse($lamaran->medical->tanggal)->format('d/m/Y') }}
                - {{ $lamaran->medical->nama }}
                (Status: {{ $lamaran->medical->status }})
            </div>
            @endif

            @if($lamaran->training->count() > 0)
            <div style="margin-top: 3px;">
                <strong>Training:</strong>
                @foreach($lamaran->training as $training)
                {{ $training->program->nama ?? 'Program tidak tersedia' }}
                @if(!$loop->last), @endif
                @endforeach
            </div>
            @endif

            @if($lamaran->dokumen)
            <div style="margin-top: 3px;">
                <strong>Status Dokumen:</strong> 
                <span class="status-badge status-{{ $lamaran->dokumen->status }}">
                    {{ $lamaran->dokumen->status_label }}
                </span>
            </div>
            @endif

            @if($lamaran->catatan)
            <div style="margin-top: 3px;">
                <strong>Catatan:</strong> {{ $lamaran->catatan }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Manajemen TKI</p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>