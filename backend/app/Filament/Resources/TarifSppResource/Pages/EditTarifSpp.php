<?php

namespace App\Filament\Resources\TarifSppResource\Pages;

use App\Filament\Resources\TarifSppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTarifSpp extends EditRecord
{
    protected static string $resource = TarifSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
