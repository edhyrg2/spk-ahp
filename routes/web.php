<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PerbandinganKriteriaController;
use App\Http\Controllers\PerbandinganAlternatifController;
use App\Http\Controllers\RankingAkhirController;


Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AuthController::class, 'dashboard'])->name('dashboard.admin');

    // KRITERIA
    Route::get('/admin/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index.admin');
    Route::get('/admin/kriteria/create', [KriteriaController::class, 'create'])->name('kriteria.create.admin');
    Route::post('/admin/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store.admin');
    Route::get('/admin/kriteria/{kriteria}/edit', [KriteriaController::class, 'edit'])->name('kriteria.edit.admin');
    Route::put('/admin/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update.admin');
    Route::delete('/admin/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy.admin');

    // ALTERNATIF
    Route::get('/admin/alternatif', [AlternatifController::class, 'index'])->name('alternatif.index.admin');
    Route::get('/admin/alternatif/create', [AlternatifController::class, 'create'])->name('alternatif.create.admin');
    Route::post('/admin/alternatif', [AlternatifController::class, 'store'])->name('alternatif.store.admin');
    Route::patch('/admin/alternatif/updateDipilih/{alt_id}', [AlternatifController::class, 'updateDipilih'])->name('alternatif.updateDipilih');
    Route::get('/admin/alternatif/{alternatif}/edit', [AlternatifController::class, 'edit'])->name('alternatif.edit.admin');
    Route::put('/admin/alternatif/{alternatif}', [AlternatifController::class, 'update'])->name('alternatif.update.admin');
    Route::delete('/admin/alternatif/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy.admin');

    //PERBANDINGAN KRITERIA
    Route::get('/admin/perbandingan-kriteria', [PerbandinganKriteriaController::class, 'index'])->name('perbandingan-kriteria.index.admin');
    Route::post('/admin/perbandingan-kriteria', [PerbandinganKriteriaController::class, 'store'])->name('perbandingan-kriteria.store.admin');

    //HASIL PERBANDINGAN KRITERIA
    Route::get('/admin/hasil-perbandingan-kriteria', [PerbandinganKriteriaController::class, 'hitungHasilPerbandingan'])->name('hasil-perbandingan-kriteria.index.admin');

    //PERBANDINGAN ALTERNATIF
    Route::get('/admin/perbandingan-alternatif', [PerbandinganAlternatifController::class, 'index'])->name('perbandingan-alternatif.index.admin');
    Route::get('/admin/perbandingan-alternatif/bandingkan/{id}', [PerbandinganAlternatifController::class, 'bandingkan'])->name('perbandingan-alternatif.bandingkan.admin');
    Route::post('/admin/perbandingan-alternatif', [PerbandinganAlternatifController::class, 'store'])->name('perbandingan-alternatif.store.admin');

    //HASIL PERBANDINGAN ALTERNATIF
    Route::get('/admin/hasil-perbandingan-alternatif', [PerbandinganAlternatifController::class, 'index_hasil'])->name('hasil-perbandingan-alternatif.index.admin');
    Route::get('/admin/hasil-perbandingan-alternatif/{id}', [PerbandinganAlternatifController::class, 'prosesAHP'])->name('hasil-perbandingan-alternatif.show.admin');

    //RANKING AKHIR
    Route::get('/admin/ranking-akhir', [RankingAkhirController::class, 'index'])->name('ranking-akhir.index.admin');
    Route::get('/admin/ranking-akhir/print/{periode}', [RankingAkhirController::class, 'print'])->name('ranking-akhir.print');
    // Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admin/ranking-akhir/print', [RankingAkhirController::class, 'print'])->name('ranking-akhir.print');
});

Route::middleware(['web', 'auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.user');
    // Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
