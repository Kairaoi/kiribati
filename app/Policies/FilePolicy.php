<?php

namespace App\Policies;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\IdentityOrganisation;
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
     * Determine whether the user can close the model.
     */
    public function close(User $user, File $file): bool
    {
        $loggedInMinistryId = auth()->user()->ministry_id;
        
        if($user->hasRole('registry') && 
           $file->ministry_id === $loggedInMinistryId && 
           $file->status === 'Dispatched') {
            return true;
           }

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
    * Determine whether the user can circulate the model to review officer
    */
    public function dispatch(User $user, File $file): bool
    {
        $loggedInMinistryId = auth()->user()->ministry_id;

        $isOwnMinistryIdentityOrganisation =
            $file->source_type === IdentityOrganisation::class
            && (int) $file->source_id === (int) $loggedInMinistryId;

        if ($file->status === "Pending Action" && 
            $user->hasRole('registry') && 
            $file->correspondence_type !== "letter" && 
            $file->correspondence_type !== "internal" && 
            $isOwnMinistryIdentityOrganisation)
        {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file): bool
    {

        if ($file->created_by === $user->id &&
           ($file->status === "Pending Action" || $file->status === "Returned for Amendment" )) {
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
            $file->correspondence_type === 'internal' &&
            $file->status !== "Pending UFS") {

            return true;
        }

        return false;
    
    }

    /**
    * Determine whether the user can circulate the model to be reviewed
    */
    public function circulateForReview(User $user, File $file): bool
    {
        if ((($file->document_source === 'upload' && ($file->status === "Pending Action" || $file->status === "Returned for Amendment" )) ||
            ($file->document_source === 'online' && $file->correspondence_type === 'letter' && ($file->status === "Pending Action" || $file->status === "Returned for Amendment" )) || 
            ($file->document_source === 'online' && $file->correspondence_type === 'memo' &&  ($file->status === "Pending Action" || $file->status === "Returned for Amendment" )) ) && 
            ($file->created_by === $user->id && !$user->hasRole('registry') )
        ) {

            return true;
        }

        return false;
    }


    /**
    * Determine whether the user can circulate the model to review officer
    */
    public function circulateToReviewOfficer(User $user, File $file): bool
    {
        if (($file->document_source === 'upload' && $file->status === "Pending Action") &&
            $user->hasRole('registry') 
        ) {
            
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
