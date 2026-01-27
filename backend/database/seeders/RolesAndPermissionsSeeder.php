<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles sesuai kebutuhan user
        $roles = [
            'super_admin' => 'Super Administrator (System Owner)', // Changed key to standard snake_case
            'admin' => 'Administrator Website (Content Manager)',
            'kepsek' => 'Kepala Sekolah',
            'ptk' => 'PTK (Guru)',
            'wali_kelas' => 'Wali Kelas',
            'tendik' => 'Tenaga Kependidikan',
            'siswa' => 'Siswa',
        ];

        foreach ($roles as $key => $label) {
            Role::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
        }

        // 2. Define Permissions per Role
        
        // SUPER ADMIN (Full Access)
        // Usually handled by Gate::before or 'Super Admin' role logic in AppServiceProvider/User Model. 
        // But we can also assign all permissions here if preferred.
        // For this system, we will rely on specific permission assignments or a 'super_admin' check.

        // ADMIN (Website Content Manager)
        $adminPermissions = [
            // Dashboard
            'view_dashboard', // Assuming there's a dashboard permission or just default access
            // Content
            'view_any_news', 'create_news', 'update_news', 'delete_news',
            'view_any_page', 'create_page', 'update_page', 'delete_page',
            'view_any_gallery', 'create_gallery', 'update_gallery', 'delete_gallery',
            'view_any_slider', 'create_slider', 'update_slider', 'delete_slider',
            'view_any_menu', 'create_menu', 'update_menu', 'delete_menu',
            'view_any_kegiatan', 'create_kegiatan', 'update_kegiatan', 'delete_kegiatan',
            // Settings
            'view_any_school::setting', 'update_school::setting',
        ];
        $this->assignPermissions('admin', $adminPermissions);

        // KEPALA SEKOLAH (View All, Monitor, Approval)
        $kepsekPermissions = [
            'view_any_guru', 'view_guru',
            'view_any_tendik', 'view_tendik',
            'view_any_siswa', 'view_siswa',
            'view_any_capaian::pembelajaran',
            'view_any_rpp',
            'view_any_mata::pelajaran',
            'view_any_jadwal::guru',
            'view_any_rombel',
            'view_any_raport',
            'view_any_raport',
            'update_raport', // For approval
            'view_any_pembayaran::spp',
            'update_mata::pelajaran', // To Manage KKM & Grading Model
            // RAPBS Management
            'view_any_rapbs', 'create_rapbs', 'update_rapbs', 'delete_rapbs',
            // Kegiatan Management
            'view_any_kegiatan', 'create_kegiatan', 'update_kegiatan', 'delete_kegiatan',
        ];
        $this->assignPermissions('kepsek', $kepsekPermissions);

        // PTK / GURU (Manage Academic, View Master)
        $guruPermissions = [
            'view_any_mata::pelajaran',
            'view_any_siswa', 'view_siswa',
            'view_any_capaian::pembelajaran', 'create_capaian::pembelajaran', 'update_capaian::pembelajaran', 'delete_capaian::pembelajaran',
            'view_any_tujuan::pembelajaran', 'create_tujuan::pembelajaran', 'update_tujuan::pembelajaran', 'delete_tujuan::pembelajaran',
            'view_any_rpp', 'create_rpp', 'update_rpp', 'delete_rpp',
            'view_any_jadwal::guru',
            'view_any_guru',
            'view_any_rombel',
            // Nilai TP (Crucial for Input Nilai)
            'view_any_nilai::tujuan::pembelajaran', 'create_nilai::tujuan::pembelajaran', 'update_nilai::tujuan::pembelajaran', 'delete_nilai::tujuan::pembelajaran',
            // Absensi
            'create_absensi::siswa', 'update_absensi::siswa',
        ];
        $this->assignPermissions('ptk', $guruPermissions);

        // WALI KELAS (Manage Rombel, Print Raport)
        $waliKelasPermissions = [
            'view_any_siswa', 'view_siswa',
            'view_any_rombel', 'view_rombel',
            'view_any_absensi::siswa',
            'view_any_nilai::siswa', 'view_nilai::siswa',
            'view_any_raport', 'create_raport', 'update_raport',
            'view_any_ekstrakurikuler',
            'view_any_kokurikuler',
        ];
        $this->assignPermissions('wali_kelas', $waliKelasPermissions);

        // TENDIK (Manage Master Data, Admin)
        $tendikPermissions = [
            'view_any_guru', 'create_guru', 'update_guru', 'delete_guru',
            'view_any_tendik', 'create_tendik', 'update_tendik', 'delete_tendik',
            'view_any_siswa', 'create_siswa', 'update_siswa', 'delete_siswa',
            'view_any_mata::pelajaran', 'create_mata::pelajaran', 'update_mata::pelajaran', 'delete_mata::pelajaran',
            'view_any_ruang::kelas', 'create_ruang::kelas', 'update_ruang::kelas', 'delete_ruang::kelas',
            'view_any_rombel', 'create_rombel', 'update_rombel', 'delete_rombel',
            'view_any_unit',
            'view_any_jabatan',
            'view_any_tahun::ajaran', 'create_tahun::ajaran', 'update_tahun::ajaran',
        ];
        $this->assignPermissions('tendik', $tendikPermissions);

        // SISWA (View Own Data)
        $siswaPermissions = [
            'view_siswa', // Only own via Policy
        ];
        $this->assignPermissions('siswa', $siswaPermissions);
    }

    private function assignPermissions($roleName, $permissions)
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) return;

        // Sync permissions (only existing ones to avoid errors if Shield hasn't generated them yet)
        $validPermissions = Permission::whereIn('name', $permissions)->pluck('name')->toArray();
        $role->syncPermissions($validPermissions);
    }
}
