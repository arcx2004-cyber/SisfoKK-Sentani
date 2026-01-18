<?php

namespace App\Filament\Resources\InputRaporSdResource\Pages;

use App\Filament\Resources\InputRaporSdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInputRaporSds extends ListRecords
{
    protected static string $resource = InputRaporSdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
