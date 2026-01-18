<?php

namespace App\Filament\Resources\SiswaBiodataResource\Pages;

use App\Filament\Resources\SiswaBiodataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiswaBiodatas extends ListRecords
{
    protected static string $resource = SiswaBiodataResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
