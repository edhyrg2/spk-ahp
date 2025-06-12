@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Edit Kriteria</h5>
            <a href="{{ route('kriteria.index.admin') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('kriteria.update.admin', $kriteria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="periode" value="{{ request('periode') }}">
                <div class="form-group text-gray-900">
                    <label for="kode_kriteria">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control"
                        value="{{ old('kode_kriteria', $kriteria->kode_kriteria) }}" required>
                </div>
                <div class="form-group text-gray-900">
                    <label for="nama_kriteria">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control"
                        value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection