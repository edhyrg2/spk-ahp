@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Data Alternatif</h5>
            <a
                href="{{ route('alternatif.create.admin', ['periode' => request('periode')]) }}"
                class="btn btn-primary {{ !request('periode') ? 'disabled' : '' }}"
                @if(!request('periode')) onclick="return false;" @endif>
                <i class="fas fa-plus"></i> Tambah Alternatif
            </a>
        </div>


        <div class="card-body">
            <form method="GET" action="{{ route('alternatif.index.admin') }}" class="mb-3">
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
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Wilayah</th>
                        <th>Alamat</th>
                        <th>Periode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($alternatif) > 0)
                    @foreach ($alternatif as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $a->wilayah }}</td>
                        <td>{{ $a->alamat }}</td>
                        <td>{{ $a->periode }}</td>
                        <td>
                            <a
                                href="{{ route('alternatif.edit.admin', [$a->id, 'periode' => request('periode')]) }}"
                                class="btn btn-warning {{ !request('periode') ? 'disabled' : '' }}"
                                @if(!request('periode')) onclick="return false;" @endif>
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('alternatif.destroy.admin',$a->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <button
                                    type="submit"
                                    class="btn btn-danger {{ !request('periode') ? 'disabled' : '' }}"
                                    @if(!request('periode')) onclick="return false;" @else onclick="return confirm('Apakah anda yakin untuk menghapus data ini?')" @endif>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data alternatif atau pilih periode terlebih dahulu</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection