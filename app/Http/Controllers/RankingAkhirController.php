<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\PerbandinganKriteria;
use App\Models\PerbandinganAlternatif;
use App\Models\Alternatif;
use App\Models\Periode;
use PhpOffice\PhpWord\TemplateProcessor;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;
use PDF;

class RankingAkhirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periodes = \App\Models\Periode::all();
        $periode_id = request('periode');
        $kriteria = Kriteria::where('periode', $periode_id)->get();
        $perbandingan = PerbandinganKriteria::where('periode', $periode_id)->get();
        $kriteriaIds = Kriteria::where('periode', $periode_id)->pluck('id')->toArray();
        $alternatif = Alternatif::where('periode', $periode_id)->get();
        $sudahAdaTerpilih = Alternatif::where('periode', $periode_id)
            ->where('pilih', 'Dipilih') // ganti 'is_selected' sesuai field di tabel
            ->exists();
        $nilaiAkhir = [];

        // Jika tidak ada periode atau data, tampilkan 0 untuk semua alternatif
        if (!$periode_id || $kriteria->isEmpty() || $alternatif->isEmpty() || $perbandingan->isEmpty()) {
            foreach ($alternatif as $alt) {
                $nilaiAkhir[$alt->id] = 0;
            }
        } else {
            $matrix_kriteria = [];

            foreach ($kriteriaIds as $rowId) {
                foreach ($kriteriaIds as $colId) {
                    $nilai = $perbandingan->where('kriteria1_id', $rowId)->where('kriteria2_id', $colId)->first();
                    $matrix_kriteria[$rowId][$colId] = $nilai ? $nilai->nilai : 1;
                }
            }

            $eigen_kriteria = $this->calculateEigenVector($matrix_kriteria, $kriteriaIds);
            $bobotKriteria = $eigen_kriteria['eigen_vector'];

            foreach ($alternatif as $alt) {
                $total = 0;
                foreach ($kriteria as $k) {
                    $rel = PerbandinganAlternatif::where('kriteria_id', $k->id)->get();
                    $altIds = $alternatif->pluck('id')->toArray();

                    // Bangun matriks alternatif untuk kriteria ini
                    $matrix = [];
                    foreach ($altIds as $i) {
                        foreach ($altIds as $j) {
                            if ($i == $j) {
                                $matrix[$i][$j] = 1;
                            } else {
                                // Ambil nilai langsung
                                $nilaiLangsung = $rel->first(function ($item) use ($i, $j) {
                                    return $item->alternatif1_id == $i && $item->alternatif2_id == $j;
                                });

                                // Jika tidak ada, ambil nilai kebalikannya
                                if ($nilaiLangsung) {
                                    $matrix[$i][$j] = $nilaiLangsung->nilai;
                                } else {
                                    $nilaiKebalikan = $rel->first(function ($item) use ($i, $j) {
                                        return $item->alternatif1_id == $j && $item->alternatif2_id == $i;
                                    });

                                    $matrix[$i][$j] = $nilaiKebalikan ? 1 / $nilaiKebalikan->nilai : 1;
                                }
                            }
                        }
                    }

                    $eigenAlt = $this->calculateEigenVector($matrix, $altIds);
                    $bobotAlternatif = $eigenAlt['eigen_vector'];

                    $total += $bobotKriteria[$k->id] * ($bobotAlternatif[$alt->id] ?? 0);
                }

                $nilaiAkhir[$alt->id] = $total;
            }
        }

        return view('Admin.ranking-akhir.index', compact('alternatif', 'nilaiAkhir', 'periodes', 'periode_id', 'sudahAdaTerpilih'));
    }

    /**
     * Generate PDF for printing
     */
    // public function print($periode_id)
    // {
    //     $periode = Periode::where('nama_periode', $periode_id)->first();
    //     $kriteria = Kriteria::where('periode', $periode_id)->get();
    //     $perbandingan = PerbandinganKriteria::where('periode', $periode_id)->get();
    //     $kriteriaIds = Kriteria::where('periode', $periode_id)->pluck('id')->toArray();
    //     $alternatif = Alternatif::where('periode', $periode_id)->get();
    //     $nilaiAkhir = [];

    //     // Calculate final scores
    //     if ($kriteria->isEmpty() || $alternatif->isEmpty() || $perbandingan->isEmpty()) {
    //         foreach ($alternatif as $alt) {
    //             $nilaiAkhir[$alt->id] = 0;
    //         }
    //     } else {
    //         $matrix_kriteria = [];

    //         foreach ($kriteriaIds as $rowId) {
    //             foreach ($kriteriaIds as $colId) {
    //                 $nilai = $perbandingan->where('kriteria1_id', $rowId)->where('kriteria2_id', $colId)->first();
    //                 $matrix_kriteria[$rowId][$colId] = $nilai ? $nilai->nilai : 1;
    //             }
    //         }

    //         $eigen_kriteria = $this->calculateEigenVector($matrix_kriteria, $kriteriaIds);
    //         $bobotKriteria = $eigen_kriteria['eigen_vector'];

    //         foreach ($alternatif as $alt) {
    //             $total = 0;
    //             foreach ($kriteria as $k) {
    //                 $rel = PerbandinganAlternatif::where('kriteria_id', $k->id)->get();
    //                 $altIds = $alternatif->pluck('id')->toArray();

    //                 // Build matrix for this criteria
    //                 $matrix = [];
    //                 foreach ($altIds as $i) {
    //                     foreach ($altIds as $j) {
    //                         if ($i == $j) {
    //                             $matrix[$i][$j] = 1;
    //                         } else {
    //                             $nilaiLangsung = $rel->first(function ($item) use ($i, $j) {
    //                                 return $item->alternatif1_id == $i && $item->alternatif2_id == $j;
    //                             });

    //                             if ($nilaiLangsung) {
    //                                 $matrix[$i][$j] = $nilaiLangsung->nilai;
    //                             } else {
    //                                 $nilaiKebalikan = $rel->first(function ($item) use ($i, $j) {
    //                                     return $item->alternatif1_id == $j && $item->alternatif2_id == $i;
    //                                 });

    //                                 $matrix[$i][$j] = $nilaiKebalikan ? 1 / $nilaiKebalikan->nilai : 1;
    //                             }
    //                         }
    //                     }
    //                 }

    //                 $eigenAlt = $this->calculateEigenVector($matrix, $altIds);
    //                 $bobotAlternatif = $eigenAlt['eigen_vector'];

    //                 $total += $bobotKriteria[$k->id] * ($bobotAlternatif[$alt->id] ?? 0);
    //             }

    //             $nilaiAkhir[$alt->id] = $total;
    //         }
    //     }

    //     $ranked = collect($nilaiAkhir)->sortDesc();

    //     // Generate PDF
    //     $pdf = app('dompdf.wrapper');
    //     $pdf->loadView('Admin.ranking-akhir.print', compact('alternatif', 'ranked', 'periode'));

    //     return $pdf->stream('hasil-pemilihan-alternatif.pdf');
    // }

    function calculateEigenVector(array $matrix, array $kriteriaIds): array
    {
        $n = count($kriteriaIds);
        $columnSums = [];

        // Calculate column sums
        foreach ($kriteriaIds as $j) {
            $columnSums[$j] = 0;
            foreach ($kriteriaIds as $i) {
                $columnSums[$j] += $matrix[$i][$j];
            }
        }

        // Normalize and calculate eigen vector
        $normalized = [];
        $eigen_vector = [];

        foreach ($kriteriaIds as $i) {
            $sumRow = 0;
            foreach ($kriteriaIds as $j) {
                $normalized[$i][$j] = $matrix[$i][$j] / $columnSums[$j];
                $sumRow += $normalized[$i][$j];
            }
            $eigen_vector[$i] = round($sumRow / $n, 4);
        }

        return [
            'normalized' => $normalized,
            'eigen_vector' => $eigen_vector
        ];
    }

    public function print(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $periode = $request->input('periode');
        $periodeObj = \App\Models\Periode::where('nama_periode', $periode)->first();
        $alternatif = \App\Models\Alternatif::where('periode', $periode)->get();

        // Hitung ranking akhir
        $kriteria = Kriteria::where('periode', $periode)->get();
        $perbandingan = PerbandinganKriteria::where('periode', $periode)->get();
        $kriteriaIds = Kriteria::where('periode', $periode)->pluck('id')->toArray();
        $nilaiAkhir = [];

        if ($kriteria->isEmpty() || $alternatif->isEmpty() || $perbandingan->isEmpty()) {
            foreach ($alternatif as $alt) {
                $nilaiAkhir[$alt->id] = 0;
            }
        } else {
            $matrix_kriteria = [];
            foreach ($kriteriaIds as $rowId) {
                foreach ($kriteriaIds as $colId) {
                    $nilai = $perbandingan->where('kriteria1_id', $rowId)->where('kriteria2_id', $colId)->first();
                    $matrix_kriteria[$rowId][$colId] = $nilai ? $nilai->nilai : 1;
                }
            }
            $eigen_kriteria = $this->calculateEigenVector($matrix_kriteria, $kriteriaIds);
            $bobotKriteria = $eigen_kriteria['eigen_vector'];
            foreach ($alternatif as $alt) {
                $total = 0;
                foreach ($kriteria as $k) {
                    $rel = PerbandinganAlternatif::where('kriteria_id', $k->id)->get();
                    $altIds = $alternatif->pluck('id')->toArray();
                    $matrix = [];
                    foreach ($altIds as $i) {
                        foreach ($altIds as $j) {
                            if ($i == $j) {
                                $matrix[$i][$j] = 1;
                            } else {
                                $nilaiLangsung = $rel->first(function ($item) use ($i, $j) {
                                    return $item->alternatif1_id == $i && $item->alternatif2_id == $j;
                                });
                                if ($nilaiLangsung) {
                                    $matrix[$i][$j] = $nilaiLangsung->nilai;
                                } else {
                                    $nilaiKebalikan = $rel->first(function ($item) use ($i, $j) {
                                        return $item->alternatif1_id == $j && $item->alternatif2_id == $i;
                                    });
                                    $matrix[$i][$j] = $nilaiKebalikan ? 1 / $nilaiKebalikan->nilai : 1;
                                }
                            }
                        }
                    }
                    $eigenAlt = $this->calculateEigenVector($matrix, $altIds);
                    $bobotAlternatif = $eigenAlt['eigen_vector'];
                    $total += $bobotKriteria[$k->id] * ($bobotAlternatif[$alt->id] ?? 0);
                }
                $nilaiAkhir[$alt->id] = $total;
            }
        }
        $ranked = collect($nilaiAkhir)->sortDesc();

        // Generate HTML sesuai format gambar
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Hasil Pengambilan Keputusan AHP</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 10px; }
                .header h2 { font-size: 22px; font-weight: bold; margin-bottom: 0; }
                hr { margin: 20px 0; }
                .table-wrap { margin-top: 40px; }
                table { width: 80%; margin: 0 auto 30px auto; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 8px; text-align: center; }
                th { background: #fff; font-weight: bold; }
                .footer { margin-top: 80px; }
                .signature { float: right; width: 250px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Laporan Hasil Pengambilan Keputusan Dengan Metode Analytical Hierarchy Process (AHP) Lokasi Toko Strategis Terbaik Pada Artolouis</h2>
                <hr>
            </div>
            <div class="table-wrap">
                <h3 style="text-align:center; margin-bottom:20px;">Rangking Akhir Alternatif</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Wilayah</th>
                            <th>Skor Akhir</th>
                            <th>Peringkat</th>
                        </tr>
                    </thead>
                    <tbody>';
        $sorted = $alternatif->sortByDesc(function ($alt) use ($ranked) {
            return $ranked[$alt->id] ?? 0;
        })->values();
        foreach ($sorted as $i => $alt) {
            $html .= '<tr>';
            $html .= '<td>' . $alt->wilayah . '</td>';
            $html .= '<td>' . number_format($ranked[$alt->id] ?? 0, 4) . '</td>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';
        $html .= '<div class="footer"><div class="signature"><br><br><br><p>Mengetahui<br>Founder Artolouis</p><br><br><br><p style="margin-top:40px;">Aldous Lukito</p></div></div>';
        $html .= '</body></html>';

        // Generate PDF dengan DOMPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Laporan_AHP_' . $periode . '.pdf');
    }

    public function printWord(Request $request)
    {
        $periode = $request->input('periode');

        // Ambil alternatif terpilih
        $alternatif = \App\Models\Alternatif::where('periode', $periode)->where('pilih', 'Dipilih')->first();

        if (!$alternatif) {
            return back()->with('error', 'Belum ada alternatif terpilih.');
        }

        $templatePath = storage_path('app/template_surat.docx');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template Word tidak ditemukan di: ' . $templatePath);
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);

            // Set value placeholder di template Word
            $templateProcessor->setValue('wilayah', $alternatif->wilayah);
            $templateProcessor->setValue('alamat', $alternatif->alamat);
            $templateProcessor->setValue('periode', $periode);
            $templateProcessor->setValue('tanggal', date('d F Y'));
            $templateProcessor->setValue('nomor', sprintf('%03d/AHP/%s', rand(1, 999), date('Y')));

            // Simpan file hasil
            $outputPath = storage_path('app/temp/surat_hasil_ranking_' . time() . '.docx');
            $templateProcessor->saveAs($outputPath);

            // Download file
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat generate Word: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function printPdfSimple(Request $request)
    {
        $periode = $request->input('periode');

        // Ambil alternatif terpilih
        $alternatif = \App\Models\Alternatif::where('periode', $periode)->where('pilih', 'Dipilih')->first();

        if (!$alternatif) {
            return back()->with('error', 'Belum ada alternatif terpilih.');
        }

        // Data untuk PDF
        $data = [
            'wilayah' => $alternatif->wilayah,
            'alamat' => $alternatif->alamat,
            'periode' => $periode,
            'tanggal' => date('d F Y'),
            'nomor' => sprintf('%03d/AHP/%s', rand(1, 999), date('Y'))
        ];

        // HTML template sederhana
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Surat Hasil Ranking</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }
                .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 30px; padding-bottom: 20px; }
                .content { margin: 30px 0; }
                .info { margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>SURAT HASIL PEMILIHAN LOKASI</h2>
                <p>ANALYTIC HIERARCHY PROCESS (AHP)</p>
            </div>
            
            <div class="content">
                <div class="info">
                    <p><strong>Nomor:</strong> ' . $data['nomor'] . '</p>
                    <p><strong>Tanggal:</strong> ' . $data['tanggal'] . '</p>
                </div>
                
                <p>Berdasarkan hasil analisis menggunakan metode AHP periode <strong>' . $data['periode'] . '</strong>, 
                wilayah yang terpilih adalah:</p>
                
                <div class="info">
                    <p><strong>Wilayah:</strong> ' . $data['wilayah'] . '</p>
                    <p><strong>Alamat:</strong> ' . $data['alamat'] . '</p>
                    <p><strong>Periode:</strong> ' . $data['periode'] . '</p>
                </div>
                
                <p>Demikian surat ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
            </div>
            
            <div style="margin-top: 50px; text-align: right;">
                <p>Kepala Dinas</p>
                <br><br><br>
                <p><strong>_______________________</strong></p>
            </div>
        </body>
        </html>';

        // Generate PDF dengan DOMPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Surat_Hasil_Ranking_' . $periode . '.pdf');
    }
}
