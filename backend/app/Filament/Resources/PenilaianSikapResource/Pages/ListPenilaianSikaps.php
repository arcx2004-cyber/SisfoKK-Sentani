<?php

namespace App\Filament\Resources\PenilaianSikapResource\Pages;

use App\Filament\Resources\PenilaianSikapResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenilaianSikaps extends ListRecords
{
    protected static string $resource = PenilaianSikapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
