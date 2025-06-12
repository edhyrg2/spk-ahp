<!-- resources/views/perbandingan-alternatif/pilih-kriteria.blade.php -->
@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12 mx-auto">
    <div class="card shadow">
        <div class="card-header">
            <h5 class="modal-title text-gray-900">Pilih Kriteria untuk Perbandingan Alternatif</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('perbandingan-alternatif.index.admin') }}" class="mb-4">
                <div class="form-group row align-items-center">
                    <label class="col-md-2 col-form-label font-weight-bold text-gray-900">Pilih Periode</label>
                    <div class="col-md-6">
                        <select name="periode" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode)
                            <option value="{{ $periode->nama_periode }}" {{ request('periode') == $periode->nama_periode ? 'selected' : '' }}>
                                {{ $periode->nama_periode }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
            <div class="list-group">
                @foreach($kriteriaList as $kriteria)
                <a href="{{ route('perbandingan-alternatif.bandingkan.admin', $kriteria->id) }}?periode={{ request('periode') }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    {{ $kriteria->nama_kriteria }}
                    <span class="badge bg-primary rounded-pill">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection