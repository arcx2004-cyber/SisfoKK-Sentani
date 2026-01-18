<?php

namespace App\Filament\Resources\NilaiAkhirResource\Pages;

use App\Filament\Resources\NilaiAkhirResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiAkhirs extends ListRecords
{
    protected static string $resource = NilaiAkhirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
