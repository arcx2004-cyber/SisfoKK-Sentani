<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

abstract class BaseResource extends Resource
{
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Super Admin & Admin see everything
        if ($user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek'])) {
            return true;
        }

        // Siswa only sees their own specific resources (which override this method)
        if ($user->hasRole('siswa')) {
            return false;
        }

        $group = static::getNavigationGroup();

        // Master Data -> Admin only (handled above)
        if ($group === 'Master Data') {
            return $user->hasAnyRole(['tendik']);
        }

        // Akademik & Akademik Guru -> PTK (Guru) and Wali Kelas
        if ($group === 'Akademik' || $group === 'Akademik Guru') {
            return $user->hasAnyRole(['ptk', 'wali_kelas']);
        }

        // Administrasi -> Tendik (Staff)
        if ($group === 'Administrasi') {
            return $user->hasRole('tendik');
        }

        // Kesiswaan -> PTK and Wali Kelas
        if ($group === 'Kesiswaan') {
            return $user->hasAnyRole(['ptk', 'wali_kelas']);
        }

        // PPDB -> Restricted (Admins only via top-level check)
        // if ($group === 'PPDB') {
        //     return $user->hasRole('tendik');
        // }

        // CMS & Pengaturan -> Tendik
        if ($group === 'CMS' || $group === 'Pengaturan') {
            return $user->hasRole('tendik');
        }

        // Keuangan -> Tendik
        if ($group === 'Keuangan') {
            return $user->hasRole('tendik');
        }

        // Tugas Tambahan -> PTK
        if ($group === 'Tugas Tambahan') {
            return $user->hasRole('ptk');
        }

        // Default deny
        return false;
    }
}

