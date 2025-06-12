@extends('layouts.adminLayout')

{{-- Pastikan Anda sudah memuat Font Awesome di layout utama Anda --}}
{{-- Contoh: <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" /> --}}

@push('styles')
<style>
    .dashboard-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .dashboard-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kriteria</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ session('kriteria', 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-check fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Alternatif</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ session('alternatif', 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pengguna</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ session('userCount', 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Anda bisa menambahkan konten lain di sini, misalnya chart atau tabel --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Selamat Datang!</h6>
                </div>
                <div class="card-body">
                    <p>Gunakan panel navigasi di sebelah kiri untuk mengelola data kriteria, alternatif, dan memulai proses perhitungan untuk sistem pendukung keputusan Anda. </p>
                    <p class="mb-0">Jika ada pertanyaan, jangan ragu untuk menghubungi administrator.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection