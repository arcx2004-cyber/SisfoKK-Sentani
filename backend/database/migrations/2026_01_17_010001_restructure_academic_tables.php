<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Model Penilaian (Assessment Model Types)
        Schema::create('model_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Sumatif Harian, STS, SAS, Kenaikan Kelas
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Profil Lulusan (8 Dimensions Kurikulum 2025)
        Schema::create('profil_lulusans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "Beriman dan Bertakwa kepada Tuhan YME"
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Kokurikuler (Co-curricular Activities)
        Schema::create('kokurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pembimbing Kokurikuler (Coach/Mentor)
        Schema::create('pembimbing_kokurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kokurikuler_id')->constrained()->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nama_pembimbing'); // Could be external
            $table->string('no_telepon')->nullable();
            $table->timestamps();
        });

        // Anggota Kokurikuler (Students in Co-curricular)
        Schema::create('anggota_kokurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kokurikuler_id')->constrained()->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['kokurikuler_id', 'siswa_id', 'semester_id'], 'anggota_kokurikuler_unique');
        });

        // Nilai Kokurikuler
        Schema::create('nilai_kokurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_kokurikuler_id')->constrained('anggota_kokurikulers')->onDelete('cascade');
            $table->enum('grade', ['A', 'B', 'C', 'D']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Update mata_pelajarans - add jenis column
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->enum('jenis', ['wajib', 'muatan_lokal'])->default('wajib')->after('deskripsi');
        });

        // Update siswas - add nik column
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nik')->nullable()->unique()->after('nisn');
        });

        // Update capaian_pembelajarans - add unit_id and fase
        Schema::table('capaian_pembelajarans', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id');
            $table->enum('fase', ['A', 'B', 'C', 'D'])->nullable()->after('unit_id');
        });

        // Add foreign key constraint for capaian_pembelajarans after column creation
        Schema::table('capaian_pembelajarans', function (Blueprint $table) {
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Remove columns from capaian_pembelajarans
        Schema::table('capaian_pembelajarans', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'fase']);
        });

        // Remove columns from siswas
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('nik');
        });

        // Remove columns from mata_pelajarans
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });

        // Drop new tables
        Schema::dropIfExists('nilai_kokurikulers');
        Schema::dropIfExists('anggota_kokurikulers');
        Schema::dropIfExists('pembimbing_kokurikulers');
        Schema::dropIfExists('kokurikulers');
        Schema::dropIfExists('profil_lulusans');
        Schema::dropIfExists('model_penilaians');
    }
};
