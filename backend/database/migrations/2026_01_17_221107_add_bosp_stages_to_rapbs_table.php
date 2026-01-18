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
        Schema::table('rapbs', function (Blueprint $table) {
            $table->decimal('bosp_tahap_1', 15, 2)->default(0)->after('status');
            $table->decimal('bosp_tahap_2', 15, 2)->default(0)->after('bosp_tahap_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapbs', function (Blueprint $table) {
            $table->dropColumn(['bosp_tahap_1', 'bosp_tahap_2']);
        });
    }
};
