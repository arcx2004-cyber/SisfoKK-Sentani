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
        Schema::create('kesehatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('pendengaran')->nullable(); // e.g., "Baik"
            $table->string('penglihatan')->nullable();
            $table->string('gigi')->nullable();
            $table->string('lainnya')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });

        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('jenis')->nullable(); // e.g., "Juara 1 Lomba Lari"
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasis');
        Schema::dropIfExists('kesehatans');
    }
};
