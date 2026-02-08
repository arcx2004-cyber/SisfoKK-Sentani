<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SiswaSDResource\Pages;

class SiswaSDResource extends SiswaResource
{
    protected static ?string $navigationLabel = 'Siswa SD';

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek']);
    }
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'siswa-sd';

    protected static bool $shouldRegisterNavigation = true;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('unit', function ($query) {
             $query->where('kode', 'SD');
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswaSD::route('/'),
            'create' => \App\Filament\Resources\SiswaResource\Pages\CreateSiswa::route('/create'),
            'edit' => \App\Filament\Resources\SiswaResource\Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
    }
