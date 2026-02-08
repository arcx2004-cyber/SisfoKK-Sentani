<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SiswaTKResource\Pages;

class SiswaTKResource extends SiswaResource
{
    protected static ?string $navigationLabel = 'Siswa TK';

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek']);
    }
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'siswa-tk';
    
    protected static bool $shouldRegisterNavigation = true;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('unit', function ($query) {
            $query->where('kode', 'TK');
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswaTK::route('/'),
            'create' => \App\Filament\Resources\SiswaResource\Pages\CreateSiswa::route('/create'),
            'edit' => \App\Filament\Resources\SiswaResource\Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
    }
