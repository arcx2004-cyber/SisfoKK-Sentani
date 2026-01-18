<?php

namespace App\Filament\Resources\EkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEkstrakurikuler extends EditRecord
{
    protected static string $resource = EkstrakurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
        ];
    }

    protected function getFormActions(): array
    {
        // Hide save/cancel buttons for Wali Kelas (read-only mode)
        if (auth()->user()->hasRole('wali_kelas')) {
            return [];
        }
        return parent::getFormActions();
    }
}
