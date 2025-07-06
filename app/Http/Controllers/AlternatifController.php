<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class AlternatifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periodes = Periode::all();
        $periodeId = $request->input('periode');

        if ($periodeId) {
            $alternatif = Alternatif::where('periode', $periodeId)->get();
        } else {
            // Jika tidak ada periode yang dipilih, ambil periode pertama atau kosong
            $alternatif = Alternatif::where('periode', '')->get();
        }

        return view('Admin.alternatif.index', compact('alternatif', 'periodes', 'periodeId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $periodes = Periode::all();
        $periodeId = $request->input('periode');
        
        return view('Admin.alternatif.create', compact('periodes', 'periodeId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $alternatifValidated = $request->validate([
            'wilayah' => 'required',
            'alamat' => 'required',
            'periode' => 'required|string',
            'pilih' => 'nullable|string',
        ]);
        
        // Set default value untuk pilih jika tidak ada
        if (!isset($alternatifValidated['pilih']) || empty($alternatifValidated['pilih'])) {
            $alternatifValidated['pilih'] = 'Tidak Dipilih';
        }
        
        // Debug: uncomment untuk melihat data yang akan disimpan
        // dd($alternatifValidated);
        
        try {
            DB::beginTransaction();

            $alternatif = Alternatif::create($alternatifValidated);

            DB::commit();
            return redirect()->route('alternatif.index.admin', ['periode' => $alternatifValidated['periode']])
                ->with('success', 'Data alternatif berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            // Debug: uncomment untuk melihat error detail
            // dd($th->getMessage());
            return redirect()->route('alternatif.index.admin', ['periode' => $request->input('periode')])
                ->with('error', 'Data alternatif gagal ditambahkan: ' . $th->getMessage());
        }
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
        $alternatif = Alternatif::find($id);
        return view('Admin.alternatif.edit', compact('alternatif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $alternatifValidated = $request->validate([
            'wilayah' => 'required',
            'alamat' => 'required',
            'periode' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $alternatif = Alternatif::find($id);
            $alternatif->update($alternatifValidated);

            DB::commit();
            return redirect()->route('alternatif.index.admin', ['periode' => $alternatifValidated['periode']])
                ->with('success', 'Data alternatif berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('alternatif.index.admin', ['periode' => $request->input('periode')])
                ->with('error', 'Data alternatif gagal diupdate');
        }
    }
    public function updateDipilih(Request $request, $alt_id)
    {
        try {
            DB::beginTransaction();

            $alternatif = Alternatif::findOrFail($alt_id);
            $alternatif->pilih = 'Dipilih';
            $alternatif->save();

            $periode = $alternatif->periode;

            DB::commit();
            return redirect()->route('ranking-akhir.index.admin', ['periode' => $periode])
                ->with('success', 'Alternatif berhasil diupdate.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('ranking-akhir.index.admin')
                ->with('error', 'Alternatif gagal diupdate.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $alternatif = Alternatif::find($id);
            $periode = $alternatif->periode;
            $alternatif->delete();

            DB::commit();
            return redirect()->route('alternatif.index.admin', ['periode' => $periode])
                ->with('success', 'Data alternatif berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('alternatif.index.admin', ['periode' => $periode ?? null])
                ->with('error', 'Data alternatif gagal dihapus');
        }
    }
}
