<?php

namespace App\Policies;

use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\File;
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
     * Determine whether the user can close the model.
     */
    public function close(User $user, FileCirculation $fileCirculation): bool
    {
        $loggedInMinistryId = auth()->user()->ministry_id;
        
        if($user->hasRole('registry') && 
           $fileCirculation->to_ministry_id === $loggedInMinistryId ) {
            return true;
           }

        return false;
    }


    /**
     * Determine whether the circulation assignments.
     */
    public function viewAssignments(User $user, FileCirculation $fileCirculation): bool
    {
        if ($user->hasRole('registry') && 
            $fileCirculation->to_ministry_id === $user->ministry_id &&
            $fileCirculation->activeAssignments()
            ->where('is_active', true)
            ->exists()
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
    public function markReceive(User $user, FileCirculation $fileCirculation): bool
    {
        $loggedInMinistryId = auth()->user()->ministry_id;
         
        $file = File::findOrFail($fileCirculation['file_id']);

        if ($fileCirculation->to_ministry_id === $loggedInMinistryId && 
            $file->ministry_id !== $loggedInMinistryId &&
            $fileCirculation->status === "Pending Receipt" && 
            $user->hasRole('registry')) {
            return true;
        }
        return false;
    }


    /**
    * Determine whether the user can circulate the model for UFS
    */
    public function ufs(User $user, FileCirculation $fileCirculation): bool
    {
        if ($fileCirculation->file->internal_ufs_id === $user->id && 
            $fileCirculation->status === "Pending UFS") {

            return true;
        }

        return false;
    
    }

    /**
    * Determine whether the user can circulate the file for approval/review
    */
    // public function circulateForUFSApproval(User $user, FileCirculation $fileCirculation): bool
    // {
    //     if ($fileCirculation->status === "UFS Approved" && 
    //         $user->hasRole('registry') ) {
    //         return true;
    //     }

    //     return false;
    
    // }


    /**
    * Determine whether the user can circulate the file for approval/review
    */
    public function circulateToReviewOfficer(User $user, FileCirculation $fileCirculation): bool
    {
        $loggedInMinistryId = auth()->user()->ministry_id;
         
        $file = File::findOrFail($fileCirculation['file_id']);

        if (($fileCirculation->status === "Received" || $fileCirculation->status === "UFS Approved" || $fileCirculation->status === 'Pending SRO Submission') && 
            $fileCirculation->to_ministry_id === $loggedInMinistryId &&
            $user->hasRole('registry') ) {
            return true;
        }
        return false;
    }


    /**
    * Determine whether the user can approve/review the file circulated
    */
    public function colleagueReview(User $user, FileCirculation $fileCirculation): bool
    {
        if ($fileCirculation->status === "Pending Colleague Review" && 
            $fileCirculation->colleague_id === $user->id) {
            return true;
        }

        return false;
    }


    /**
    * Determine whether the user can approve/review the file circulated
    */
    public function review(User $user, FileCirculation $fileCirculation): bool
    {
        if ( ($fileCirculation->status === "Pending SRO Approval" || $fileCirculation->status === "Pending HOD Approval") && 
            $user->hasRole(['review-officer', 'hod'])) {
            return true;
        }

        return false;
    }


    // public function hodReview(User $user, FileCirculation $fileCirculation): bool
    // {
    //     if ($fileCirculation->status === "Pending Review" && 
    //         $fileCirculation->review_officer === $user->id &&
    //         $user->hasRole('hod')) {
    //         return true;
    //     }

    //     return false;
    // }


    /**
    * Determine whether the user can assign the file to officers
    */
    public function assign(User $user, FileCirculation $fileCirculation): bool
    {
        if (($fileCirculation->status === "Reviewed" || $fileCirculation->status === "Approved") && 
            $user->hasRole('registry') ) {
            return true;
        }
        return false;
    }


    /**
    * Determine whether the user can review and approve
    */
    // public function review(User $user, FileCirculation $fileCirculation): bool
    // {
    //     if (($fileCirculation->status === "Pending Review" || $fileCirculation->status === "Approved") && 
    //         $user->hasRole('registry')) {
    //         return true;
    //     }
    //     return false;
    // }

}
