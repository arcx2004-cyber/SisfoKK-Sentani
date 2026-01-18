<?php

namespace App\Filament\Resources\SiswaBiodataResource\Pages;

use App\Filament\Resources\SiswaBiodataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswaBiodata extends EditRecord
{
    protected static string $resource = SiswaBiodataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No delete action needed for students on their own biodata
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
