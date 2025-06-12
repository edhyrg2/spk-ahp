@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Data Kriteria</h5>
            <a href="{{ route('kriteria.create.admin', ['periode' => request('periode')]) }}"
                class="btn btn-primary {{ !request('periode') ? 'disabled' : '' }}">
                <i class="fas fa-plus"></i> Tambah Kriteria
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('kriteria.index.admin') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <select name="periode" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode)
                            <option value="{{ $periode->nama_periode }}" {{ request('periode') == $periode->id ? 'selected' : '' }}>
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
                        <th>No</th>
                        <th>Kode Kriteria</th>
                        <th>Kriteria</th>
                        <th>Periode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($kriteria) > 0)
                    @foreach ($kriteria as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k->kode_kriteria }}</td>
                        <td>{{ $k->nama_kriteria }}</td>
                        <td>{{ $k->periode }}</td>
                        <td>
                            <a href="{{ route('kriteria.edit.admin', ['kriteria' => $k->id, 'periode' => request('periode')]) }}"
                                class="btn btn-warning {{ !request('periode') ? 'disabled' : '' }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kriteria.destroy.admin',$k->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger {{ !request('periode') ? 'disabled' : '' }}"
                                    onclick="return confirm('Apakah anda yakin untuk menghapus project ini?')"
                                    {{ !request('periode') ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data kriteria atau Pilih Periode Terlebih dahulu</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection