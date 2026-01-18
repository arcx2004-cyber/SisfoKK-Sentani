<?php

namespace App\Filament\Resources\InputRaporSdResource\Pages;

use App\Filament\Resources\InputRaporSdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInputRaporSd extends EditRecord
{
    protected static string $resource = InputRaporSdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
