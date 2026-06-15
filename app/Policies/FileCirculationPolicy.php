<?php

namespace App\Policies;

use App\Models\National\Eregistry\FileCirculation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FileCirculationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FileCirculation $fileCirculation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FileCirculation $fileCirculation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FileCirculation $fileCirculation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FileCirculation $fileCirculation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FileCirculation $fileCirculation): bool
    {
        return false;
    }

    /**
    * Determine whether the user can circulate the model for UFS
    */
    public function ufs(User $user, FileCirculation $fileCirculation): bool
    {
        if ($fileCirculation->ufs_id === $user->id && 
            $fileCirculation->status === "Pending UFS") {

            return true;
        }

        return false;
    
    }
}
