<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_periode' => '2025'],
            ['nama_periode' => '2026'],
            ['nama_periode' => '2027'],
            ['nama_periode' => '2028'],
        ];

        DB::table('periodes')->insert($data);
    }
}
