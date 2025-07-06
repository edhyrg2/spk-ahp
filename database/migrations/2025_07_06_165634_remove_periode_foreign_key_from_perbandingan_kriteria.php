<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah index ada terlebih dahulu
        $indexExists = collect(DB::select("SHOW INDEX FROM perbandingan_kriteria WHERE Key_name = 'perbandingan_kriteria_periode_index'"))->isNotEmpty();
        
        Schema::table('perbandingan_kriteria', function (Blueprint $table) use ($indexExists) {
            // Hapus index hanya jika ada
            if ($indexExists) {
                $table->dropIndex('perbandingan_kriteria_periode_index');
            }
            
            // Pastikan kolom periode adalah string/varchar biasa
            $table->string('periode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            // Buat ulang index jika diperlukan
            $table->index('periode');
        });
    }
};
