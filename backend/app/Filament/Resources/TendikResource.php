<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource;
use App\Models\Guru;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class TendikResource extends GuruResource
{
    protected static ?string $navigationLabel = 'Data Tendik';
    
    public static function getNavigationGroup(): ?string
    {
        if (auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            return 'Administrasi Kepala Sekolah';
        }
        return 'Master Data Sekolah';
    }
    
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'tendik';
    protected static ?string $modelLabel = 'Tendik';
    protected static ?string $pluralModelLabel = 'Tendik';

    public static function getEloquentQuery(): Builder
    {
        // Don't call parent::getEloquentQuery() as it forces is_teaching=true
        // We use the aliased trait method from GuruResource
        return self::unitFilteredQuery()->whereHas('jabatan', function ($query) {
            $query->where('is_teaching', false);
        });
    }
}
