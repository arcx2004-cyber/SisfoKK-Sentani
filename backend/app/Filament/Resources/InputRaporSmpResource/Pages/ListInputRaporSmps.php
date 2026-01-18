<?php

namespace App\Filament\Resources\InputRaporSmpResource\Pages;

use App\Filament\Resources\InputRaporSmpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInputRaporSmps extends ListRecords
{
    protected static string $resource = InputRaporSmpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
