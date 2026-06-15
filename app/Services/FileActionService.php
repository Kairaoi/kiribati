<?php

namespace App\Services;

use App\Models\National\Eregistry\File;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class FileActionService
{

    private $fileCirculations;

    public function __construct(FileCirculationRepository $fileCirculations) {
        $this->fileCirculations = $fileCirculations; 
    }


    public function getAvailableActions(File $file, User $user): array
    {
        $actions = [];
        $closedRecord = DB::table('ministry_closed_files')
                ->join('users', 'ministry_closed_files.closed_by', '=', 'users.id')
                ->where('ministry_closed_files.file_id', $file->id)
                ->where('ministry_closed_files.ministry_id', $user->ministry_id)
                ->select(
                    'ministry_closed_files.created_at',
                    'users.first_name as closed_by_name',
                    'users.last_name as closed_by_lastName',

                )
                ->first();
        $isClosed = !is_null($closedRecord);
        $ownsFile = $file->ministry_id === $user->ministry_id;
        $createdByUser = $file->created_by === $user->id;

        $fileCirculations = $this->fileCirculations->ministryCirculations($fileId, $ministryId)->latest()->get();
        $circulation = $this->fileCirculations->thisCirculation($fileId, $ministryId);
    
        $fileAssignment = $circulation?->activeAssignments()->where('officer_id', Auth::id())->first(); // Get the file assignment for the logged-in user, if it exists
        
        
        if ($user->hasRole('registry') && ($ownsFile || $circulation?->to_ministry_id === $user->ministry_id ) && (!$fileAssignment || $fileAssignment->status === 'accepted'))
        {
            $actions[] = [
                'label' => 'Close File',
                'route' => route('registry.files.close', $file) ,
                'color' => 'gray',
            ];
        }

        if ($user->hasRole('registry') && $ownsFile && $file->status === 'Pending Action') {
            $actions[] = [
                'label' => 'Edit',
                'route' => route('registry.files.edit', $file),
                'color' => 'gray',
            ];

            $actions[] = [
                'label' => 'Delete',
                'route' => route('registry.files.destroy', $file),
                'color' => 'red',
                'method' => 'DELETE',
            ];

            $actions[] = [
                'label' => 'Submit for UFS',
                'route' => route('registry.files.submit', $file),
                'color' => 'blue',
            ];
        }

        if (($user->hasRole('registry') || $createdByUser) && $ownsFile && $file->status === 'UFS Approved') {
            $actions[] = [
                'label' => 'Submit for Approval',
                'route' => route('registry.files.submit', $file),
                'color' => 'blue',
            ];
        }

        if (($user->hasRole('registry') || $createdByUser) && $ownsFile && $file->status === 'UFS Rejected') {
            $actions[] = [
                'label' => 'Submit for Approval',
                'route' => route('registry.files.submit', $file),
                'color' => 'blue',
            ];
        }

        if ($user->hasRole('review-officer') && $file->status === 'Pending Review') {
            $actions[] = [
                'label' => 'Review',
                'route' => route('files.review', $file),
                'color' => 'cyan',
            ];
        }

        if ($user->hasRole(['admin', 'registry'])) {
            $actions[] = [
                'label' => 'View',
                'route' => route('files.show', $file),
                'color' => 'gray',
            ];
        }

        return $actions;
    }
}
