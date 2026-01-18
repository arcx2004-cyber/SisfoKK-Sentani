<?php

namespace App\Filament\Resources\RapbsResource\Pages;

use App\Filament\Resources\RapbsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRapbs extends ListRecords
{
    protected static string $resource = RapbsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
