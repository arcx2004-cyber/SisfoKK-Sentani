<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kokurikuler;
use Illuminate\Auth\Access\HandlesAuthorization;

class KokurikulerPolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin bypass
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // Allow Wali Kelas to view and access edit page (read-only)
        if ($user->hasRole('wali_kelas') && in_array($ability, ['viewAny', 'view', 'update'])) {
            return true;
        }
        
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kokurikuler');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('view_kokurikuler');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_kokurikuler');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('update_kokurikuler');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('delete_kokurikuler');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kokurikuler');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('force_delete_kokurikuler');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_kokurikuler');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('restore_kokurikuler');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_kokurikuler');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Kokurikuler $kokurikuler): bool
    {
        return $user->can('replicate_kokurikuler');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_kokurikuler');
    }
}
