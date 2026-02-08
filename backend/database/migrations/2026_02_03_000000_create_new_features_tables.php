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
        // 1. Documents Table
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('type')->default('general'); // e.g. brosur, formulir, sk
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        // 2. Mading (Majalah Dinding) Table
        Schema::create('madings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('author_name')->nullable();
            $table->string('status')->default('draft'); // draft, published, rejected
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 3. Update Prestasis Table
        Schema::table('prestasis', function (Blueprint $table) {
            $table->string('judul')->after('semester_id')->nullable();
            $table->date('tanggal')->after('judul')->nullable();
            $table->string('foto')->after('keterangan')->nullable();
            $table->boolean('is_public')->default(false)->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('madings');

        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropColumn(['judul', 'tanggal', 'foto', 'is_public']);
        });
    }
};
