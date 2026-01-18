<?php

namespace App\Filament\Resources\RekapAbsensiResource\Pages;

use App\Filament\Resources\RekapAbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRekapAbsensis extends ManageRecords
{
    protected static string $resource = RekapAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
