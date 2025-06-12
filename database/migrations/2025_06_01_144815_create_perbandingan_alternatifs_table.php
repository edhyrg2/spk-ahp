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
        Schema::create('perbandingan_alternatif', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kriteria_id');
            $table->unsignedBigInteger('alternatif1_id');
            $table->unsignedBigInteger('alternatif2_id');
            $table->decimal('nilai', 8, 2);
            $table->timestamps();

            $table->foreign('kriteria_id')->references('id')->on('kriteria');
            $table->foreign('alternatif1_id')->references('id')->on('alternatif');
            $table->foreign('alternatif2_id')->references('id')->on('alternatif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbandingan_alternatif');
    }
};
