<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class MergeGuruToPtkRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Find Guru Role
        $guruRole = Role::where('name', 'guru')->first();
        $ptkRole = Role::firstOrCreate(['name' => 'ptk', 'guard_name' => 'web']);

        if ($guruRole) {
            // 2. Find all users with this role
            $users = User::role('guru')->get();
            
            foreach ($users as $user) {
                // 3. Assign PTK role if not exists
                if (!$user->hasRole('ptk')) {
                    $user->assignRole($ptkRole);
                }
                // 4. Remove Guru role
                $user->removeRole($guruRole);
            }

            // 5. Delete Guru Role
            $guruRole->delete(); 
        }
    }
}
