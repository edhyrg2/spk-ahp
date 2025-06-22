@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12 mx-auto">
    <div class="card shadow">
        <div class="card-header">
            <h5 class="modal-title text-gray-900">Pilih Kriteria untuk Menampilkan Hasil Perbandingan Alternatif</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('hasil-perbandingan-alternatif.index.admin') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
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
                <a href="{{ route('hasil-perbandingan-alternatif.show.admin', $kriteria->id) }}?periode={{ request('periode') }}"
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