<?php

namespace App\Filament\Resources\NilaiTujuanPembelajaranResource\Pages;

use App\Filament\Resources\NilaiTujuanPembelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiTujuanPembelajaran extends EditRecord
{
    protected static string $resource = NilaiTujuanPembelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
