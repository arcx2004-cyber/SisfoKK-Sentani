<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tarif SPP
        Schema::create('tarif_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained()->onDelete('cascade');
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['unit_id', 'tahun_ajaran_id']);
        });

        // Tarif Kegiatan Tahunan
        Schema::create('tarif_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained()->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Pembayaran SPP
        Schema::create('pembayaran_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('tarif_spp_id')->constrained()->onDelete('cascade');
            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // 2024, 2025, etc.
            $table->decimal('nominal', 12, 2);
            $table->decimal('nominal_bayar', 12, 2);
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'sebagian'])->default('belum_bayar');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['siswa_id', 'tarif_spp_id', 'bulan', 'tahun'], 'pembayaran_spp_unique');
        });

        // Pembayaran Kegiatan
        Schema::create('pembayaran_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('tarif_kegiatan_id')->constrained()->onDelete('cascade');
            $table->decimal('nominal', 12, 2);
            $table->decimal('nominal_bayar', 12, 2);
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'sebagian'])->default('belum_bayar');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['siswa_id', 'tarif_kegiatan_id'], 'pembayaran_kegiatan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_kegiatans');
        Schema::dropIfExists('pembayaran_spps');
        Schema::dropIfExists('tarif_kegiatans');
        Schema::dropIfExists('tarif_spps');
    }
};
