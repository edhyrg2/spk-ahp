@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">Tambah Wilayah</h5>
            <a href="{{ route('alternatif.index.admin') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('alternatif.store.admin') }}" method="POST">
                @csrf
                
                <!-- Dropdown Periode -->
                <div class="form-group text-gray-900">
                    <label for="periode">Periode</label>
                    <select name="periode" class="form-control" required>
                        <option value="">Pilih Periode</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->nama_periode }}" {{ $periodeId == $periode->nama_periode ? 'selected' : '' }}>
                                {{ $periode->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group text-gray-900">
                    <label for="wilayah">Wilayah</label>
                    <input type="text" name="wilayah" class="form-control" required>
                </div>
                <div class="form-group text-gray-900">
                    <label for="alamat">Alamat</label>
                    <input type="text" name="alamat" class="form-control" required>
                </div>
                
                <!-- Hidden field untuk pilih dengan default value -->
                <input type="hidden" name="pilih" value="Tidak Dipilih">
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection