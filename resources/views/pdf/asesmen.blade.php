<html>

<head>
    <title>ASESMEN_TOPIK_{{ ucwords($data->subtopik) }}</title>

    <link rel="stylesheet" href="/css/bootstrap.css">
    <style>
        body {
            /* font-family: 'Time' */
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="text-align:center;font-size:48pt;font-weight: bold;"><br/><br/><br/>
            LEMBAR<br/>
            ASESMEN
        </h2>
        <table class="table table-bordered datatable w-100">
            <tbody>
                <tr>
                    <td width="30%">Satuan Pendidikan</td>
                    <td>TPA BAITURRAHMAH</td>
                </tr>
                <tr>
                    <td width="30%">Kelompok / Usia</td>
                    <td>{{ $data->kelompok->nama }} / {{ $data->kelompok->usia}} </td>
                </tr>
                <tr>
                    <td width="30%">Topik / Subtopik</td>
                    <td>{{ $data->topik }} / {{ $data->subtopik}} </td>
                </tr>
                <tr>
                    <td>Fase</td>
                    <td>{{ $data->fase }}</td>
                <tr>
                <tr>
                    <td>Durasi</td>
                    <td>{{ $data->durasi }}</td>
                </tr>
                <tr>
                    <td>Jumlah Murid</td>
                    <td>{{ $data->kelompok->anak->count() }} Anak</td>
                </tr>
                <tr>
                    <td>Guru</td>
                    <td>{{ $data->kelompok->admin->nama }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <pagebreak />
    <div class="container">
        <h2 style="text-align:center;font-size:12pt;font-weight: bold;">
            ASESMEN CHECKLIST<br/>
            TOPIK/SUBTOPIk : {{ $data->topik}} / {{ $data->subtopik}}<br/>
            DURASI : {{ $data->durasi }} Pertemuan
        </h2>
        <table class="table table-bordered datatable w-100">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA ANAK</th>
                    <th>PERILAKU YANG TERAMATI (IKTP)</th>  
                </tr>
            </thead>
            <tbody>
                @foreach ($data->asesmen as $d)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $d->anak->nama }}</td>
                        <td>
                            <ul>
                                @foreach ($d->nilai as $n)
                                <li style="margin-bottom:10px;">
                                    <span style="font-weight: bold">{{ $n->line->klasifikasi }}</span>
                                    <p style="font-weight: bold">TP : {{ $n->line->tujuan }}</p>
                                    <p style="font-weight: bold">IKTP : {{ $n->line->iktp }}</p>
                                    <p style="font-weight: normal">Anekdot : <br/>{{ $n->keterangan }}<br/></p>
                                </li>
                                @endforeach
                            </ul>
                            <p style="font-weight: bold">Dokumentasi:</p>
                            <br/>
                            @foreach ($d->foto as $f)
                                <img src="{{ $f->path }}" style="width: 100px"/>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
{{-- 
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
    </div> --}}

</body>

</html>