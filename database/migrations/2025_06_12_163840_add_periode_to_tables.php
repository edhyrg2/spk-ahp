<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodeToTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alternatif', function (Blueprint $table) {
            $table->unsignedBigInteger('periode')->nullable()->after('id');
        });

        Schema::table('kriteria', function (Blueprint $table) {
            $table->unsignedBigInteger('periode')->nullable()->after('id');
        });

        Schema::table('perbandingan_alternatif', function (Blueprint $table) {
            $table->unsignedBigInteger('periode')->nullable()->after('id');
        });

        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            $table->unsignedBigInteger('periode')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatif', function (Blueprint $table) {
            $table->dropColumn('periode');
        });

        Schema::table('kriteria', function (Blueprint $table) {
            $table->dropColumn('periode');
        });

        Schema::table('perbandingan_alternatif', function (Blueprint $table) {
            $table->dropColumn('periode');
        });

        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
}
