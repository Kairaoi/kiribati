<?php

namespace App\Policies;

use App\Models\National\Eregistry\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
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
    public function view(User $user, File $file): bool
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
    public function update(User $user, File $file): bool
    {
        if ($file->created_by === $user->id && $file->status === "Pending Action") {
            return true;
        }

        return false;
    
    }


    /**
    * Determine whether the user can circulate the model for UFS
    */
    public function ufsCirculate(User $user, File $file): bool
    {
        if ($file->created_by === $user->id && 
            $file->status === "Pending Action" && 
            $file->document_source === 'online' && 
            $file->correspondence_type === 'internal') {

            return true;
        }

        return false;
    
    }

    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, File $file): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, File $file): bool
    {
        return false;
    }
}
