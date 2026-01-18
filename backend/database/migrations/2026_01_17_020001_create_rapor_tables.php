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
        Schema::create('penilaian_sikaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('rombel_id')->constrained('rombels')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            
            // 7 Sikap defaults
            $table->enum('kedisiplinan', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('kejujuran', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('kesopanan', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('kebersihan', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('kepedulian', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('tanggung_jawab', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('percaya_diri', ['A', 'B', 'C', 'D'])->nullable();
            
            $table->timestamps();
        });

        Schema::create('catatan_rapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('rombel_id')->constrained('rombels')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_sikaps');
        Schema::dropIfExists('catatan_rapors');
    }
};
