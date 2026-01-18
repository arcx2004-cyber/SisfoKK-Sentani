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
        // Increase grade column size in nilai_sosials
        Schema::table('nilai_sosials', function (Blueprint $table) {
            $table->string('grade', 20)->change();
        });

        // Increase grade column size in nilai_spirituals
        Schema::table('nilai_spirituals', function (Blueprint $table) {
            $table->string('grade', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_sosials', function (Blueprint $table) {
            $table->string('grade', 1)->change();
        });

        Schema::table('nilai_spirituals', function (Blueprint $table) {
            $table->string('grade', 1)->change();
        });
    }
};
