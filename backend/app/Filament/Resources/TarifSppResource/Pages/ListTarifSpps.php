<?php

namespace App\Filament\Resources\TarifSppResource\Pages;

use App\Filament\Resources\TarifSppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTarifSpps extends ListRecords
{
    protected static string $resource = TarifSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
