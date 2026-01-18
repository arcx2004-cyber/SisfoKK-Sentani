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
        Schema::create('nilai_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('model_penilaian_id')->constrained()->cascadeOnDelete();
            $table->decimal('nilai', 5, 2);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'mata_pelajaran_id', 'semester_id', 'model_penilaian_id'], 'nilai_evaluasi_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_evaluasis');
    }
};
