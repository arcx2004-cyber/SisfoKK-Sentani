<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = [
            'super_admin',
            // 'guru' merged into 'ptk'
            'siswa',
            'kepala_sekolah',
            'dia', // Director of Academic? Or just general Director.
            'bendahara',
            'tendik',
            'panitia_ppdb', // New Role
            'ptk', 
            'yayasan'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        
        // Assign permissions logic could go here, but for now just ensuring role exists.
    }
}
