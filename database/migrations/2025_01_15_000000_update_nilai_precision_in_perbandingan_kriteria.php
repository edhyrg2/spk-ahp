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
            $table->decimal('nilai', 15, 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbandingan_kriteria', function (Blueprint $table) {
            $table->decimal('nilai', 8, 2)->change();
        });
    }
};