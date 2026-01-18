<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        if ($record->user) {
            $userData = [];
            
            // Sync Email
            if (isset($data['email']) && $data['email'] !== $record->user->email) {
                $userData['email'] = $data['email'];
            }

            // Sync Password only if filled
            if (!empty($data['password'])) {
                // Determine if we need to hash manually or if Model cast handles it.
                // User model has 'password' => 'hashed'. so we pass plain text.
                // HOWEVER, to be absolutely safe against double hashing issues in specific Laravel versions:
                // We will assign it directly.
                $userData['password'] = $data['password'];
            }

            if (!empty($userData)) {
                $record->user->update($userData);
            }
        }

        // Clean up data for Guru model update
        unset($data['email']);
        unset($data['password']);

        return $data;
    }
}
