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
        // 1. Projects P5
        Schema::create('projek_p5s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            
            $table->string('tema'); // e.g. "Gaya Hidup Berkelanjutan"
            $table->string('judul'); // e.g. "Sampahku Tanggung Jawabku"
            $table->text('deskripsi')->nullable();
            $table->enum('fase', ['A', 'B', 'C', 'D']);
            $table->timestamps();
        });

        // 2. Project Dimensions (Linking Project -> Profil Lulusan)
        Schema::create('projek_p5_dimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projek_p5_id')->constrained('projek_p5s')->cascadeOnDelete();
            $table->foreignId('profil_lulusan_id')->constrained('profil_lulusans')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Project Assessment (Nilai per Dimensi)
        Schema::create('nilai_projek_p5s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('projek_p5_id')->constrained('projek_p5s')->cascadeOnDelete();
            $table->foreignId('profil_lulusan_id')->constrained('profil_lulusans')->cascadeOnDelete();
            
            // MB=Mulai Berkembang, SB=Sedang Berkembang, BSH=Berkembang Sesuai Harapan, SAB=Sangat Berkembang
            $table->enum('nilai', ['MB', 'SB', 'BSH', 'SAB'])->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            $table->unique(['siswa_id', 'projek_p5_id', 'profil_lulusan_id'], 'nilai_p5_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_projek_p5s');
        Schema::dropIfExists('projek_p5_dimensions');
        Schema::dropIfExists('projek_p5s');
    }
};
