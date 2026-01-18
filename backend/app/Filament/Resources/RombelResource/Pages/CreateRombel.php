<?php

namespace App\Filament\Resources\RombelResource\Pages;

use App\Filament\Resources\RombelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRombel extends CreateRecord
{
    protected static string $resource = RombelResource::class;

    protected function afterCreate(): void
    {
        $waliKelasId = $this->data['wali_kelas_id'] ?? null;
        
        if ($waliKelasId) {
            $semester = \App\Models\Semester::getActive();
            
            if ($semester) {
                \App\Models\WaliKelas::create([
                    'rombel_id' => $this->record->id,
                    'guru_id' => $waliKelasId,
                    'semester_id' => $semester->id,
                ]);
                
                // Assign role if needed
                $guru = \App\Models\Guru::find($waliKelasId);
                $guru?->user?->assignRole('wali_kelas');
            }
        }
    }
}
