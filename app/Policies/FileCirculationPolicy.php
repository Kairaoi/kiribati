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
        
        if($user->hasRole('registry') && 
           $fileCirculation->to_ministry_id === $user->ministry_id) {
            return true;
           }

        return false;
    }

    
    /**
     * Determine whether the user can view the circulation final reviews.
     */
    public function viewFinalReview(User $user, FileCirculation $fileCirculation): bool
    {
        $fileAssignment = $fileCirculation->activeAssignments()->where('officer_id', $user->id)->first(); // Get the file assignment for the logged-in user, if it exists
        
        if ($fileAssignment) {
            return true;
        }

        if (($fileCirculation->status === 'Reviewed' || $fileCirculation->status === 'Approved' || $fileCirculation->status === 'Rejected' || $fileCirculation->status === 'Pending SRO Approval') &&
            $fileCirculation->to_ministry_id === $user->ministry_id &&
            ($fileCirculation->created_by === $user->id|| $user->hasRole('registry') || $fileCirculation->reviewed_by === $user->id)
          
        ) {
            return true;
        }
        
        return false;
    }


    /**
     * Determine whether the circulation assignments.
     */
    public function viewAssignments(User $user, FileCirculation $fileCirculation): bool
    {
        if ( ($user->hasRole('registry') || $user->hasRole('review-officer') || $fileCirculation->reviewed_by === $user->id) && 
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
         
        if ($fileCirculation->to_ministry_id === $user->ministry_id && 
            $fileCirculation->file->ministry_id !== $user->ministry_id &&
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
    public function circulateToReviewOfficer(User $user, FileCirculation $fileCirculation): bool
    {

        if (($fileCirculation->status === "Received" || $fileCirculation->status === "UFS Approved" || $fileCirculation->status === 'Pending SRO Submission' || $fileCirculation->status === "Returned for Amendment" ) && 
            $fileCirculation->to_ministry_id === $user->ministry_id &&
            $user->hasRole('registry') ) {
            return true;
        }
        return false;
    }


    /**
    * Determine whether the user can approve/review the file circulated
    */
    public function hodReview(User $user, FileCirculation $fileCirculation): bool
    {
        if ($fileCirculation->status === "Pending HOD Review" && 
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

        //review a file from other ministries
        if ($fileCirculation->file->ministry_id !== $user->ministry_id &&
           ($fileCirculation->status === "Pending SRO Approval" || $fileCirculation->status === "Pending HOD Approval") && 
            $user->hasAnyRole(['review-officer', 'hod']) &&
            $fileCirculation->review_officer === $user->id) {
            return true;
        }

        //review an internal file 
        if ($fileCirculation->file->ministry_id === $user->ministry_id &&
           ($fileCirculation->file->correspondence_type === 'internal' || $fileCirculation->file->document_source === 'upload') &&
           ($fileCirculation->status === "Pending SRO Approval" || $fileCirculation->status === "Pending HOD Approval") && 
            $user->hasAnyRole(['review-officer', 'hod']) &&
            $fileCirculation->review_officer === $user->id) {
            return true;
        }

        return false;
    }


    /**
    * Determine whether the user can sign the model
    * Only ministry users can sign the model if the file is a memo/letter and the ministry of 
    * the file is the same as the user ministry
    */
    public function sign(User $user, File $fileCirculation): bool
    {

        if ($fileCirculation->file->ministry_id === $user->ministry_id &&
            $fileCirculation->review_officer === $user->id && 
            $fileCirculation->status === "Pending SRO Approval" &&
            ($fileCirculation->file->correspondence_type === 'memo' || $fileCirculation->file->correspondence_type === 'letter')) {

            return true;
        }

        return false;
    
    }


    /**
    * Determine whether the user can assign the file to officers
    */
    public function assign(User $user, FileCirculation $fileCirculation): bool
    {
        $file = File::findOrFail($fileCirculation['file_id']);

        $ministryId = $file->ministry_id;

        //cant assign a file to officers if the file is a memo/letter and the ministry of the file is the same as the user ministry - file can only be dispatched at this stage
        if (($file->correspondence_type === 'memo' || $file->correspondence_type === 'letter') && $ministryId === $user->ministry_id ) {
            return false;
        }

        if (($fileCirculation->status === "Reviewed" || $fileCirculation->status === "Approved") && 
            $user->hasRole('registry') ) {
            return true;
        }
        return false;
    }

}
