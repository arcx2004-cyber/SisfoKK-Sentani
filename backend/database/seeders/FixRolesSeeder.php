<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FixRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Fix 'dia' -> 'direktur_pelaksana'
        $diaRole = Role::where('name', 'dia')->first();
        if ($diaRole) {
            $diaRole->update(['name' => 'direktur_pelaksana']);
        } else {
            Role::firstOrCreate(['name' => 'direktur_pelaksana', 'guard_name' => 'web']);
        }
        
        // Ensure other roles exist properly
        $roles = [
            'kepala_sekolah', 'panitia_ppdb', 'bendahara'
        ];
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
