<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Menu;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call seeders first to establish Roles, Permissions, and Master Data
        $this->call([
            MasterDataSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        // Create Super Admin User (System Owner)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sisfokk.sch.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'SisfoKK2024!')), // Fallback for local, but should be env
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Create Admin User (Content Manager)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sisfokk.sch.id'],
            [
                'name' => 'Administrator Website',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'SisfoKK2024!')),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Jabatan
        $jabatans = [
            ['nama' => 'Pendidik (PTK)', 'kode' => 'PTK', 'is_teaching' => true, 'deskripsi' => 'Guru yang mengajar mata pelajaran'],
            ['nama' => 'Tenaga Kependidikan', 'kode' => 'TENDIK', 'is_teaching' => false, 'deskripsi' => 'Staff administrasi dan keuangan'],
            ['nama' => 'Kepala Sekolah', 'kode' => 'KEPSEK', 'is_teaching' => false, 'deskripsi' => 'Kepala unit sekolah'],
        ];
        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate(['kode' => $jabatan['kode']], $jabatan);
        }

        // Create School Settings
        $settings = [
            ['key' => 'school_name', 'value' => 'Sekolah Kristen Kalam Kudus Sentani', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_short_name', 'value' => 'SKKK Sentani', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_motto', 'value' => 'Dengan Kasih & Disiplin Meningkatkan Prestasi', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_address', 'value' => 'Jl. Raya Sentani, Sentani, Jayapura, Papua', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_phone', 'value' => '(0967) 123456', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_email', 'value' => 'info@sisfokk.sch.id', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_logo', 'value' => '', 'type' => 'image', 'group' => 'general'],
            ['key' => 'primary_color', 'value' => '#1e40af', 'type' => 'text', 'group' => 'theme'],
            ['key' => 'secondary_color', 'value' => '#3b82f6', 'type' => 'text', 'group' => 'theme'],
            ['key' => 'google_maps_embed', 'value' => '', 'type' => 'textarea', 'group' => 'contact'],
        ];
        foreach ($settings as $setting) {
            SchoolSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        // Create Menus
        $menus = [
            ['nama' => 'Home', 'slug' => 'home', 'url' => '/', 'urutan' => 1],
            ['nama' => 'Profile', 'slug' => 'profile', 'url' => '/profile', 'urutan' => 2],
            ['nama' => 'Visi Misi', 'slug' => 'visi-misi', 'url' => '/visi-misi', 'urutan' => 3],
            ['nama' => 'Unit', 'slug' => 'unit', 'url' => '/unit', 'urutan' => 4],
            ['nama' => 'Galeri', 'slug' => 'galeri', 'url' => '/galeri', 'urutan' => 5],
            ['nama' => 'Kegiatan', 'slug' => 'kegiatan', 'url' => '/kegiatan', 'urutan' => 6],
            ['nama' => 'Kontak', 'slug' => 'kontak', 'url' => '/kontak', 'urutan' => 7],
            ['nama' => 'Berita', 'slug' => 'berita', 'url' => '/berita', 'urutan' => 8],
            ['nama' => 'PPDB', 'slug' => 'ppdb', 'url' => '/ppdb', 'urutan' => 9],
        ];
        foreach ($menus as $menu) {
            Menu::firstOrCreate(['slug' => $menu['slug']], $menu);
        }

        // Call other seeders
        $this->call([
            MasterDataSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin login: admin@sisfokk.sch.id / SisfoKK2024!');
    }
}
