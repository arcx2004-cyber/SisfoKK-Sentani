<?php

namespace App\Filament\Resources\KokurikulerResource\Pages;

use App\Filament\Resources\KokurikulerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKokurikuler extends CreateRecord
{
    protected static string $resource = KokurikulerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
