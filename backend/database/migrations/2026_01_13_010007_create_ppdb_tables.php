<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PPDB Settings
        Schema::create('ppdb_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->text('alur_pendaftaran')->nullable();
            $table->text('persyaratan')->nullable();
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Pendaftaran PPDB
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_setting_id')->constrained()->onDelete('cascade');
            $table->string('nomor_pendaftaran')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama')->nullable();
            $table->text('alamat');
            $table->string('asal_sekolah')->nullable();
            $table->string('email');
            $table->string('no_wa');
            $table->string('nama_ayah');
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu');
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('no_telepon_ortu');
            $table->enum('status', ['pending', 'verifikasi', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });

        // Dokumen Pendaftaran
        Schema::create('dokumen_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_dokumen', ['akta_lahir', 'kartu_keluarga', 'ijazah', 'foto', 'lainnya']);
            $table->string('nama_file');
            $table->string('path');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pendaftarans');
        Schema::dropIfExists('pendaftarans');
        Schema::dropIfExists('ppdb_settings');
    }
};
