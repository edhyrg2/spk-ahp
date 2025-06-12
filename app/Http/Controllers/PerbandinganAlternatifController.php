<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\PerbandinganAlternatif;

class PerbandinganAlternatifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periodes = Periode::all();
        $periodeId = $request->input('periode');
        if ($periodeId) {
            $kriteriaList = Kriteria::where('periode', $periodeId)->get();
        } else {
            $kriteriaList = Kriteria::where('periode', 0)->get();
        }
        return view('Admin.perbandingan-alternatif.index', compact('kriteriaList', 'periodes', 'periodeId'));
    }

    public function index_hasil(Request $request)
    {
        $periodes = Periode::all();
        $periodeId = $request->input('periode');
        if ($periodeId) {
            $kriteriaList = Kriteria::where('periode', $periodeId)->get();
        } else {
            $kriteriaList = Kriteria::where('periode', 0)->get();
        }

        return view('Admin.hasil-perbandingan-alternatif.index', compact('kriteriaList', 'periodes', 'periodeId'));
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
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nilai' => 'required|array',
        ]);

        $kriteria_id = $request->kriteria_id;
        $nilaiInput = $request->nilai;

        // Hapus data lama untuk kriteria ini
        PerbandinganAlternatif::where('kriteria_id', $kriteria_id)->delete();

        foreach ($nilaiInput as $alt1_id => $row) {
            foreach ($row as $alt2_id => $value) {
                $val = floatval($value);
                if ($val <= 0) continue; // Hindari pembagian dengan nol atau nilai aneh

                // Simpan nilai asli
                PerbandinganAlternatif::create([
                    'kriteria_id' => $kriteria_id,
                    'alternatif1_id' => $alt1_id,
                    'alternatif2_id' => $alt2_id,
                    'nilai' => $val,
                ]);

                // Simpan nilai kebalikan (reciprocal)
                PerbandinganAlternatif::create([
                    'kriteria_id' => $kriteria_id,
                    'alternatif1_id' => $alt2_id,
                    'alternatif2_id' => $alt1_id,
                    'nilai' => round(1 / $val, 4),
                ]);
            }
        }

        return redirect()->route('perbandingan-alternatif.index.admin')
            ->with('success', 'Perbandingan alternatif berhasil disimpan.');
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

    public function bandingkan(string $id)
    {
        $kriteria = Kriteria::find($id);

        if (!$kriteria) {
            return redirect()->route('perbandingan-alternatif.index')->with('error', 'Kriteria tidak ditemukan.');
        }

        $alternatif = Alternatif::all();

        return view('Admin.perbandingan-alternatif.bandingkan', compact('kriteria', 'alternatif'));
    }

    public function prosesAHP($kriteria_id)
    {
        $kriteria = Kriteria::findOrFail($kriteria_id);
        $alternatif = Alternatif::all();
        $altIds = $alternatif->pluck('id')->toArray();

        // Ambil data perbandingan dari DB
        $perbandingan = PerbandinganAlternatif::where('kriteria_id', $kriteria_id)->get();

        // Bangun matriks perbandingan
        $matrix = [];
        foreach ($altIds as $i) {
            foreach ($altIds as $j) {
                if ($i == $j) {
                    $matrix[$i][$j] = 1;
                } else {
                    // Ambil nilai langsung
                    $nilaiLangsung = $perbandingan->first(function ($item) use ($i, $j) {
                        return $item->alternatif1_id == $i && $item->alternatif2_id == $j;
                    });

                    // Jika tidak ada, ambil nilai kebalikannya
                    if ($nilaiLangsung) {
                        $matrix[$i][$j] = $nilaiLangsung->nilai;
                    } else {
                        $nilaiKebalikan = $perbandingan->first(function ($item) use ($i, $j) {
                            return $item->alternatif1_id == $j && $item->alternatif2_id == $i;
                        });

                        $matrix[$i][$j] = $nilaiKebalikan ? 1 / $nilaiKebalikan->nilai : 1;
                    }
                }
            }
        }

        // Lanjut ke perhitungan AHP
        $result = $this->calculateEigenVector($matrix, $altIds);

        // Ranking Alternatif
        $ranking = collect($result['eigen_vector'])
            ->sortDesc()
            ->map(function ($value, $key) use ($alternatif) {
                $alt = $alternatif->firstWhere('id', $key);
                return [
                    'nama' => $alt->nama_siswa,
                    'eigen' => $value,
                ];
            })
            ->values();

        return view('Admin.hasil-perbandingan-alternatif.hasil', [
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'matrix' => $result['matrix'],
            'normalized' => $result['normalized'],
            'eigen_vector' => $result['eigen_vector'],
            'lambda_max' => $result['lambda_max'],
            'ci' => $result['ci'],
            'cr' => $result['cr'],
            'ranking' => $ranking
        ]);
    }

    public function calculateEigenVector($matrix, $altIds)
    {
        $n = count($altIds);
        $columnSums = array_fill(0, $n, 0);

        // Hitung jumlah tiap kolom
        foreach ($altIds as $j => $colId) {
            foreach ($altIds as $i => $rowId) {
                $columnSums[$j] += $matrix[$rowId][$colId];
            }
        }

        // Normalisasi matriks dan hitung eigen vector
        $normalized = [];
        $eigen_vector = [];
        foreach ($altIds as $i => $rowId) {
            $rowTotal = 0;
            foreach ($altIds as $j => $colId) {
                $normalized[$rowId][$colId] = $matrix[$rowId][$colId] / $columnSums[$j];
                $rowTotal += $normalized[$rowId][$colId];
            }
            $eigen_vector[$rowId] = $rowTotal / $n;
        }

        // Hitung λ max
        $lambda_max = 0;
        foreach ($altIds as $i => $id) {
            $weightedSum = 0;
            foreach ($altIds as $j => $jId) {
                $weightedSum += $matrix[$id][$jId] * $eigen_vector[$jId];
            }
            $lambda_max += $weightedSum / $eigen_vector[$id];
        }

        $lambda_max = $lambda_max / $n;
        $ci = ($lambda_max - $n) / ($n - 1);
        $riTable = [1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45];
        $ri = $riTable[$n] ?? 1.49;
        $cr = $ri == 0 ? 0 : round($ci / $ri, 4);

        return [
            'matrix' => $matrix,
            'normalized' => $normalized,
            'eigen_vector' => $eigen_vector,
            'lambda_max' => round($lambda_max, 4),
            'ci' => round($ci, 4),
            'cr' => round($cr, 4),
        ];
    }
}
