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
            // Ubah tipe data periode dari BIGINT ke VARCHAR
            $table->string('periode', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatif', function (Blueprint $table) {
            $table->bigInteger('periode')->nullable()->change();
        });
    }
};
