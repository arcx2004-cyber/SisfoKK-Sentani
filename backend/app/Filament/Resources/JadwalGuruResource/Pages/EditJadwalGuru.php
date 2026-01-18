<?php

namespace App\Filament\Resources\JadwalGuruResource\Pages;

use App\Filament\Resources\JadwalGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalGuru extends EditRecord
{
    protected static string $resource = JadwalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
