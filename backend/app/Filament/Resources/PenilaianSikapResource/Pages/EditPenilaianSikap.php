<?php

namespace App\Filament\Resources\PenilaianSikapResource\Pages;

use App\Filament\Resources\PenilaianSikapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianSikap extends EditRecord
{
    protected static string $resource = PenilaianSikapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
