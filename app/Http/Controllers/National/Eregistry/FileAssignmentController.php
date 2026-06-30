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
    * Review a file circulation - (HOD & SRO)
    */
    public function review(Request $request, $fileCirculationId)
    {
        if (!auth()->user()->hasRole(['hod','sro'])) {
            abort(403, 'Not authorised can review this document.');
        }
        // dd($request->all());
        $ministryId = auth()->user()->ministry_id;
    
        $validated = $request->validate([
            'comment' => 'nullable|string',
            'file_id' => 'required|exists:files,id',
            'status' => 'required|in:Reviewed,Approved,Rejected',
        ]);

        $reviewOfficerId = auth()->id();

        $fileCirculation = FileCirculation::findOrFail($fileCirculationId);
        $fileCirculation->update([
            'approval_comment' => $validated['comment'] ?? null,
            'status' => $validated['status'],
            'approved_by' => auth()->user()->id,
            'approved_at' => now(),
            'updated_by' => auth()->id()
        ]);

        $file = File::findOrFail($validated['file_id']);

        if (auth()->user()->signature_path) {
            // 1. Store signature details first
            $fileCirculation->update([
                'signed_by'       => auth()->id(),
                'signature_path'  => auth()->user()->signature_path,
                'signed_at'       => now(),
                'updated_by'      => auth()->id(),
            ]);

            // 2. Refresh so PDF blade receives updated signature_path
            $fileCirculation->refresh();

            // 3. Choose template
            $view = $file->correspondence_type === 'memo'
                ? 'national.eregistry.files.pdf.templates.memo'
                : 'national.eregistry.files.pdf.templates.letter';

            // 4. Get recipients only for letters
            $recipientCopies = $file->correspondence_type === 'letter'
                ? ($file->letter_recipient_copies ?? collect())
                : collect();

            // 5. Generate PDF after signature is saved
            $pdf = Pdf::loadView($view, [
                'file'             => $file,
                'fileCirculation'  => $fileCirculation,
                'recipientCopies'  => $recipientCopies,
                'signedUser'       => auth()->user(),
            ]);

            $pdfPath = 'rendered-files/file-' . $file->id .
                '/circulation-' . $fileCirculation->id . '.pdf';

            Storage::disk('public')->put($pdfPath, $pdf->output());

            // 6. Save final rendered PDF path
            $fileCirculation->update([
                'rendered_pdf_path' => $pdfPath,
                'rendered_pdf_at'   => now(),
                'updated_by'        => auth()->id(),
            ]);
        }

        return redirect()->route('registry.files.index');
    }


    /*
    * Assign officers
    */
    public function assign(Request $request, $fileCirculationId)
    {

        $fileCirculation = FileCirculation::findOrFail($fileCirculationId);
        
        // $this->authorize('assign', $fileCirculation);

        $ministryId = auth()->user()->ministry_id;
    
        $validated = $request->validate(
            [
                'officers' => ['required', 'array'],
                'officers.*' => ['exists:users,id'],
                'file_id' => ['required', 'exists:files,id'],
            ],
            [
                'officers.required' => 'Please select at least one officer.',
                'officers.array' => 'Please select one or more officers.',
                'officers.*.exists' => 'One or more selected officers are invalid.',
            ]
        );

        if (!empty($validated['officers'])) {
            foreach ($validated['officers'] as $officerId) {
                FileAssignment::create([
                    'file_circulation_id' => $fileCirculationId,
                    'officer_id'          => $officerId,
                    'assigned_by'         => auth()->id(),
                    'assigned_date'       => now(),
                    'is_active'           => true,
                ]);
            }
        }
       
        $fileCirculation->update([
            'updated_by' => auth()->id()
        ]);

        $file = File::findOrFail($fileCirculation['file_id']);

        return redirect()->route('registry.files.show', $file);
    }

    /*
    * Reassign an office by deactivating the old assignment and creating a new one
    * If the user accepts the assignment, update the status to 'received' and set the received_at timestamp
    * Only the currently assigned officer can accept or reassign the file
    */
    public function reassign(Request $request, $fileCirculationId)
    {
        
        $validated = $request->validate([
            'reassign_comment' => 'required|string',
            'action' => ['required', 'in:reassign,accepted'],
            'officers' => ['nullable', 'array'],
            'officers.*' => ['exists:users,id'],
        ]);
      
        $fileAssignment = FileAssignment::where('file_circulation_id', $fileCirculationId)
                                            ->where('officer_id', auth()->user()->id)
                                            ->where('is_active', true)
                                            ->firstOrFail();

        // dd($fileAssignment);
        if($validated['action'] === 'accepted' && $fileAssignment->officer_id == auth()->user()->id) {
            $fileAssignment->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
            return back()->with('success', 'File marked as accepted');

        } else if($validated['action'] === 'reassign') {
            // $fileAssignment->update([
            //     'status' => 'reassigned',
            // ]);

            $old_officer_id = $fileAssignment->officer_id;

            if (!empty($validated['officers'])) {
                foreach ($validated['officers'] as $officerId) {
                    FileAssignment::create([
                        'file_circulation_id' => $fileCirculationId,
                        'officer_id'          => $officerId,
                        'assigned_by'         => auth()->id(),
                        'assigned_date'       => now(),
                        'is_active'           => true,
                        'reassigned_from'     => $old_officer_id,
                        'reassign_comment'    => $validated['reassign_comment']
                    ]);
                }
            }
            // dd($file);
            return back()->with('success', 'Officers reassigned successfully');

        } else {
            return back()->with('error', 'Invalid action or you are not assigned to this file');
        }
    }
}
