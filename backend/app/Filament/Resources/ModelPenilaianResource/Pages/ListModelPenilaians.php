<?php

namespace App\Filament\Resources\ModelPenilaianResource\Pages;

use App\Filament\Resources\ModelPenilaianResource;
use Filament\Resources\Pages\ListRecords;

class ListModelPenilaians extends ListRecords
{
    protected static string $resource = ModelPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
