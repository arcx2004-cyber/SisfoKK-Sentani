<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RekapAbsensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class RekapAbsensiPolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin and Wali Kelas bypass
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // Allow Wali Kelas full access to manage their students' attendance
        if ($user->hasRole('wali_kelas')) {
            return true;
        }
        
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_rekap::absensi');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('view_rekap::absensi');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_rekap::absensi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('update_rekap::absensi');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('delete_rekap::absensi');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_rekap::absensi');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('force_delete_rekap::absensi');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_rekap::absensi');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('restore_rekap::absensi');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_rekap::absensi');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, RekapAbsensi $rekapAbsensi): bool
    {
        return $user->can('replicate_rekap::absensi');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_rekap::absensi');
    }
}
