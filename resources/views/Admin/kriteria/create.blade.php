@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Tambah Kriteria</h5>
            <a href="{{ route('kriteria.index.admin') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('kriteria.store.admin') }}" method="POST">
                @csrf
                <input type="hidden" name="periode" value="{{ request('periode') }}">
                <div class="form-group text-gray-900">
                    <label for="name">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" required>
                </div>
                <div class="form-group text-gray-900">
                    <label for="name">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection