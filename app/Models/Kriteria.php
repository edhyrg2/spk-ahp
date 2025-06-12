<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'periode',
    ];

    public function perbandinganSebagaiKriteria1()
    {
        return $this->hasMany(PerbandinganKriteria::class, 'kriteria1_id');
    }

    public function perbandinganSebagaiKriteria2()
    {
        return $this->hasMany(PerbandinganKriteria::class, 'kriteria2_id');
    }

    public function perbandingan()
    {
        return $this->hasMany(PerbandinganAlternatif::class, 'kriteria_id');
    }
}
