<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\FileAssignment;
use App\Models\National\Eregistry\FileCirculation;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FileAssignmentController extends Controller
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

    /*
    * Assign multiple officers to a file circulation
    */
    public function assign(Request $request, $fileCirculationId)
    {
    
        $validated = $request->validate([
            'officers' => ['required', 'array'],
            'officers.*' => ['exists:users,id'],
            'review_comment' => 'nullable|string',
            'file_id' => 'required|exists:files,id',
        ]);

        // dd($request->officers);

        foreach ($validated['officers'] as $officerId) {
            FileAssignment::create([
                'file_circulation_id' => $fileCirculationId,
                'officer_id'          => $officerId,
                'assigned_by'         => auth()->id(),
                'assigned_date'       => now(),
                'is_active'           => true,
            ]);
        }

        $fileCirculation = FileCirculation::findOrFail($fileCirculationId);
        $fileCirculation->update([
            'review_comment' => $validated['review_comment'] ?? null,
            'date_reviewed' => now(),
            'status' => 'Reviewed',
            'updated_by' => auth()->id()

        ]);

        $file = $this->files->getById($validated['file_id']);

      
        
        return redirect()->route('registry.files.index');
    }

    /*
    * Reassign an office by deactivating the old assignment and creating a new one
    * If the user accepts the assignment, update the status to 'received' and set the received_at timestamp
    * Only the currently assigned officer can accept or reassign the file
    */
    public function reassign(Request $request, $fileCirculationId)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:reassign,accepted'],
            'new_officer_id' => [
                'nullable',
                'required_if:action,reassign',
                Rule::exists('users', 'id')->where(fn ($q) =>
                    $q->where('division_id', auth()->user()->division_id)
                ),
            ]
        ]);

        $fileAssignment = FileAssignment::where('file_circulation_id', $fileCirculationId)
                                            ->where('officer_id', auth()->user()->id)
                                            ->where('is_active', true)
                                            ->firstOrFail();
        // dd($fileAssignment);
        // If the user accepts the assignment, update the status to 'received' and set the received_at timestamp
        if($request->action === 'accepted' && $fileAssignment->officer_id == auth()->user()->id) {
            // dd($fileAssignment);
            $fileAssignment->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
            return back()->with('success', 'File marked as accepted');

        } else if($request->action === 'reassign') {
            $old_officer_id = $fileAssignment->officer_id;

            // deactivate old assignment
            FileAssignment::where('file_circulation_id', $fileCirculationId)
                ->where('officer_id', $old_officer_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // create new assignment
            FileAssignment::create([
                'file_circulation_id' => $fileCirculationId,
                'officer_id'          => $validated['new_officer_id'],
                'assigned_by'         => auth()->id(),
                'assigned_date'       => now(),
                'is_active'           => true,
                'reassigned_from'     => $old_officer_id,
            ]);

            return back()->with('success', 'Officer reassigned successfully');

        } else {
            return back()->with('error', 'Invalid action or you are not assigned to this file');
        }
    }
}
