<?php

namespace App\Filament\Resources\JadwalGuruResource\Pages;

use App\Filament\Resources\JadwalGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalGurus extends ListRecords
{
    protected static string $resource = JadwalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
