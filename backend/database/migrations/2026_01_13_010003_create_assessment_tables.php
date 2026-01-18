<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Capaian Pembelajaran (Learning Achievement)
        Schema::create('capaian_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->string('kode');
            $table->text('deskripsi');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tujuan Pembelajaran (Learning Objective)
        Schema::create('tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capaian_pembelajaran_id')->constrained()->onDelete('cascade');
            $table->string('kode');
            $table->text('deskripsi');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Nilai Siswa per CP (Student Grade per CP)
        Schema::create('nilai_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('capaian_pembelajaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'capaian_pembelajaran_id', 'semester_id'], 'nilai_siswa_unique');
        });

        // Nilai Akhir Semester (Final Semester Grade)
        Schema::create('nilai_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('deskripsi_capaian')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'mata_pelajaran_id', 'semester_id'], 'nilai_akhir_unique');
        });

        // Nilai Spiritual
        Schema::create('nilai_spirituals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('grade', ['A', 'B', 'C', 'D']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });

        // Nilai Sosial
        Schema::create('nilai_sosials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('grade', ['A', 'B', 'C', 'D']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });

        // Data Tubuh (Physical Data)
        Schema::create('data_tubuhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->decimal('tinggi_badan', 5, 2)->nullable(); // in cm
            $table->decimal('berat_badan', 5, 2)->nullable(); // in kg
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });

        // Catatan Akhir Semester
        Schema::create('catatan_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->text('nilai_clc')->nullable(); // Deskripsi CLC
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_akhirs');
        Schema::dropIfExists('data_tubuhs');
        Schema::dropIfExists('nilai_sosials');
        Schema::dropIfExists('nilai_spirituals');
        Schema::dropIfExists('nilai_akhirs');
        Schema::dropIfExists('nilai_siswas');
        Schema::dropIfExists('tujuan_pembelajarans');
        Schema::dropIfExists('capaian_pembelajarans');
    }
};
