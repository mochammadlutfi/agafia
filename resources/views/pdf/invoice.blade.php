<html>

<head>
    <title>Invoice {{ $data->nomor }}</title>

    <link rel="stylesheet" href="/css/bootstrap.css">
    <style>
        .table.table-transparent td,
        .table.table-transparent th {
            background-color: transparent !important;
        }
    </style>
</head>
<body>
    <div class="container" style="background: url({{ $data->status == 'paid' ? '/images/paid.png' : '/images/unpaid.png' }}) no-repeat center center`">
        <table width="100%">
            <tr>
                <td width="20%" class="text-center">
                    <img src="/images/logo/logo.png" width="120pt">
                </td>
                <td class="text-right width" width="50%">
                    <h1 style="margin-bottom: 5px;font-size:16pt;font-weight: 600;">INVOICE</h1>
                    <table width="400px">
                        <tr>
                            <td>
                                Referensi
                            </td>
                            <td style="text-align: right;">
                                {{ $data->nomor}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal
                            </td>
                            <td style="text-align: right;">
                                {{ \Carbon\Carbon::parse($data->tgl)->translatedFormat('d F Y')}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <table width="100%">
            <tr>
                <td style="border-bottom: 1px solid black" width="40%">
                    Dari
                </td>
                <td></td>
                <td style="border-bottom: 1px solid black" width="40%">
                    Tagihan untuk
                </td>
            </tr>
            <tr>
                <td>
                    <h4 style="font-size: 12pt;font-weight:bold;">Daycare Baiturrahman</h4>
                    <br/>
                    <p style="font-size:10pt">
                        Jl. Awigombong No.14 RT.05/RW.05 <br/>
                        Cicadas, Kec. Cibeunying Kidul<br/>
                        Kota Bandung, Jawa Barat 40121, Indonesia
                        <br/>
                        Telp : 02220544338<br/>
                    </p>
                </td>
                <td></td>
                <td>
                    <h4 style="font-size: 12pt;font-weight:bold;">{{$data->user->nama}}</h4>
                    <br/>
                    <p style="font-size:10pt">
                        {{$data->user->detail->alamat_ayah}}
                        <br/>
                        Email : {{$data->user->email}}
                    </p>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <table class="table v-align-center table-transparent table-bordered w-100">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Keterangan</th>
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->detail as $d)
                <tr>
                    <td>{{ $loop->index+1 }}</td>
                    <td>{{ $d->tipe }}</td>
                    <td>Rp {{ number_format($d->harga,0,',','.') }}</td>
                    <td>{{ $d->qty }}</td>
                    <td>Rp {{ number_format($d->harga*$d->qty,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Total</td>
                    <td>Rp. {{ number_format($data->total,0,',','.') }}</td>
                </tr>
            </tfoot>
        </table>
        <br/>
    </div>

</body>

</html>