<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Hasil Pemilihan Alternatif</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin-bottom: 5px;
        }

        .header p {
            margin-top: 0;
        }

        .content {
            margin-bottom: 30px;
        }

        .letter {
            text-align: justify;
            margin-bottom: 30px;
        }

        .selected-alt {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 50px;
        }

        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="font-size:22px; font-weight:bold; margin-bottom:0;">Laporan Hasil Pengambilan Keputusan Dengan Metode Analytical Hierarchy Process (AHP) Lokasi Toko Strategis Terbaik Pada Artolouis</h2>
        <hr style="margin:20px 0;">
    </div>

    <div class="content">
        <h3 style="text-align:center; margin-bottom:20px;">Rangking Akhir Alternatif</h3>
        <table style="width:80%; margin:0 auto 30px auto; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="border:1px solid #000; padding:8px;">Wilayah</th>
                    <th style="border:1px solid #000; padding:8px;">Skor Akhir</th>
                    <th style="border:1px solid #000; padding:8px;">Peringkat</th>
                </tr>
            </thead>
            <tbody>
                @php
                $sorted = collect($alternatif)->sortByDesc(function($alt) use ($ranked) {
                return $ranked[$alt->id] ?? 0;
                })->values();
                @endphp
                @foreach ($sorted as $i => $alt)
                <tr>
                    <td style="border:1px solid #000; padding:8px;">{{ $alt->wilayah }}</td>
                    <td style="border:1px solid #000; padding:8px; text-align:center;">{{ number_format($ranked[$alt->id] ?? 0, 4) }}</td>
                    <td style="border:1px solid #000; padding:8px; text-align:center;">{{ $i+1 }}</td>
                </tr>
                @endforeach
                @for ($j = count($sorted); $j < 6; $j++)
                    <tr>
                    <td style="border:1px solid #000; padding:8px;">&nbsp;</td>
                    <td style="border:1px solid #000; padding:8px;">&nbsp;</td>
                    <td style="border:1px solid #000; padding:8px;">&nbsp;</td>
                    </tr>
                    @endfor
            </tbody>
        </table>
    </div>

    <div class="footer" style="margin-top:80px;">
        <div style="float:right; width:250px; text-align:center;">
            <p>Mengetahui<br>Founder Artolouis</p>
            <div style="margin-top:80px;"></div>
            <p style="margin-top:40px;">Aldous Lukito</p>
        </div>
    </div>
</body>

</html>