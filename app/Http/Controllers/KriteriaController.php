<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;


class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periodes = Periode::all();
        $periodeId = $request->input('periode');

        if ($periodeId) {
            $kriteria = Kriteria::where('periode', $periodeId)->get();
        } else {
            $kriteria = Kriteria::where('periode', 0)->get();
        }

        return view('Admin.kriteria.index', compact('kriteria', 'periodes', 'periodeId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.kriteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $kriteriaValidated = $request->validate([
            'kode_kriteria' => 'required',
            'nama_kriteria' => 'required',
            'periode' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $kriteria = Kriteria::create($kriteriaValidated);

            DB::commit();
            return redirect()->route('kriteria.index.admin', ['periode' => $kriteria->periode])->with('success', 'Data kriteria berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('kriteria.index.admin', ['periode' => $request->input('periode')])->with('error', 'Data kriteria gagal ditambahkan');
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
        $kriteria = Kriteria::find($id);
        return view('Admin.kriteria.edit', compact('kriteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $kriteriaValidated = $request->validate([
            'kode_kriteria' => 'required',
            'nama_kriteria' => 'required',
            'periode' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $kriteria = Kriteria::find($id);
            $kriteria->update($kriteriaValidated);

            DB::commit();
            return redirect()->route('kriteria.index.admin', ['periode' => $kriteria->periode])->with('success', 'Data kriteria berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('kriteria.index.admin', ['periode' => $request->input('periode')])->with('error', 'Data kriteria gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $kriteria = Kriteria::find($id);
            $periode = $kriteria ? $kriteria->periode : request()->input('periode');
            if ($kriteria) {
                $kriteria->delete();
            }

            DB::commit();
            return redirect()->route('kriteria.index.admin', ['periode' => $periode])->with('success', 'Data kriteria berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('kriteria.index.admin', ['periode' => $periode ?? request()->input('periode')])->with('error', 'Data kriteria gagal dihapus');
        }
    }
}
