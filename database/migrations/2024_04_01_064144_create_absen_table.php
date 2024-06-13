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
        Schema::create('absen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id');
            $table->text('lokasi_user');
            $table->string('waktu_absen_masuk', length: 100);
            $table->string('waktu_absen_pulang', length: 100)->nullable();
            $table->string('tanggal_hari_ini', length: 100);
            $table->string('status', length: 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen');
    }
};
