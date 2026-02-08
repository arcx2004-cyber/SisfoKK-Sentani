<?php

namespace App\Filament\Resources\MadingResource\Pages;

use App\Filament\Resources\MadingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMadings extends ListRecords
{
    protected static string $resource = MadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
