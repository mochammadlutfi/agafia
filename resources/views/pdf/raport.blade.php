<html>

<head>
    <title>Raport {{ $data->anak->nama }} ({{ \Carbon\Carbon::parse($data->tgl)->format('d-m-Y') }})</title>

    <link rel="stylesheet" href="/css/bootstrap.css">
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
    <h2 style="text-align:center;font-size:16pt;font-weight: bold;">LAPORAN PERKEMBANGAN ANAK</h2>

    <div class="container">
        <br/>
        <br/>
        <table width="100%">
            <tr>
                <td width="37%">Kelompok</td>
                <td width="20px">:</td>
                <td>{{ $data->kelompok->nama }} ({{ $data->kelompok->usia}})</td>
            <tr>
            <tr>
                <td>Nama Anak</td>
                <td>:</td>
                <td>{{ $data->anak->nama }}</td>
            <tr>
            <tr>
                <td>Tinggi Badan / Berat Badan</td>
                <td>:</td>
                <td>{{ $data->tinggi }} CM / {{ $data->berat }} Kg</td>
            <tr>
            <tr>
                <td>Hadir</td>
                <td>:</td>
                <td>{{ $data->hadir }}</td>
            <tr>
                <tr>
                    <td>Izin</td>
                    <td>:</td>
                    <td>{{ $data->izin }}</td>
                <tr>
            <tr>
                <td>Sakit</td>
                <td>:</td>
                <td>{{ $data->sakit }}</td>
            <tr>
            <tr>
                <td>Tanpa Keterangan</td>
                <td>:</td>
                <td>{{ $data->alpa }}</td>
            <tr>
        </table>
        <br/>
        <br/>
        <table class="table table-bordered datatable w-100">
            <tbody>
                <tr class="">
                    <td style="font-weight:bold;font-size:12pt">Perkembangan Nilai Agama dan Moral</td>
                </tr>
                <tr>
                    <td>{{ $data->perkembangan_nilai_agama_dan_moral}}</td>
                </tr>
                <tr class="">
                    <td style="font-weight:bold;font-size:12pt">Perkembangan Sosial dan Emosional</td>
                </tr>
                <tr>
                    <td>{{ $data->perkembangan_sosial_emosional}}</td>
                </tr>
                <tr class="">
                    <td style="font-weight:bold;font-size:12pt">Perkembangan Fisik dan Motorik</td>
                </tr>
                <tr>
                    <td>{{ $data->perkembangan_fisik_motorik}}</td>
                </tr>
                <tr class="">
                    <td style="font-weight:bold;font-size:12pt">Perkembangan Kognitif</td>
                </tr>
                <tr>
                    <td>{{ $data->perkembangan_kognitif}}</td>
                </tr>
                <tr class="">
                    <td style="font-weight:bold;font-size:12pt">Perkembangan Bahasa</td>
                </tr>
                <tr>
                    <td>{{ $data->perkembangan_bahasa}}</td>
                </tr>
            </tbody>
        </table>
        <br/>
        <br/>
        <br/>
        <div style="text-align:right">
            Bandung, {{ \Carbon\Carbon::parse($data->tgl)->translatedFormat('d F Y') }}
        </div>
        <br/>
        <br/>
        <div style="text-align:center">
            <p>
                Guru Pendamping Khusus
                <br/>
                <br/>
                <br/>
                {{ $data->kelompok->admin->nama }}
            </p>
        </div>
        <br/>
        <br/>
        <div style="text-align:center">
            <p>
                Mengetahui
                <br/>
                Kepala Sekolah TKJT Baiturrahmah
                <br/>
                <br/>
                <br/>
                <br/>

            </p>
        </div>
    </div>

</body>

</html>