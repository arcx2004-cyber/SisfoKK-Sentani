<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapbs', function (Blueprint $table) {
            $table->decimal('alokasi_dana_kegiatan', 15, 2)->default(0)->after('total_pengeluaran');
        });

        Schema::table('rapbs_details', function (Blueprint $table) {
            $table->enum('sumber_dana', ['bosp', 'kegiatan'])->nullable()->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('rapbs', function (Blueprint $table) {
            $table->dropColumn('alokasi_dana_kegiatan');
        });

        Schema::table('rapbs_details', function (Blueprint $table) {
            $table->dropColumn('sumber_dana');
        });
    }
};
