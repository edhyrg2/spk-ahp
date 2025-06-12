@extends('layouts.adminLayout')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="text-primary">Hasil Perbandingan Alternatif - {{ $kriteria->nama_kriteria }}</h5>
    </div>
    <div class="card-body">

        <h6 class="text-gray-900">Matriks Perbandingan</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($alternatif as $alt)
                        <th>{{ $alt->nama_siswa }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatif as $row)
                    <tr>
                        <th>{{ $row->nama_siswa }}</th>
                        @foreach($alternatif as $col)
                        <td>{{ number_format($matrix[$row->id][$col->id], 4) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h6 class="text-gray-900">Matriks Normalisasi & Eigen Vector</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($alternatif as $alt)
                        <th>{{ $alt->nama_siswa }}</th>
                        @endforeach
                        <th>Priority Vector</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatif as $row)
                    <tr>
                        <th>{{ $row->nama_siswa }}</th>
                        @foreach($alternatif as $col)
                        <td>{{ number_format($normalized[$row->id][$col->id], 4) }}</td>
                        @endforeach
                        <td class="font-weight-bold text-success">{{ number_format($eigen_vector[$row->id], 4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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

    <a href="{{ route('hasil-perbandingan-alternatif.index.admin') }}" class="btn btn-secondary">Kembali</a>
</div>
</div>
@endsection