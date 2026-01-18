<?php

namespace App\Filament\Resources\NilaiSosialResource\Pages;

use App\Filament\Resources\NilaiSosialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiSosials extends ListRecords
{
    protected static string $resource = NilaiSosialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
