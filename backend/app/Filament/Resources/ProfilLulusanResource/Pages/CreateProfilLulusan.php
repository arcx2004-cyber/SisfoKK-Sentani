<?php

namespace App\Filament\Resources\ProfilLulusanResource\Pages;

use App\Filament\Resources\ProfilLulusanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProfilLulusan extends CreateRecord
{
    protected static string $resource = ProfilLulusanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
