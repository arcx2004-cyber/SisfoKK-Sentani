<?php

namespace App\Filament\Resources\KokurikulerResource\Pages;

use App\Filament\Resources\KokurikulerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKokurikuler extends EditRecord
{
    protected static string $resource = KokurikulerResource::class;

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
