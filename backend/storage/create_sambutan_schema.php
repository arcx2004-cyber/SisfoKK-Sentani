<?php
$migrationPath = "/var/www/backend/database/migrations/2026_01_30_050503_create_sambutans_table.php";
$migrationContent = <<<'MIG'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sambutans', function (Blueprint $table) {
            $table->id();
            $table->string('jabatan');
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->text('konten');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sambutans');
    }
};
MIG;

file_put_contents($migrationPath, $migrationContent);

$modelPath = "/var/www/backend/app/Models/Sambutan.php";
$modelContent = <<<'MOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sambutan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan',
        'nama',
        'foto',
        'konten',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
MOD;

file_put_contents($modelPath, $modelContent);
echo "Success\n";
