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
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('alpha')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('catatan_akhirs', function (Blueprint $table) {
            $table->dropColumn(['sakit', 'izin', 'alpha']);
        });
    }
};
