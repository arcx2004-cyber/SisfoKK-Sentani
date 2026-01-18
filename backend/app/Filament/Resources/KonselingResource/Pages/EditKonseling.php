<?php

namespace App\Filament\Resources\KonselingResource\Pages;

use App\Filament\Resources\KonselingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKonseling extends EditRecord
{
    protected static string $resource = KonselingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
