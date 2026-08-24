<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\FileAssignment;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\DocumentOverlay;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OfficerAssignedNotification;

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
    * Review and Assign officers
    */
    public function review(Request $request, $fileCirculationId)
    {

        $fileCirculation = FileCirculation::findOrFail($fileCirculationId);
        $this->authorize('review', $fileCirculation);

        $validated = $request->validate(
            [
                'comment' => 'nullable|string',
                'status' => 'required|in:Reviewed,Approved,Rejected',
                'officers' => ['nullable', 'array'],
                'officers.*' => ['exists:users,id'],
                'file_id' => ['required', 'exists:files,id'],
            ],
        );

        if (!empty($validated['officers'])) {
            foreach ($validated['officers'] as $officerId) {
                $file_assignment = FileAssignment::create([
                    'file_circulation_id' => $fileCirculationId,
                    'officer_id'          => $officerId,
                    'assigned_by'         => auth()->id(),
                    'assigned_date'       => now(),
                    'is_active'           => true,
                ]);

                $file_assignment->load([
                    'fileCirculation.file',
                    'assignedBy',
                    'officer'
                ]);

                $recipients = User::query()
                    ->where('id', $officerId)
                    ->where('is_active', true)
                    ->whereNotNull('email')
                    ->get();
                
                Notification::send(
                    $recipients,
                    new OfficerAssignedNotification($file_assignment)
                );

            }
        }
       
        $fileCirculation->update([
            'status' => $validated['status'],
            'review_comment' => $validated['comment'] ?? null,
            'reviewed_at' => now(),
            'updated_by' => auth()->id(),
            'approved_at' => $validated['status'] === 'Approved' ? now() : null,
            'approved_by' => $validated['status'] === 'Approved' ? auth()->id() : null,
            'reviewed_by' => auth()->id(),
            'date_reviewed' => now(),
        ]);

        $file = File::findOrFail($fileCirculation['file_id']);

        return redirect()->route('registry.files.show', $file);
    }


    /*
    * Only the currently assigned officer can accept or reassign the file to their officers
    */
    public function reassign(Request $request, $fileCirculationId)
    {
        // dd($request->all());
        $validated = $request->validate([
            'action' => ['required', 'in:reassign,accepted'],

            'tasks' => [
                'exclude_unless:action,reassign',
                'required',
                'array',
                'min:1',
            ],

            'tasks.*.officer_id' => [
                'exclude_unless:action,reassign',
                'required',
                'exists:users,id',
            ],

            'tasks.*.task' => [
                'exclude_unless:action,reassign',
                'required',
                'string',
                'max:1000',
            ],
        ]);
      
        $fileAssignment = FileAssignment::where('file_circulation_id', $fileCirculationId)
                                            ->where('officer_id', Auth::user()->id)
                                            ->where('is_active', true)
                                            ->firstOrFail();

        if($validated['action'] === 'accepted' && $fileAssignment->officer_id == Auth::user()->id) {
            $fileAssignment->update([
                'status' => 'in_progress',
                'accepted_at' => now(),
            ]);

            // dd('ikai');
            return back()->with('success', 'File marked as accepted');

        } elseif($validated['action'] === 'reassign') {
            $old_officer_id = $fileAssignment->officer_id;
            $fileAssignment->update([
                'status' => 'reassigned',
                'updated_at' => now(),
            ]);
            if (!empty($validated['tasks'])) {
                foreach ($validated['tasks'] as $task) {
                    FileAssignment::create([
                        'file_circulation_id' => $fileCirculationId,
                        'officer_id'          => $task['officer_id'],
                        'assigned_by'         => auth()->id(),
                        'assigned_date'       => now(),
                        'is_active'           => true,
                        'reassigned_from'     => $old_officer_id,
                        'task'                => $task['task'],
                    ]);
                }
            }
            
            return back()->with('success', 'Officers reassigned successfully');

        } else {
            return back()->with('error', 'Invalid action or you are not assigned to this file');
        }
    }


    public function accept(Request $request, $fileCirculationId)
    {
        // dd($request->all());
        $validated = $request->validate([
            'action' => ['required', 'in:accepted'],
        ]);
      
        $fileAssignment = FileAssignment::where('file_circulation_id', $fileCirculationId)
                                            ->where('officer_id', Auth::user()->id)
                                            ->where('is_active', true)
                                            ->firstOrFail();

        if($validated['action'] === 'accepted' && $fileAssignment->officer_id == Auth::user()->id) {
            $fileAssignment->update([
                'status' => 'in_progress',
                'accepted_at' => now(),
            ]);
            return back()->with('success', 'File marked as accepted');

        } else {
            return back()->with('error', 'Invalid action or you are not assigned to this file');
        }
    }


    /*
    * The reassigned officers will need to complete this file by adding a completion comment.
    */
    public function complete(Request $request, $fileCirculationId)
    {
        // dd($request->all());
        $validated = $request->validate([
            'assignee_comment' => 'required|string',
        ]);
      
        $fileAssignment = FileAssignment::where('file_circulation_id', $fileCirculationId)
                                            ->where('officer_id', Auth::user()->id)
                                            ->where('is_active', true)
                                            ->firstOrFail();

        // dd($fileAssignment);
        if($fileAssignment->officer_id == Auth::user()->id) {
            $fileAssignment->update([
                'status' => 'complete',
                'completed_at' => now(),
                'assigned_officer_comment' => $validated['assignee_comment'],
            ]);
            // dd($fileAssignment);
            return back()->with('success', 'File marked as completed');

        } else {
            return back()->with('error', 'Invalid action or you are not assigned to this file');
        }
    }
}
