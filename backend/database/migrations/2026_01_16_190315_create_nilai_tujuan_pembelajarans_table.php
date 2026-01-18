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
        Schema::create('nilai_tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tujuan_pembelajaran_id')->constrained()->cascadeOnDelete();
            $table->decimal('nilai', 5, 2);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tujuan_pembelajaran_id'], 'nilai_tp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_tujuan_pembelajarans');
    }
};
