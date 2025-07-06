<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\PerbandinganKriteria;

class PerbandinganKriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periodes = \App\Models\Periode::all();
        $periode_id = request('periode');

        if ($periode_id) {
            $kriteria = Kriteria::where('periode', $periode_id)->get();
            $perbandingan = PerbandinganKriteria::where('periode', $periode_id)->get();
        } else {
            $kriteria = Kriteria::where('periode', 0)->get();
            $perbandingan = PerbandinganKriteria::where('periode', 0)->get();
        }

        return view('Admin.perbandingan-kriteria.index', compact('periodes', 'kriteria', 'perbandingan'));
    }

    public function hitungHasilPerbandingan()
    {
        $periodes = \App\Models\Periode::all();
        $periode = request('periode');

        // Ambil kriteria sesuai periode, jika tidak ada periode, kosongkan array
        $kriteria = $periode ? Kriteria::where('periode', $periode)->get() : collect();
        $kriteriaIds = $kriteria->pluck('id')->toArray();

        // Ambil perbandingan sesuai periode, jika tidak ada periode, kosongkan collection
        $perbandingan = $periode ? PerbandinganKriteria::where('periode', $periode)->get() : collect();

        $n = count($kriteriaIds);
        $matrix  = [];

        // Jika ada kriteria, isi matrix dari data, jika tidak, isi matrix 0
        if ($n > 0 && $perbandingan->count() > 0) {
            foreach ($kriteriaIds as $rowId) {
                foreach ($kriteriaIds as $colId) {
                    $item = $perbandingan->where('kriteria1_id', $rowId)->where('kriteria2_id', $colId)->first();
                    $matrix[$rowId][$colId] = $item ? $item->nilai : 0;
                }
            }
            $eigenResult = $this->calculateEigenVector($matrix, $kriteriaIds);
            $normalized = $eigenResult['normalized'];
            $eigen_vector = $eigenResult['eigen_vector'];

            $consistencyResult = $this->calculateConsistencyRatio($matrix, $eigen_vector, $kriteriaIds);

            $lambda_max = $consistencyResult['lambda_max'];
            $ci = $consistencyResult['ci'];
            $cr = $consistencyResult['cr'];
        } else {
            // Jika tidak ada data, isi semua matrix, eigen_vector, normalized, dll dengan 0
            foreach ($kriteriaIds as $rowId) {
                foreach ($kriteriaIds as $colId) {
                    $matrix[$rowId][$colId] = 0;
                }
            }
            $normalized = [];
            $eigen_vector = [];
            foreach ($kriteriaIds as $id) {
                $eigen_vector[$id] = 0;
                $normalized[$id] = array_fill_keys($kriteriaIds, 0);
            }
            $lambda_max = 0;
            $ci = 0;
            $cr = 0;
        }

        return view('Admin.hasil-perbandingan-kriteria.index', compact(
            'kriteria',
            'kriteriaIds',
            'periodes',
            'matrix',
            'normalized',
            'eigen_vector',
            'lambda_max',
            'ci',
            'cr'
        ));
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
        // Validasi input
        $request->validate([
            'periode' => 'required',
            'arah' => 'required|array',
            'nilai' => 'required|array',
        ]);

        $periodes = Periode::all();
        $periode = $request->input('periode');
        $arahInput = $request->input('arah');   // Array: arah[A][B]
        $nilaiInput = $request->input('nilai'); // Array: nilai[A][B]

        // Debug untuk cek apakah periode diterima
        if (!$periode) {
            return redirect()->route('perbandingan-kriteria.index.admin')
                ->with('error', 'Periode tidak ditemukan. Silakan pilih periode terlebih dahulu.');
        }

        // dd($arahInput, $nilaiInput, $periode); // Uncomment untuk debug

        // $kriteriaIds = array_merge(
        //     array_keys($arahInput),
        //     array_keys($nilaiInput)
        // );

        // Ambil kriteria sesuai periode yang dipilih
        $kriteriaIds = Kriteria::where('periode', $periode)->pluck('id')->toArray();
        $n = count($kriteriaIds);

        if (empty($kriteriaIds)) {
            return redirect()->route('perbandingan-kriteria.index.admin', ['periode' => $periode])
                ->with('error', 'Tidak ada kriteria pada periode yang dipilih.');
        }

        $matrix = $this->buildComparisonMatrix($kriteriaIds, $nilaiInput, $arahInput);

        // Hapus data lama untuk periode ini terlebih dahulu untuk menghindari conflict
        PerbandinganKriteria::where('periode', $periode)
            ->whereIn('kriteria1_id', $kriteriaIds)
            ->whereIn('kriteria2_id', $kriteriaIds)
            ->delete();

        foreach ($kriteriaIds as $id1) {
            foreach ($kriteriaIds as $id2) {
                if ($id1 == $id2) continue; // Lewati data diagonal

                $nilaiFinal = $matrix[$id1][$id2];
                PerbandinganKriteria::create([
                    'kriteria1_id' => $id1,
                    'kriteria2_id' => $id2,
                    'periode' => $periode,
                    'nilai' => $nilaiFinal,
                ]);
            }
        }

        $kriteriaList = Kriteria::whereIn('id', $kriteriaIds)->pluck('nama_kriteria', 'id')->toArray();
        $kriteria = Kriteria::where('periode', $periode)->get();
        $perbandingan = PerbandinganKriteria::where('periode', $periode)->get();

        return redirect()->route('perbandingan-kriteria.index.admin', ['periode' => $periode])
            ->with('success', 'Perbandingan kriteria berhasil disimpan.');
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

    function buildComparisonMatrix(array $kriteriaIds, array $nilaiInput, array $arahInput): array
    {
        $matrix = [];

        foreach ($kriteriaIds as $id1) {
            foreach ($kriteriaIds as $id2) {
                if ($id1 == $id2) {
                    $matrix[$id1][$id2] = 1.0; // Diagonal = 1
                    continue;
                }

                // Check if comparison exists in A→B or B→A
                $nilai = null;
                $arah = null;

                if (isset($nilaiInput[$id1][$id2]) && isset($arahInput[$id1][$id2])) {
                    $nilai = (float)$nilaiInput[$id1][$id2];
                    $arah = $arahInput[$id1][$id2];
                } elseif (isset($nilaiInput[$id2][$id1]) && isset($arahInput[$id2][$id1])) {
                    $nilai = (float)$nilaiInput[$id2][$id1];
                    $arah = $arahInput[$id2][$id1] == 'AB' ? 'BA' : 'AB';
                }

                // If no data, throw error
                if ($nilai === null || $arah === null) {
                    throw new \Exception("Missing comparison for {$id1} vs {$id2}");
                }

                // Build matrix based on direction
                if ($arah === 'AB') {
                    $matrix[$id1][$id2] = (float)$nilai;
                    $matrix[$id2][$id1] = round(1 / (float)$nilai, 4);
                } else { // BA
                    $matrix[$id2][$id1] = (float)$nilai;
                    $matrix[$id1][$id2] = round(1 / (float)$nilai, 4);
                }
            }
        }

        return $matrix;
    }

    /**
     * Calculates the eigen vector from a comparison matrix
     * 
     * @param array $matrix The comparison matrix
     * @param array $kriteriaIds Array of criteria IDs
     * @return array Array with 'normalized' matrix and 'eigen_vector' values
     */
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
     * Calculates consistency ratio (CR) for AHP
     * 
     * @param array $matrix The comparison matrix
     * @param array $eigen_vector The eigen vector values
     * @param array $kriteriaIds Array of criteria IDs
     * @return array Array with 'lambda_max', 'ci', 'cr' values
     */
    function calculateConsistencyRatio(array $matrix, array $eigen_vector, array $kriteriaIds): array
    {
        $n = count($kriteriaIds);

        // Calculate lambda max
        $lambda_max = 0;
        foreach ($kriteriaIds as $i) {
            $weightedSum = 0;
            foreach ($kriteriaIds as $j) {
                $weightedSum += $matrix[$i][$j] * $eigen_vector[$j];
            }
            $lambda_max += $weightedSum / $eigen_vector[$i];
        }
        $lambda_max = $lambda_max / $n;

        // Calculate CI & CR
        $ci = ($lambda_max - $n) / ($n - 1);
        $riTable = [0.00, 0.00, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        $ri = $riTable[$n - 1] ?? 1.49;
        $cr = ($ri == 0) ? 0 : $ci / $ri;

        return [
            'lambda_max' => $lambda_max,
            'ci' => $ci,
            'cr' => $cr
        ];
    }
}
