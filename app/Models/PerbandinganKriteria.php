<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerbandinganKriteria extends Model
{
    use HasFactory;

    protected $table = 'perbandingan_kriteria';
    
    protected $fillable = [
        'kriteria1_id',
        'kriteria2_id',
        'nilai'
    ];

    protected $casts = [
        'nilai' => 'float'
    ];

    
    public function kriteria1()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria1_id');
    }


    public function kriteria2()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria2_id');
    }

    public static function bandingkan($kriteria1_id, $kriteria2_id, $nilai)
    {

        $perbandingan = self::where([
            'kriteria1_id' => $kriteria1_id,
            'kriteria2_id' => $kriteria2_id
        ])->first();
        if ($perbandingan) {
            $perbandingan->update(['nilai' => $nilai]);
        } else {
            $perbandingan = self::create([
                'kriteria1_id' => $kriteria1_id,
                'kriteria2_id' => $kriteria2_id,
                'nilai' => $nilai
            ]);
        }

        // Otomatis buat/mirror inverse value (jika A vs B = 3, maka B vs A = 1/3)
        $inverse = self::where([
            'kriteria1_id' => $kriteria2_id,
            'kriteria2_id' => $kriteria1_id
        ])->first();

        if (!$inverse) {
            self::create([
                'kriteria1_id' => $kriteria2_id,
                'kriteria2_id' => $kriteria1_id,
                'nilai' => $nilai != 0 ? 1 / $nilai : 0
            ]);
        }

        return $perbandingan;
    }
}