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
        Schema::create('perbandingan_kriteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kriteria1_id');
            $table->unsignedBigInteger('kriteria2_id');
            $table->decimal('nilai', 8, 2);
            
            $table->foreign('kriteria1_id')->references('id')->on('kriteria');
            $table->foreign('kriteria2_id')->references('id')->on('kriteria');
            
            $table->unique(['kriteria1_id', 'kriteria2_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbandingan_kriteria');
    }
};
