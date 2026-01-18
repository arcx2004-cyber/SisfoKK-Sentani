<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Create the User
        $user = User::create([
            'name' => $data['nama_lengkap'],
            'email' => $data['email'],
            'email' => $data['email'],
            'password' => $data['password'], // Cast 'hashed' in model will handle hashing
            'is_active' => $data['is_active'] ?? true, 
        ]);

        // 2. Assign 'ptk' Role
        // Check if role exists to avoid error, though it should exist from seeder
        if (Role::where('name', 'ptk')->exists()) {
            $user->assignRole('ptk');
        }

        // 3. Link User ID to Guru Data
        $data['user_id'] = $user->id;

        // 4. Remove Email/Password from Guru data (as they don't exist in gurus table)
        unset($data['email']);
        unset($data['password']);

        return $data;
    }
}
