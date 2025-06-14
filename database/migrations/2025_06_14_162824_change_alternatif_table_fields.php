<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alternatif', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn(['nis', 'nama_siswa', 'kelas', 'jenis_kelamin']);
            
            // Add the new columns
            $table->string('wilayah');
            $table->string('alamat');
            // Note: 'periode' column already exists from a previous migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatif', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn(['wilayah', 'alamat']);
            
            // Restore the old columns
            $table->string('nis');
            $table->string('nama_siswa');
            $table->string('kelas');
            $table->string('jenis_kelamin');
        });
    }
};
