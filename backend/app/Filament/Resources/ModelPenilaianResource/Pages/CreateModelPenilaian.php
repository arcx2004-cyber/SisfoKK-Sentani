<?php

namespace App\Filament\Resources\ModelPenilaianResource\Pages;

use App\Filament\Resources\ModelPenilaianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModelPenilaian extends CreateRecord
{
    protected static string $resource = ModelPenilaianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
