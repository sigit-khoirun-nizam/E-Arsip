<!DOCTYPE html>
<html>

<head>
    <title>Print Korin</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-title {
            text-decoration: underline;
        }

        .info-table {
            width: 100%;
            margin-top: 30px;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .info-table td:first-child {
            width: 60px;
        }

        .info-table td:nth-child(2) {
            width: 15px;
        }

        .content {
            margin-bottom: 40px;
            text-align: justify;
            line-height: 1.5;
        }

        .signature {
            margin-bottom: 50px;
            width: 250px;
            float: right;
        }

        .disposisi {
            margin-top: 50px;
            clear: both;
        }
        .disposisi-title {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .disposisi p {
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <strong class="header-title">KORESPONDENSI INTERN</strong><br>
        No. {{ $korin->nomor_surat }}
    </div>

    <table class="info-table">
        <tr>
            <td>Kepada</td>
            <td>:</td>
            <td>Pengurus KKUSB</td>
        </tr>
        <tr>
            <td>Dari</td>
            <td>:</td>
            <td>{{ $korin->unitPengirim->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>{{ $korin->perihal }}</td>
        </tr>
    </table>

    <div class="content">
        <p>Dengan hormat,</p>
        <div>
            {!! $korin->isi !!}
        </div>
        <p style="margin-top: 20px;">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    <div class="signature">
        Gresik, {{ \Carbon\Carbon::parse($korin->tanggal_surat)->translatedFormat('d F Y') }}<br>
        KopKar Usaha Sejahtera Bersama<br>
        <br><br><br><br>
        {{ $korin->pembuat->name ?? '-' }}<br>
        Unit {{ $korin->unitPengirim->name ?? '-' }}
    </div>

    <div class="disposisi">
        <strong class="disposisi-title">Disposisi Pengurus :</strong>
        <p>1. Ketua : {{ $disposisiKetua }}</p>
        <p>2. Bendahara : {{ $disposisiBendahara }}</p>
        <p>3. Sekertaris : {{ $disposisiSekretaris }}</p>
    </div>
</body>

</html>