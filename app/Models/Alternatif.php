<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alternatif extends Model
{
    use HasFactory;

    protected $table = 'alternatif';

    protected $fillable = [
        'wilayah',
        'alamat',
        'periode',
    ];

    public function perbandingan()
    {
        return $this->hasMany(PerbandinganAlternatif::class, 'alternatif1_id');
    }

    public function perbandingan2()
    {
        return $this->hasMany(PerbandinganAlternatif::class, 'alternatif2_id');
    }
}
