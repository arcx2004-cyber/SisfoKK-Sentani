<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class SiswaSMPResource extends SiswaResource
{
    protected static ?string $navigationLabel = 'Siswa SMP';

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek']);
    }
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'siswa-smp';

    protected static bool $shouldRegisterNavigation = true;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('unit', function ($query) {
             $query->where('nama', 'like', '%Sekolah Menengah Pertama%');
        });
    }
}
