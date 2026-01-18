<?php

namespace App\Filament\Resources\ProjekP5Resource\Pages;

use App\Filament\Resources\ProjekP5Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjekP5S extends ListRecords
{
    protected static string $resource = ProjekP5Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
