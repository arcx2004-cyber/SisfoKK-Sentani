<?php

namespace App\Filament\Resources\InputRaporSmpResource\Pages;

use App\Filament\Resources\InputRaporSmpResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInputRaporSmp extends ViewRecord
{
    protected static string $resource = InputRaporSmpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
