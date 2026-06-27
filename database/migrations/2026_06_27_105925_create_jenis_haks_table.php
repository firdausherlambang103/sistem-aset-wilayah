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
        // Migrasi Jenis Hak
        Schema::create('jenis_haks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hak'); // cth: Hak Milik, Hak Guna Bangunan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_haks');
    }
};
