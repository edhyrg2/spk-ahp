<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerbandinganAlternatif extends Model
{
    use HasFactory;
    protected $table = 'perbandingan_alternatif';
    protected $fillable = ['kriteria_id', 'alternatif1_id', 'alternatif2_id', 'nilai'];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function alternatif1()
    {
        return $this->belongsTo(Alternatif::class, 'alternatif1_id');
    }

    public function alternatif2()
    {
        return $this->belongsTo(Alternatif::class, 'alternatif2_id');
    }
}
