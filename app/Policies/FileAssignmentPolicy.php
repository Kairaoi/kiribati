<?php

namespace App\Policies;

use App\Models\National\Eregistry\FileAssignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FileAssignmentPolicy
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
    public function view(User $user, FileAssignment $fileAssignment): bool
    {
        if ($fileAssignment->status === "pending" && 
            $fileAssignment->fileCirculation?->to_ministry_id === $user->ministry_id && 
            $fileAssignment->officer_id === $user->id
        ) {
            return true;
        }
        return false;
    }


    // /**
    //  * Determine whether the user can view the model.
    //  */
    // public function viewAll(User $user, FileAssignment $fileAssignment): bool
    // {
    //     if ($user->hasRole('registry') && 
    //         $fileAssignment->fileCirculation?->to_ministry_id === $user->ministry_id 
    //     ) {
    //         return true;
    //     }
    //     return false;
    // }

    
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
    public function update(User $user, FileAssignment $fileAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FileAssignment $fileAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FileAssignment $fileAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FileAssignment $fileAssignment): bool
    {
        return false;
    }
}
