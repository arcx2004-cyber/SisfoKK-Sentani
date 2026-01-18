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
        Schema::table('bk_records', function (Blueprint $table) {
            $table->integer('skor')->default(0)->after('jenis');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null')->after('skor');
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('set null')->after('semester_id');
        });
    }

    public function down(): void
    {
        Schema::table('bk_records', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn(['skor', 'semester_id', 'tahun_ajaran_id']);
        });
    }
};
