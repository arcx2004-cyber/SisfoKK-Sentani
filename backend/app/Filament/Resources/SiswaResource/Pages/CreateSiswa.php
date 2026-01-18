<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Generate Automatic User Credentials
        // Format: siswa{NIS}@sisfokk-sentani.sch.id
        $nis = $data['nis'] ?? rand(10000, 99999); // Fallback if NIS empty, though required
        $email = "siswa{$nis}@sisfokk-sentani.sch.id";
        $password = '12345678'; // Default password

        // Check uniqueness just in case, append random if conflict (rare given NIS unique)
        if (User::where('email', $email)->exists()) {
            $email = "siswa{$nis}." . rand(100,999) . "@sisfokk-sentani.sch.id";
        }

        // 2. Create User
        $user = User::create([
            'name' => $data['nama_lengkap'],
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        // 3. Assign Role
        if (Role::where('name', 'siswa')->exists()) {
            $user->assignRole('siswa');
        }

        // 4. Link User ID & Set Default Status
        $data['user_id'] = $user->id;
        $data['status'] = 'Aktif';

        return $data;
    }
}
