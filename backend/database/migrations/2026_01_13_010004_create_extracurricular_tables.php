<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ekstrakurikuler
        Schema::create('ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pelatih Ekstrakurikuler
        Schema::create('pelatih_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained()->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nama_pelatih'); // Could be external coach
            $table->string('no_telepon')->nullable();
            $table->timestamps();
        });

        // Rombel Ekstrakurikuler (Students in Ekskul)
        Schema::create('rombel_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained()->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['ekstrakurikuler_id', 'siswa_id', 'semester_id'], 'rombel_ekskul_unique');
        });

        // Nilai Ekstrakurikuler
        Schema::create('nilai_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_ekskul_id')->constrained('rombel_ekskuls')->onDelete('cascade');
            $table->enum('grade', ['A', 'B', 'C', 'D']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ekskuls');
        Schema::dropIfExists('rombel_ekskuls');
        Schema::dropIfExists('pelatih_ekskuls');
        Schema::dropIfExists('ekstrakurikulers');
    }
};
