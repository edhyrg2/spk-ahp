<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\PerbandinganKriteria;
use App\Models\PerbandinganAlternatif;
use App\Models\Alternatif;
use App\Models\Periode;
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
    public function print($periode_id)
    {
        $periode = Periode::where('nama_periode', $periode_id)->first();
        $kriteria = Kriteria::where('periode', $periode_id)->get();
        $perbandingan = PerbandinganKriteria::where('periode', $periode_id)->get();
        $kriteriaIds = Kriteria::where('periode', $periode_id)->pluck('id')->toArray();
        $alternatif = Alternatif::where('periode', $periode_id)->get();
        $nilaiAkhir = [];
        
        // Calculate final scores
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

                    // Build matrix for this criteria
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
        
        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Admin.ranking-akhir.print', compact('alternatif', 'ranked', 'periode'));
        
        return $pdf->stream('hasil-pemilihan-alternatif.pdf');
    }

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
}
