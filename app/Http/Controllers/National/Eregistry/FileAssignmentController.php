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
    * Assign multiple officers to a file circulation
    */
    public function assign(Request $request, $fileCirculationId)
    {
        // dd($request->all());
        $ministryId = auth()->user()->ministry_id;
    
        $validated = $request->validate([
            'officers' => ['nullable', 'array'],
            'officers.*' => ['nullable:users,id'],
            'comment' => 'nullable|string',
            'file_id' => 'required|exists:files,id',
            'status' => 'required|in:Reviewed,Approved,Rejected',
        ]);

        if (!auth()->user()->hasRole('review-officer')) {
            abort(403, 'Only a Review Officer can review the documents.');
        }

        $reviewOfficerId = auth()->id();

        if (!empty($validated['officers'])) {
            foreach ($validated['officers'] as $officerId) {
                FileAssignment::create([
                    'file_circulation_id' => $fileCirculationId,
                    'officer_id'          => $officerId,
                    'assigned_by'         => auth()->id(),
                    'assigned_date'       => now(),
                    'is_active'           => true,
                    'review_officer'      => $reviewOfficerId,
                ]);
            }
        }

        $fileCirculation = FileCirculation::findOrFail($fileCirculationId);
        $fileCirculation->update([
            'review_comment' => $validated['comment'] ?? null,
            'date_reviewed' => now(),
            'status' => $validated['status'],
            'updated_by' => auth()->id()
        ]);

        $file = File::findOrFail($validated['file_id']);

        if ($file->document_source === 'upload') {
            if ($validated['comment']) {
                DocumentOverlay::create([
                    'file_id' => $file->id,
                    'file_circulation_id' => $fileCirculation->id,
                    'page_number' => 1,
                    'overlay_type' => 'review_comment',
                    'content' => json_encode([
                        'status' => $validated['status'],
                        'comment' => $validated['comment'],
                        'date' => now()->format('d M Y'),
                        'reference' => $file->reference_no,

                        'signature_path' => auth()->user()->signature_path,
                        'approved_by' => auth()->user()->name,
                        'designation' => auth()->user()->designation,
                    ]),
                    'x_position' => 60,
                    'y_position' => 700,
                    'width' => 300,
                    'height' => 80,
                    'font_size' => 13,
                    'created_by' => auth()->id(),
                ]);
            }

            if (!empty($validated['status'])) {
                DocumentOverlay::create([
                    'file_id' => $file->id,
                    'file_circulation_id' => $fileCirculation->id,
                    'page_number' => 1,
                    'overlay_type' => 'status',
                    'content' => [
                        'status' => $validated['status'],
                        'label' => strtoupper($validated['status']),
                    ],
                    'x_position' => 60,
                    'y_position' => 700,
                    'width' => 300,
                    'height' => 80,
                    'font_size' => 20,
                    'created_by' => auth()->id(),
                ]);
            }

            return redirect()->route('registry.overlays.edit', $fileCirculation);
        }


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
