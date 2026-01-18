<?php

namespace App\Filament\Resources\ModelPenilaianResource\Pages;

use App\Filament\Resources\ModelPenilaianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModelPenilaian extends EditRecord
{
    protected static string $resource = ModelPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
