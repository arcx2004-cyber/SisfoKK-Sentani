<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->text('sekilas')->nullable()->after('deskripsi');
            $table->longText('konten')->nullable()->after('sekilas');
            $table->string('foto_sekolah')->nullable()->after('konten');
            $table->string('foto_kepala_sekolah')->nullable()->after('foto_sekolah');
            $table->text('visi')->nullable()->after('foto_kepala_sekolah');
            $table->text('misi')->nullable()->after('visi');
            $table->text('fasilitas')->nullable()->after('misi');
            $table->string('jam_belajar')->nullable()->after('fasilitas');
            $table->string('telepon')->nullable()->after('jam_belajar');
            $table->string('email')->nullable()->after('telepon');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn([
                'sekilas', 'konten', 'foto_sekolah', 'foto_kepala_sekolah',
                'visi', 'misi', 'fasilitas', 'jam_belajar', 'telepon', 'email'
            ]);
        });
    }
};
