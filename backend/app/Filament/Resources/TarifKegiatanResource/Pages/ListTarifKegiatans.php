<?php

namespace App\Filament\Resources\TarifKegiatanResource\Pages;

use App\Filament\Resources\TarifKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTarifKegiatans extends ListRecords
{
    protected static string $resource = TarifKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
