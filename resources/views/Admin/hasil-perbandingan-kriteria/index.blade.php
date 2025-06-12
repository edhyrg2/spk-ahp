@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12 my-4">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Matriks Perbandingan Kriteria</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('hasil-perbandingan-kriteria.index.admin') }}" class="mb-3">
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
            <h6 class="text-gray-900">Matriks Perbandingan</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            @foreach ($kriteriaIds as $colId)
                            <th>{{ is_object($colId) ? $colId->nama_kriteria : $kriteria->firstWhere('id', $colId)->nama_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteriaIds as $rowId)
                        <tr>
                            <th>{{ is_object($rowId) ? $rowId->nama_kriteria : $kriteria->firstWhere('id', $rowId)->nama_kriteria }}</th>
                            @foreach ($kriteriaIds as $colId)
                            <td>{{ number_format($matrix[$rowId][$colId], 4) }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <h6 class="text-gray-900">Matriks Normalisasi & Eigen Vector</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            @foreach ($kriteriaIds as $colId)
                            <th>{{ is_object($colId) ? $colId->nama_kriteria : $kriteria->firstWhere('id', $colId)->nama_kriteria }}</th>
                            @endforeach
                            <th>Priority Vector</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteriaIds as $rowId)
                        <tr>
                            <th>{{ is_object($rowId) ? $rowId->nama_kriteria : $kriteria->firstWhere('id', $rowId)->nama_kriteria }}</th>
                            @foreach ($kriteriaIds as $colId)
                            <td>{{ number_format($normalized[$rowId][$colId], 4) }}</td>
                            @endforeach
                            <td><strong>{{ number_format($eigen_vector[$rowId], 4) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <br>

            <h6 class="text-gray-900">Nilai Konsistensi</h6>
            <ul>
                <li class="text-gray-900"><strong class="text-gray-900">λ Max:</strong> {{ $lambda_max }}</li>
                <li class="text-gray-900"><strong class="text-gray-900">CI (Consistency Index):</strong> {{ $ci }}</li>
                <li class="text-gray-900"><strong class="text-gray-900">CR (Consistency Ratio):</strong> <span class="{{ $cr < 0.1 ? 'text-success' : 'text-danger' }}">{{ $cr }} / {{ number_format($cr * 100, 2) }}%</span></li>
            </ul>

            @if ($cr < 0.1)
                <div class="alert alert-success">Perbandingan konsisten ✅
        </div>
        @else
        <div class="alert alert-danger">Perbandingan tidak konsisten ❌ — silakan periksa kembali input</div>
        @endif

    </div>
</div>
</div>
@endsection