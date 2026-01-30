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
        Schema::table("siswas", function (Blueprint $table) {
            $table->string("rt", 10)->nullable()->after("alamat");
            $table->string("rw", 10)->nullable()->after("rt");
            $table->string("kelurahan")->nullable()->after("rw");
            $table->string("kecamatan")->nullable()->after("kelurahan");
            $table->string("kode_pos", 10)->nullable()->after("kecamatan");
            $table->string("jenis_tinggal")->nullable()->after("kode_pos");
            $table->string("alat_transportasi")->nullable()->after("jenis_tinggal");
            $table->string("sekolah_asal")->nullable()->after("foto");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("siswas", function (Blueprint $table) {
            $table->dropColumn([
                "rt",
                "rw",
                "kelurahan",
                "kecamatan",
                "kode_pos",
                "jenis_tinggal",
                "alat_transportasi",
                "sekolah_asal"
            ]);
        });
    }
};