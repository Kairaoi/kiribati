<?php

namespace App\Policies;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\Organisation;
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
        if ($file->created_by === $user->id || 
           ($file->organisation_id === $user->organisation_id && 
           $user->hasRole(['registry', 'ministry-admin', 'review-officer', 'sro'])) ) {
            return true;
        }


        // Assigned officer of the file circulation (recipient organisation)
        if ($file->circulations()->whereHas('assignments', function ($query) use ($user) {
                    $query->where('officer_id', $user->id);
            })->exists()) {
            return true;
        }


        if ($file->internal_ufs_id === $user->id) {
            return true;
        }

        
        if ($file->circulations()->where('colleague_id', $user->id)->exists()) {
            return true;
        }

        
        // Review officer of the recipient organisation
        if ($file->circulations()->where('review_officer', $user->id)->exists()) {
            return true;
        }

        // Registry officers of the recipient organisation
        if ($user->hasRole('registry') &&
            $file->circulations()->where('to_organisation_id', $user->organisation_id)->exists()
        ) {
            return true;
        }

        return false;
    }



    /**
     * Determine whether the user can close the model.
     */
    public function close(User $user, File $file): bool
    {
        $loggedInOrganisationId = $user->organisation_id;

        if($user->hasRole('registry') && 
           $file->organisation_id === $loggedInOrganisationId && 
           ($file->status === 'Dispatched' || 
            $file->status === 'Pending Dispatch' || 
            $file->status === 'Pending Action' ||
            $file->status === 'Cancelled') 
            )
        {
           return true;
        }

        if ($user->hasRole('registry') &&
            $file->circulations()->where('to_organisation_id', $user->organisation_id)
                                 ->whereIn('status', ['Reviewed', 
                                                      'Pending SRO Review', 
                                                      'Approved', 
                                                      'Cancelled',
                                                      'Signature Cancelled',
                                ])->exists()
        ) {
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
        $organisationId = $user->organisation_id;

        $ownedFile =
            $file->source_type === Organisation::class
            && (int) $file->source_id === (int) $organisationId;

        if ($file->document_source === 'upload' && 
            $file->status === 'Pending Action' && 
            $user->hasRole('registry') && $ownedFile) {
            return true;
        }

        if ($file->correspondence_type === 'memo' &&
            ($file->status === "Approved" || $file->status === "Pending Dispatch") &&
            $user->hasRole('registry') &&
            $ownedFile)
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
           ($file->status === "Pending Signature" || $file->status === "Pending Action" || $file->status === "Returned for Amendment" )) {
            return true;
        }

        return false;
    
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {

        if ($file->created_by === $user->id &&
           ($file->status === "Pending Signature" || $file->status === "Pending Action")) {
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
            $file->status === "Pending UFS Circulation" && 
            $file->document_source === 'online' && 
            $file->correspondence_type === 'internal') {

            return true;
        }

        return false;
    
    }


    /**
    * Determine whether the user can sign the model
    */
    public function sign(User $user, File $file): bool
    {
        $fileCirculation = $file->circulations()
                                ->where(function ($query) use ($user) {
                                    $query->where('review_officer', $user->id)
                                        ->orWhere('colleague_id', $user->id);
                                })
                                ->first();
        
        
        if ($file->organisation_id === $user->organisation_id &&
            ($file->created_by === $user->id || ($fileCirculation && $fileCirculation->review_officer === $user->id)) &&
            ($file->status === "Pending Signature" || 
            $file->status === "Pending SRO Signature" || $file->status === "Returned for Amendment") && 
            $file->document_source === 'online') {

            return true;
        }

        if ($file->organisation_id === $user->organisation_id && 
            $fileCirculation && 
            $fileCirculation->status === "Pending HOD Review" && 
            $fileCirculation->colleague_id === $user->id) {
                return true;
        }



        return false;
    
    }


    /**
    * Determine whether the user can reject the model
    */
    public function cancelSignature(User $user, File $file): bool
    {
        $fileCirculation = $file->circulations()->where('review_officer', $user->id)->first();
        
        if (($file->organisation_id === $user->organisation_id &&
            $fileCirculation && 
            $fileCirculation->review_officer &&$fileCirculation && 
            $fileCirculation->review_officer === $user->id) &&
            ($file->status === "Pending SRO Signature") && 
            $file->document_source === 'online') {

            return true;
        }

        return false;
    
    }


    /**
    * Determine whether the user can circulate the model to be reviewed
    * Only for users, registry, etc, not Secretaries and HODs 
    */
    public function circulateToHod(User $user, File $file): bool
    {
        
        if ((($file->document_source === 'upload' && ($file->status === "Pending Signature" || $file->status === "Returned for Amendment" )) ||
            ($file->correspondence_type === 'letter' && ($file->status === "Pending Signature" || $file->status === "Pending Action" || $file->status === "Returned for Amendment" )) || 
            ($file->correspondence_type === 'memo' &&  ($file->status === "Pending Signature" || $file->status === "Pending Action" || $file->status === "Returned for Amendment" )) ) && 
            $file->created_by === $user->id && 
            !$user->hasRole('sro') &&
            !$user->hasRole('hod')
        ) {

            return true;
        }

        return false;
    }


    /**
    * Determine whether the user can circulate the model to review officer
    * Does not work for secretaries as they are the SRO themselves
    */
    public function circulateToReviewOfficer(User $user, File $file): bool
    {

        if (($file->document_source === 'upload' && 
            $file->status === "Pending Action") &&
            $user->hasRole('registry') 
        ) {
            
            return true;
        } 

        return false;
    
    }

    public function submitForSroSignature(User $user, File $file): bool
    {

        if ( ($file->correspondence_type === 'memo' || 
            $file->correspondence_type === 'letter') && 
            $file->status === "Pending Signature" &&
            $file->created_by === $user->id &&
            !$user->hasRole('sro')
        ) {
            
            return true;
        } 

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



    public function viewSignatureDetails(User $user, File $file): bool
    {
        if ($file->organisation_id === $user->organisation_id && $file->signature_path) {
            return true;
        }

        return false;
    }
}
