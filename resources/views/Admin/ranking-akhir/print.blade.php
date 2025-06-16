<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
        <h2>HASIL PEMILIHAN ALTERNATIF</h2>
        <p>Periode: {{ $periode->nama_periode }}</p>
    </div>
    
    <div class="content">
        <div class="letter">
            <p>Berdasarkan hasil perhitungan dengan metode AHP (Analytical Hierarchy Process) yang telah dilakukan, 
            kami menyampaikan bahwa alternatif yang terpilih untuk periode {{ $periode->nama_periode }} adalah:</p>
            
            @php
                $terpilih = null;
                foreach ($alternatif as $alt) {
                    if ($alt->pilih == 'Dipilih') {
                        $terpilih = $alt;
                        break;
                    }
                }
            @endphp
            
            @if ($terpilih)
            <div class="selected-alt">
                <p><strong>Wilayah:</strong> {{ $terpilih->wilayah }}</p>
                <p><strong>Alamat:</strong> {{ $terpilih->alamat }}</p>
                <p><strong>Skor Akhir:</strong> {{ number_format($ranked[$terpilih->id] ?? 0, 4) }}</p>
            </div>
            
            <p>Alternatif ini telah dipilih berdasarkan kriteria-kriteria yang telah ditetapkan dan 
            memiliki nilai tertinggi dalam perhitungan AHP.</p>
            @else
            <p>Belum ada alternatif yang dipilih untuk periode ini.</p>
            @endif
        </div>
    </div>
    
    <div class="footer">
        <div class="signature">
            <p>Tanggal: {{ date('d/m/Y') }}</p>
            <div class="signature-line"></div>
            <p>Penanggung Jawab</p>
        </div>
    </div>
</body>
</html>