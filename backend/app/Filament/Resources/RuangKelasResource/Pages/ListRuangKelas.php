<?php

namespace App\Filament\Resources\RuangKelasResource\Pages;

use App\Filament\Resources\RuangKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRuangKelas extends ListRecords
{
    protected static string $resource = RuangKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
