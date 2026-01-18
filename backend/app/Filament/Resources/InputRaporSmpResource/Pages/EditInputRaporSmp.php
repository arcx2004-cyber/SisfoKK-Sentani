<?php

namespace App\Filament\Resources\InputRaporSmpResource\Pages;

use App\Filament\Resources\InputRaporSmpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInputRaporSmp extends EditRecord
{
    protected static string $resource = InputRaporSmpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
