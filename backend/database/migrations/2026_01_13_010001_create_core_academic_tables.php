<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add extra columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('avatar_url');
        });

        // Tahun Ajaran (Academic Year)
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "2024/2025"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Semester
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained()->onDelete('cascade');
            $table->enum('tipe', ['ganjil', 'genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Unit (TK, SD, SMP)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // TK, SD, SMP
            $table->string('kode')->unique(); // TK, SD, SMP
            $table->text('deskripsi')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Jabatan (Position)
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // PTK, Tendik, Kepsek
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_teaching')->default(false); // PTK = true, Tendik = false
            $table->timestamps();
        });

        // Ruang Kelas (Classroom)
        Schema::create('ruang_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('nama'); // Kelas 1A, 2B, etc.
            $table->string('kode')->unique();
            $table->integer('kapasitas')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Mata Pelajaran (Subject)
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('ruang_kelas');
        Schema::dropIfExists('jabatans');
        Schema::dropIfExists('units');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('tahun_ajarans');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'is_active']);
        });
    }
};
