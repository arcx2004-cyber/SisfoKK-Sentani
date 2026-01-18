<?php

namespace App\Filament\Resources\NilaiSosialResource\Pages;

use App\Filament\Resources\NilaiSosialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiSosial extends EditRecord
{
    protected static string $resource = NilaiSosialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
