<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jadwal Guru (Teacher Schedule)
        Schema::create('jadwal_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained()->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('rombel_id')->constrained()->onDelete('cascade');
            $table->foreignId('ruang_kelas_id')->constrained('ruang_kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });

        // Raport (Report Card)
        Schema::create('raports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('rombel_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'printed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->text('catatan_wali_kelas')->nullable();
            $table->text('catatan_kepala_sekolah')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });

        // BK (Bimbingan Konseling)
        Schema::create('bk_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained()->onDelete('set null');
            $table->date('tanggal');
            $table->enum('jenis', ['konseling', 'pelanggaran', 'prestasi', 'lainnya']);
            $table->text('deskripsi');
            $table->text('tindak_lanjut')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bk_records');
        Schema::dropIfExists('raports');
        Schema::dropIfExists('jadwal_gurus');
    }
};
