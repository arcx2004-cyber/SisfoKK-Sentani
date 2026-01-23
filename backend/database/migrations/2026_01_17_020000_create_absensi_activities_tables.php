<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Absensi Ekstrakurikuler
        Schema::create('absensi_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_ekskul_id')->constrained()->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H'); // Hadir, Sakit, Izin, Alpha
            $table->string('keterangan')->nullable();
            $table->timestamps();
            
            // Prevent duplicate attendance for same student in same activity
            $table->unique(['kegiatan_ekskul_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_ekskuls');
    }
};
