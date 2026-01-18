<?php

namespace App\Filament\Resources\RombelResource\Pages;

use App\Filament\Resources\RombelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRombel extends EditRecord
{
    protected static string $resource = RombelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $waliKelasId = $this->data['wali_kelas_id'] ?? null;
        
        if ($waliKelasId) {
            $semester = \App\Models\Semester::getActive();
            
            if ($semester) {
                // Update or Create
                \App\Models\WaliKelas::updateOrCreate(
                    [
                        'rombel_id' => $this->record->id,
                        'semester_id' => $semester->id,
                    ],
                    [
                        'guru_id' => $waliKelasId,
                    ]
                );
                
                // Assign role if needed
                $guru = \App\Models\Guru::find($waliKelasId);
                $guru?->user?->assignRole('wali_kelas');
            }
        }
    }
}
