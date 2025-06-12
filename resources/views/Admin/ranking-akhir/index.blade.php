@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12 my-4">
    <div class="card shadow">
        <div class="card-header">
            <h5 class="text-gray-900">Hasil Akhir Pemilihan Alternatif</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ranking-akhir.index.admin') }}" class="mb-4">
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
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Wilayah</th>
                        <th>Skor Akhir</th>
                        <th>Terpilih</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ranked = collect($nilaiAkhir)->sortDesc();
                    $peringkat = 1;
                    @endphp
                    @foreach ($ranked as $alt_id => $skor)
                    <tr>
                        <td>{{ $peringkat++ }}</td>
                        <td>{{ $alternatif->find($alt_id)->wilayah }}<br>{{ $alternatif->find($alt_id)->alamat }}</td>
                        <td>{{ number_format($skor, 4) }}</td>
                        @if (empty($sudahAdaTerpilih))
                        <td>
                            <form action="{{ route('alternatif.updateDipilih', $alt_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm">Pilih</button>
                            </form>
                        </td>
                        @else
                        <td>{{ $alternatif->find($alt_id)->pilih}}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection