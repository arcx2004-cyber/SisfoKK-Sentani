<?php

namespace App\Filament\Resources\KokurikulerResource\Pages;

use App\Filament\Resources\KokurikulerResource;
use Filament\Resources\Pages\ListRecords;

class ListKokurikulers extends ListRecords
{
    protected static string $resource = KokurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
