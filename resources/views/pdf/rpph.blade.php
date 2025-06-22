<html>

<head>
    <title>RPPH_TOPIK_{{ ucwords($data->subtopik) }}</title>

    <link rel="stylesheet" href="/css/bootstrap.css">
    <style>
        body {
            /* font-family: 'Time' */
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td width="20%" class="text-center">
                <img src="/images/logo/logo.png" width="100px">
            </td>
            <td class="text-center" width="50%">
                <h1 style="margin-bottom: 5px;font-size:28pt;font-weight: bold;">DAYCARE BAITURRAHMAH</h1>
                <p>
                    Jl. Awi Gombong No 11 Rt 01 Rw 07  Kel. Cicadas Kec.Cibeunying Kidul<br/>
                    Kota Bandung, Jawa Barat 40121
                </p>
            </td>
        </tr>
    </table>
    <hr style="border-width: 10px"/>
    <h2 style="text-align:center;font-size:12pt;font-weight: bold;">RENCANA PELAKSANAAN PEMBELAJARAN (RPP) TPA BAITURRAHMAH</h2>
    <h2 style="text-align:center;font-size:12pt;font-weight: bold;">TAHUN AJARAN 2024-2025</h2>

    <div class="container">
        <br/>
        <br/>
        <table width="100%">
            <tr>
                <td width="150px">Kelompok / Usia</td>
                <td width="20px">:</td>
                <td>{{ $data->kelompok->nama }} / {{ $data->kelompok->usia}} </td>
            <tr>
                <tr>
                    <td width="150px">Topik / Subtopik</td>
                    <td width="20px">:</td>
                    <td>{{ $data->topik }} / {{ $data->subtopik}} </td>
                <tr>
            <tr>
                <td>Durasi</td>
                <td>:</td>
                <td>{{ $data->durasi }}</td>
            <tr>
        </table>
        <br/>
        <br/>
        <table class="table table-bordered datatable w-100">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Tujuan Pembelajaran</th>
                    <th>Perilaku yang teramati/IKTP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->detail as $l)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $l->tujuan }}</td>
                        <td>{{ $l->iktp }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
        <h3>Asesmen Awal</h3>
        <br/>
        <table class="table table-bordered datatable w-100">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Kegiatan</th>
                    <th>Hasil Pengamatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->asesmen_awal as $l)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $l->kegiatan }}</td>
                        <td>{{ $l->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
        <h3>Kegiatan Pembelajaran</h3>
        <ol>
            @foreach ($data->kegiatan as $d)
                <li>{{ $d }}</li>
            @endforeach
        </ol>
        <br/>
        <h3>Asesmen Sumatif</h3>
        <table class="table table-bordered datatable w-100">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Perilaku yang teramati/IKTP</th>
                    <th>Muncul/Tidak</th>
                    <th>Hasil Pengamatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->asesmen_sumatif as $l)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $l->line->iktp }}</td>
                        <td>{{ $l->status }}</td>
                        <td>{{ $l->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
        <br/>
        <table class="w-100">
            <tr>
                <td width="34%" style="text-align: left">Mengetahui,</td>
                <td width="33%" style="text-align: center"></td>
                <td width="33%" style="text-align: right">
                    Bandung, {{ \Carbon\Carbon::parse($data->tgl)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center">Kepala Sekolah TPA Baiturrahmah</td>
                <td style="text-align: center">Guru Pendamping</td>
                <td style="text-align: center">
                    Guru Kelompok {{ $data->kelompok->nama }}
                </td>
            </tr>
            <tr>
                <td><br/><br/><br/><br/><br/><br/></td>
                <td><br/><br/><br/><br/><br/><br/></td>
                <td><br/><br/><br/><br/><br/><br/></td>
            </tr>
            <tr>
                <td style="text-align: center">Annisa Nadashafa. S.Psi</td>
                <td style="text-align: center">Fitriah</td>
                <td style="text-align: center">{{ $data->kelompok->admin->nama }}</td>
            </tr>
        </table>
    </div>

</body>

</html>