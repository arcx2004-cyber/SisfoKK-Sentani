<?php

namespace App\Filament\Resources\NilaiSpiritualResource\Pages;

use App\Filament\Resources\NilaiSpiritualResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiSpirituals extends ListRecords
{
    protected static string $resource = NilaiSpiritualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
