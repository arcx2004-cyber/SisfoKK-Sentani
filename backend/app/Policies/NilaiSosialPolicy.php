<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NilaiSosial;
use Illuminate\Auth\Access\HandlesAuthorization;

class NilaiSosialPolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin and Wali Kelas bypass
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin') || $user->hasRole('wali_kelas')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_nilai::sosial');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('view_nilai::sosial');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_nilai::sosial');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('update_nilai::sosial');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('delete_nilai::sosial');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_nilai::sosial');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('force_delete_nilai::sosial');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_nilai::sosial');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('restore_nilai::sosial');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_nilai::sosial');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, NilaiSosial $nilaiSosial): bool
    {
        return $user->can('replicate_nilai::sosial');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_nilai::sosial');
    }
}
