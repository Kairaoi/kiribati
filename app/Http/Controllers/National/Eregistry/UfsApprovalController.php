<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\FileAssignment;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\File;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UfsApprovalController extends Controller
{
    private $fileCirculations;
    private $divisions;
    private $ministries;
    private $users;
    private $files;

    public function __construct(DivisionRepository $divisions, 
                                MinistryRepository $ministries, 
                                UserRepository $users,
                                FileRepository $files,
                                FileCirculationRepository $fileCirculations)
    {
        $this->divisions = $divisions;
        $this->ministries = $ministries;
        $this->users = $users;
        $this->files = $files;
        $this->fileCirculations = $fileCirculations;    
    }


    public function approve(FileCirculation $fileCirculation)
    {
        abort_unless(auth()->id() === $fileCirculation->ufs_id, 403);

        // abort_unless($fileCirculation->initial_type === 'internal', 403);


        $fileCirculation->update([
            'status' => 'UFS Approved',
            'ufs_approved_by' => auth()->id(),
            'ufs_approved_at' => now(),
        ]);

        return back()->with('success', 'File approved and sent to Review Officer.');
    }


    public function reject(FileCirculation $fileCirculation)
    {
            abort_unless(auth()->id() === $fileCirculation->ufs_id, 403);

            // abort_unless($fileCirculation->initial_type === 'internal', 403);

            $fileCirculation->update([
                'status' => 'UFS Rejected',
                'ufs_rejected_by' => auth()->id(),
                'ufs_rejected_at' => now(),
            ]);

            return back()->with('success', 'File rejected.');
    }
  
  
}
