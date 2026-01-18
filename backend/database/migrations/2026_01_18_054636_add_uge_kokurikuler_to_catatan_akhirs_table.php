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
        Schema::table('catatan_akhirs', function (Blueprint $table) {
            $table->text('uge_report')->nullable()->after('catatan');
            $table->text('kokurikuler_catatan')->nullable()->after('uge_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_akhirs', function (Blueprint $table) {
            $table->dropColumn(['uge_report', 'kokurikuler_catatan']);
        });
    }
};
