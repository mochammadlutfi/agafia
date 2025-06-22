<html>

<head>
    <title>Kartu SPP</title>

    <link rel="stylesheet" href="/css/bootstrap.css">

    <style>
        .table-santri {
            width: 100%;
            border: 1px solid black;
            padding: 5px;
            margin-bottom: 10px;
        }

        .table-santri .judul {
            font-size: 6pt;
            width: 40%;
        }

        .table-santri .value {
            font-size: 6pt;
            border-bottom: 1px dotted black;
        }

        .table-spp {
            width: 100%;
            border-collapse: collapse;
        }

        .table-spp td, .table-spp th {
            font-size: 7pt;
            padding: 3px;
            border: 1px solid black;
        }

        .table-spp th {
            font-weight: bold;
        }

    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td width="20%" class="text-center">
                <img src="/images/logo/logo.png" width="70px">
            </td>
            <td class="text-center">
                <h2 style="margin-bottom: 5px;font-size:12pt;font-weight: bold;">KARTU SPP</h2>
                <h2 style="margin-bottom: 5px;font-size:8pt;font-weight: bold;">TEMPAT PENITIPAN ANAK (TPA)/DAYCARE</h2>
                <h2 style="margin-bottom: 5px;font-size:12pt;font-weight: bold;">BAITURRAHMAH</h2>
                <p style="font-size:7pt;font-weight:bold;margin-bottom:0px">
                    Jl. Awi Gombong No 14, Cicadas - Bandung 40121<br>
                    Telp 022. 20544338 - 0878.6887.9678
                </p>
            </td>
        </tr>
    </table>
    <hr style="margin-top: 0px;margin-bottom:5px"/>
    <table class="table-santri">
        <tr style="padding-top: 10px;">
            <td class="judul">NAMA SANTRI</td>
            <td width="5px">:</td>
            <td class="value">{{ $anak->nama }}</td>
        </tr>
        <tr>
            <td class="judul">TEMPAT / TANGGAL LAHIR</td>
            <td width="5px">:</td>
            <td class="value">{{ $anak->tmp_lahir }} / {{ \Carbon\Carbon::parse($anak->tgl_lahir)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="judul">ALAMAT</td>
            <td width="5px">:</td>
            <td class="value">{{ $anak->alamat }}</td>
        </tr>
        <tr>
            <td class="judul">NAMA ORANG TUA / WALI</td>
            <td width="5px">:</td>
            <td class="value">{{ $anak->user->detail->nama_ibu }}</td>
        </tr>
    </table>
    <table class="table-spp">
        <tr>
            <td width="5px;">NO</td>
            <td>BULAN</td>
            <td>NO INVOICE</td>
            <td>PEMBAYARAN</td>
            <td>JUMLAH</td>
        </tr>
        @foreach ($data as $report)
            <tr>
                <td rowspan="{{ count($report['invoices']) }}">{{ $loop->index+1 }}</td>
                <td>{{ $report['month'] }}</td>
                @foreach ($report['invoices'] as $index => $invoice)
                    @if($index > 0) <tr> @endif
                        <td>
                            {{ $invoice['nomor'] }}
                        </td>
                        <td>{{ $invoice['metode'] }}</td>
                        <td>Rp {{ $invoice['total'] != '-' ? number_format($invoice['total'],0,',','.') : '-' }}</td>
                        {{-- <td>{{ $invoice['metode'] }}</td> --}}
                    </tr>
                @endforeach
            @endforeach
    </table>
</body>

</html>