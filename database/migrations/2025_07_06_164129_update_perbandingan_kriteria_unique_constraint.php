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
        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            // Drop foreign key constraints terlebih dahulu
            try {
                $table->dropForeign(['kriteria1_id']);
                $table->dropForeign(['kriteria2_id']);
            } catch (Exception $e) {
                // Abaikan jika foreign key tidak ada
            }

            // Hapus unique constraint lama
            $table->dropUnique('perbandingan_kriteria_kriteria1_id_kriteria2_id_unique');

            // Tambah unique constraint baru dengan periode
            $table->unique(['kriteria1_id', 'kriteria2_id', 'periode'], 'perbandingan_kriteria_unique');

            // Buat ulang foreign key constraints
            $table->foreign('kriteria1_id')->references('id')->on('kriteria')->onDelete('cascade');
            $table->foreign('kriteria2_id')->references('id')->on('kriteria')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            // Drop foreign key constraints terlebih dahulu jika ada
            try {
                $table->dropForeign(['kriteria1_id']);
                $table->dropForeign(['kriteria2_id']);
            } catch (Exception $e) {
                // Foreign key mungkin tidak ada, abaikan error
            }

            // Hapus unique constraint yang baru
            $table->dropUnique('perbandingan_kriteria_unique');

            // Kembalikan unique constraint lama
            $table->unique(['kriteria1_id', 'kriteria2_id'], 'perbandingan_kriteria_kriteria1_id_kriteria2_id_unique');

            // Buat ulang foreign key constraints jika diperlukan
            try {
                $table->foreign('kriteria1_id')->references('id')->on('kriteria')->onDelete('cascade');
                $table->foreign('kriteria2_id')->references('id')->on('kriteria')->onDelete('cascade');
            } catch (Exception $e) {
                // Abaikan jika gagal membuat ulang
            }
        });
    }
};
