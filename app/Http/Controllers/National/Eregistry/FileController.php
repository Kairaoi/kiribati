<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\ExternalPartner;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Ministry;
use App\Models\National\Eregistry\OrganisationType;
use App\Models\National\Eregistry\IdentityOrganisation;
use App\Models\National\Eregistry\MinistryArchivedFile;
use App\Models\National\Eregistry\MinistryClosedFile;
use App\Models\User;
use App\Repositories\National\Eregistry\CategoryRepository;
use App\Repositories\National\Eregistry\DispatchRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\ExternalPartnerRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use App\Repositories\National\Eregistry\IdentityOrganisationRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Services\FileReferenceService;
use App\Services\FileActionService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class FileController extends Controller
{
    private $files;
    private $users;
    private $externalPartners;
    private $organisation_types;
    private $identityOrganisations;
    private $file_types;
    private $divisions;
    private $categories;
    private $dispatches;
    private $fileCirculations;
    private $ministries;

    public function __construct(
        FileRepository $files,
        UserRepository $users,
        ExternalPartnerRepository $externalPartners,
        IdentityOrganisationRepository $identityOrganisations,
        OrganisationTypeRepository $organisation_types,
        FileTypeRepository $file_types,
        CategoryRepository $categories,
        DivisionRepository $divisions,
        DispatchRepository $dispatches,
        FileCirculationRepository $fileCirculations,
        MinistryRepository $ministries
    ) {

        $this->files = $files;
        $this->users = $users;
        $this->identityOrganisations = $identityOrganisations;
        $this->organisation_types = $organisation_types;
        $this->externalPartners = $externalPartners;
        $this->file_types = $file_types;
        $this->divisions = $divisions;
        $this->categories = $categories;
        $this->dispatches = $dispatches;
        $this->fileCirculations = $fileCirculations;
        $this->ministries = $ministries;
    }

    /**
     * Get files for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function getDataTables(Request $request)
    {
        Log::info('DataTables request', $request->all());
        $selectedType = $request->get('selected_type');
        $type = empty($selectedType)
                ? 'active'
                : $request->get('type', 'active');

         $fileType = $request->get('file_type');
         $category = $request->get('category');
        // $organisationId = $request->get('organisation_id'); // Get array of selected organisation IDs
        // $fromDate = $request->get('date_from');
        // $toDate = $request->get('date_to');

        // $query = $this->files->getForDataTable(auth()->user()->ministry_id, $type, $selectedType, $organisationId, $fromDate, $toDate);

        $query = $this->files->getForDataTable(auth()->user()->ministry_id, 
                                                $type, 
                                                $selectedType,
                                                $fileType,
                                                $category
                                            );

        return DataTables::of($query)
            ->editColumn('file_status', function ($row) {
                $userMinistryId = auth()->user()->ministry_id;

                if ($row->ministry_id == $userMinistryId && $row->circulation_ministry_id && $row->circulation_ministry_id == $userMinistryId) {
                    return $row->circulation_status;
                }

                if ($row->ministry_id == $userMinistryId) {
                    return $row->file_status;
                }

                return $row->circulation_status ?? 'Pending';
            })->make(true);
    }


    public function index($type = 'active')
    {
        // if (!auth()->user()->hasRole(['registry','admin'])) {
        //     abort(403, 'Unauthorized access');
        // }
        $ministryId = auth()->user()->ministry_id;
        $organisations = $this->identityOrganisations->listAll();
        $categories = $this->categories->listWithDescriptions();
        $file_types = $this->file_types->listWithMinistryTypes($ministryId);
        return view('national.eregistry.files.index', compact('type', 'organisations', 'categories', 'file_types'));
    }


    public function assignedIndex()
    {
        // if (!auth()->user()->hasRole(['registry','admin'])) {
        //     abort(403, 'Unauthorized access');
        // }

        return view('national.eregistry.files.assignedIndex');
    }



    public function getArchiveFiles(Request $request)
    {

        $organisationId = Auth::user()->organisation_id;
        $selectedType = $request->get('selected_type');
        $filterOrgIds = $request->get('organisation_ids', []); // Get array of selected organisation IDs
        $fromDate = $request->get('date_from');
        $toDate = $request->get('date_to');

        $query = $this->files->getForFilteredTable($selectedType, 
                                                   $organisationId, 
                                                   $filterOrgIds, 
                                                   $fromDate, $toDate);
        
        // Log::info('Archive query: '.$query->toSql(), $query->getBindings());
        
        return Datatables::of($query)->make(true);

    }
       

    /**
     * Show the form for creating a new file (for dispatch & circulation).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $ministryId = auth()->user()->ministry_id;

        $identityOrganisations = IdentityOrganisation::with('type')->orderBy('name')->get(); 
        $externalPartners = $this->externalPartners->list($ministryId);
        $ministryId = Auth::user()->ministry_id;
        $file_types = $this->file_types->listWithMinistryTypes($ministryId); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ministryId);
        $ministries = $this->ministries->list();
        $usersWithDivision = $this->users->getUsersDivision();

        $notMinistriesOrgs = $identityOrganisations->filter(function($org) {
            return $org->type->name !== 'Ministry';
        });
        
        return view('national.eregistry.files.create', compact('identityOrganisations',
                                                                'externalPartners',  
                                                                'ministries',  
                                                                'divisions',
                                                                'categories',
                                                                'file_types',
                                                                'notMinistriesOrgs',
                                                                'usersWithDivision'
        ));
    }


    /**
     * Create a new file record, and also create a new file circulation record for the sender ministry if it's an internal circulation file, or a new dispatch record if it's a dispatch file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'source_type' => 'required|in:identity_organisation,external_partner',
            'source_id' => 'required|integer',
            'document_source' => 'required|in:upload,online',
            'from_division_id' => 'nullable|exists:divisions,id',
            'subject' => 'required|string|max:255',
            'main_file' => 'required_if:document_source,upload|file|mimes:pdf|max:10240',
            'content' => [
                    'exclude_unless:document_source,online',
                    'required',
                    'string',
            ],
            'correspondence_type' => 'required_if:document_source,online|in:letter,internal,memo',
            'additional_files' => 'nullable|array|max:3',
            'additional_files.*' => 'file|mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx|max:10240',
            'file_type_id' => 'required|exists:file_types,id',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'memo_from_field' => 'nullable|string|max:255',
            'memo_cc_field' =>'nullable|string|max:255',
            'memo_attention_to' => 'nullable|string|max:255',
            'internal_from_field' => 'nullable|string|max:255',
            'internal_to_field' => 'nullable|string|max:255',
            'internal_cc_field' => 'nullable|string|max:255',
            'internal_ufs_id' => 'nullable|exists:users,id',
        ]);

        if (!$request->filled('content') && !$request->hasFile('main_file')) {
            return back()
                ->withErrors([
                    'content' => 'Please upload a file or write content online.',
                ])
                ->withInput();
        }

        if ($validated['document_source'] === 'online' && !$request->filled('content')) {
            return back()
                ->withErrors([
                    'content' => 'Content is required when document source is online.',
                ])
                ->withInput();
        }

        $map = [
            'identity_organisation' => IdentityOrganisation::class,
            'external_partner' => ExternalPartner::class,
        ];

        $validated['source_type'] = $map[$validated['source_type']];

        //generate reference number using the FileReferenceService
        $referenceNo = FileReferenceService::generate(
            auth()->user()->ministry_id,
            $request->file_type_id
        );

        $letterRecipients = [];
        $memoRecipients = [];
        $correspondenceType = $validated['correspondence_type'] ?? null;
        if ($correspondenceType !== null && $validated['correspondence_type'] === 'letter') {
                $request->validate([
                    'registered_organisations'   => ['nullable', 'array'],
                    'registered_organisations.*' => ['integer', 'exists:identity_organisations,id'],

                    'external_partners'   => ['nullable', 'array'],
                    'external_partners.*' => ['integer', 'exists:external_partners,id'],
                ]);

                $letterRecipients = [
                    'registered_organisations' => $request->registered_organisations ?? [],
                    'external_partners'        => $request->external_partners ?? [],
                ];

        } else if ($correspondenceType !== null && $validated['correspondence_type']=== 'memo') {
                $request->validate([
                    'memo_recipients'   => ['nullable', 'array'],
                    'memo_recipients.*' => ['integer', 'exists:ministries,id'],
                ]);
        }
        
        $memoRecipients = $request->memo_recipients ?? [];
        $mainFilePath = null;
        if ($request->hasFile('main_file')) {
            $mainFile = $request->file('main_file');
            $mainFilePath = $mainFile->store('uploads/main_files', 'public');
        }
       
            // Store up to 3 additional files
            $additionalFilePaths = [];

            if ($request->hasFile('additional_files')) {
                foreach ($request->file('additional_files') as $uploadedFile) {
                    $path = $uploadedFile->store('uploads/additional_files', 'public');
                    $additionalFilePaths[] = $path;
                }
            }
            // $status = $validated['document_source'] === 'online'
            //     ? 'Draft'
            //     : 'Pending Action';

            $correspondenceType = $validated['correspondence_type'] ?? null;
           
            $fileData = array_merge($validated, [
                'main_file_path' => $mainFilePath,
                'additional_file_paths' => $additionalFilePaths,
                'reference_no' => $referenceNo,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'letter_date' => now()->toDateString(),
                'ministry_id' => auth()->user()->ministry_id,
                'status' => 'Pending Action',

                'letter_recipients' => $correspondenceType === 'letter'
                    ? $letterRecipients
                    : [],

                'memo_recipients' => $correspondenceType === 'memo'
                    ? $memoRecipients
                    : [],
            ]);

            $file = File::create($fileData);
            
            activity('file')
                ->causedBy(auth()->user())
                ->performedOn($file)
                ->withProperties([
                    'file_name' => $file->name
                ])
                ->log('File created');

            Log::info('File successfully stored in database', ['file_id' => $file->id]);
            return redirect()->route('registry.files.index')->with('success', 'File created successfully!');
    }

    
    /**
     * Display the specified file.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(File $file)
    {
        $ministryId = auth()->user()->ministry_id;
        $fileId = $file->id;
        $closedRecord = DB::table('ministry_closed_files')
                ->join('users', 'ministry_closed_files.closed_by', '=', 'users.id')
                ->where('ministry_closed_files.file_id', $fileId)
                ->where('ministry_closed_files.ministry_id', $ministryId)
                ->select(
                    'ministry_closed_files.created_at',
                    'users.first_name as closed_by_name',
                    'users.last_name as closed_by_lastName',
                )
                ->first();

        $isClosed = !is_null($closedRecord);
        $closedBy = $closedRecord?->closed_by_name ?? 'Unknown';
        $closedDate = $closedRecord?->closed_at ?? null;

        $ministrySource = $file->isOwnedByMinistry(auth()->user()->ministry_id);
        // dd($ministrySource);
        // $fileCirculations = $this->fileCirculations->ministryCirculations($fileId, $ministryId)->latest()->get();
        $fileCirculations = $this->fileCirculations
                                ->ministryCirculations($fileId, $ministryId)
                                ->with('activeAssignments')
                                ->latest()
                                ->get();
        $circulation = $this->fileCirculations->thisCirculation($fileId, $ministryId);
    
        $fileAssignment = $circulation?->activeAssignments()->where('officer_id', Auth::id())->first(); // Get the file assignment for the logged-in user, if it exists
        
        $dispatchedMinistries = $fileCirculations->pluck('to_ministry_id')->unique()->toArray();
        $ministries = $this->ministries->list()
                                       ->where('id', '!=', $file->ministry_id)
                                       ->whereNotIn('id', $fileCirculations->pluck('to_ministry_id')->unique())
                                       ->values();
        $officers = $this->users->pluck();
        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
                                ->first();
        $usersWithDivision = $this->users->getUsersDivision();
        
        return view('national.eregistry.files.show', compact('file', 
                                                             'ministrySource', 
                                                             'fileId', 
                                                             'isClosed', 
                                                             'closedBy',
                                                             'closedDate', 
                                                             'ministryId', 
                                                             'ministries', 
                                                             'officers', 
                                                             'dispatchedMinistries', 
                                                             'reviewOfficer', 
                                                             'usersWithDivision', 
                                                             'fileCirculations',
                                                             'circulation', 
                                                             'fileAssignment'));
    }


    // public function show(File $file, FileActionService $fileActionService)
    // {
    //     $actions = $fileActionService->getActions($file, auth()->user());

    //     return view('files.show', compact(
    //         'file',
    //         'actions'
    //     ));
    // }


     /**
     * View the specified file.
     *
     * @param \App\Models\National\Eregistry\File $file
     * @return \Illuminate\Http\Response
     */
    public function viewFile($id)
    {
        $userOrgId = Auth::user()->ministry_id;

        $file = $this->files->getById($id);

        $circulation = $this->fileCirculations->thisCirculation($file->id, $file->ministry_id);

        if ($file->document_source === 'online') {
            /*
            |--------------------------------------------------------------------------
            | RETURN FINAL RENDERED PDF IF EXISTS
            |--------------------------------------------------------------------------
            */

            if ($circulation && $circulation->rendered_pdf_path && Storage::disk('public')->exists($circulation->rendered_pdf_path))
            {
                return Storage::disk('public')->response(
                    $circulation->rendered_pdf_path,
                    null,
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' .
                            basename($circulation->rendered_pdf_path) . '"'
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE LIVE TEMPLATE PREVIEW
            |--------------------------------------------------------------------------
            */

            $file->load('ministry');

            $templateView = match ($file->correspondence_type) {
                'memo'    => 'national.eregistry.files.pdf.templates.memo',
                'letter'  => 'national.eregistry.files.pdf.templates.letter',
                'internal'=> 'national.eregistry.files.pdf.templates.internal',
                default   => 'national.eregistry.files.pdf.templates.memo',
            };

            if ($file->correspondence_type === 'letter' && !empty($file->letter_recipients)) {
                 $recipientCopies = $file->correspondence_type === 'letter'
                    ? ($file->letter_recipient_copies ?? collect())
                    : collect();
                            

                $pdf = Pdf::loadView($templateView, [
                    'file' => $file,
                    'fileCirculation' => $circulation,
                    'recipientCopies' => $recipientCopies,
                ])->setPaper('a4', 'portrait');

                return $pdf->stream($file->reference_no . '.pdf');
            }

            /*
            |--------------------------------------------------------------------------
            | DEFAULT SINGLE PDF
            |--------------------------------------------------------------------------
            */

            $pdf = Pdf::loadView($templateView, [
                'file' => $file,
                'fileCirculation' => $circulation,
            ])->setPaper('a4', 'portrait');

            return $pdf->stream($file->reference_no . '.pdf');
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOADED FILE
        |--------------------------------------------------------------------------
        */

        if (!Storage::disk('public')->exists($file->main_file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->response($file->main_file_path, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' .
                basename($file->main_file_path) . '"'
        ]);
    }

    
    public function edit(File $file)
    {
        $this->authorize('update', $file);

        $ministryId = auth()->user()->ministry_id;

        $identityOrganisations = IdentityOrganisation::with('type')->orderBy('name')->get();        
        $externalPartners = $this->externalPartners->list($ministryId);
        $ministryId = Auth::user()->ministry_id;
        $file_types = $this->file_types->listWithMinistryTypes($ministryId); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ministryId);
        $ministries = $this->ministries->list();
        $usersWithDivision = $this->users->getUsersDivision();

        $notMinistriesOrgs = $identityOrganisations->filter(function($org) {
            return $org->type->name !== 'Ministry';
        });
        
        return view('national.eregistry.files.edit', compact('file',
                                                             'identityOrganisations',
                                                             'externalPartners',    
                                                             'divisions',
                                                             'categories',
                                                             'file_types',
                                                             'ministries',
                                                             'notMinistriesOrgs',
                                                             'usersWithDivision'
        ));

    }



    /**
     * Update the specified file in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\National\Eregistry\File $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, File $file)
    {
        $this->authorize('update', $file);
        $validated = $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'organisation_name' => 'required_if:organisation_id,null|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'subject' => 'required|string|max:255',
            'main_file' => 'nullable|file|mimes:pdf|max:10240',
            'additional_files' => 'nullable|array|max:3',
            'additional_files.*' => 'file|mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx|max:10240',
            'letter_ref_no' => 'nullable|string|unique:files,letter_ref_no,' . $file->id,
            'file_type_id' => 'required|exists:file_types,id',
            'category_id' => 'required|exists:categories,id',
            'recipient_organisations' => 'required|array',
            'recipient_organisations.*' => 'exists:organisations,id',
        ]);

        try {
            // Handle main file replacement if uploaded
            if ($request->hasFile('main_file')) {
                if ($file->main_file_path && \Storage::exists($file->main_file_path)) {
                    \Storage::delete($file->main_file_path);
                }
                $mainFilePath = $request->file('main_file')->store('uploads/main_files', 'public');
            } else {
                $mainFilePath = $file->main_file_path;
            }

            // Handle additional files (keep existing, add new)
            $existingFiles = $file->additional_file_paths ?? [];

            // Remove selected filesclear
            if ($request->filled('delete_additional_files')) {
                foreach ($request->delete_additional_files as $fileToDelete) {

                    // delete from storage
                    Storage::disk('public')->delete($fileToDelete);

                    // remove from array
                    $existingFiles = array_values(array_diff($existingFiles, [$fileToDelete]));
                }
            }

            if ($request->hasFile('additional_files')) {
                foreach ($request->file('additional_files') as $uploadedFile) {
                    $path = $uploadedFile->store('uploads/additional_files', 'public');
                    $existingFiles[] = $path; //append new files to the existing array of additional files
                }
            }

            // Build update data
            $updateData = array_merge(
                Arr::except($validated, ['recipient_organisations']),
                [
                    'main_file_path' => $mainFilePath,
                    'additional_file_paths' => $existingFiles, 
                    'updated_by' => auth()->id(),
                ]
            );

            // Update file record
            $file->update($updateData);

            // Sync recipient organisations
            $syncData = [];
            foreach ($validated['recipient_organisations'] as $organisationId) {
                $syncData[$organisationId] = ['status' => 'Pending Dispatch'];
            }
            $file->recipientMinistries()->sync($syncData);

            if($file->initial_type === 'dispatch') {
                if(auth()->user()->hasRole('user') || (auth()->user()->hasRole('admin')) ){
                    return redirect()->route('registry.dispatches.user.index')->with('success', 'Dispatch file edited successfully!');
                }

                if(auth()->user()->hasRole('registry')) {
                    return redirect()->route('registry.dispatches.index')->with('success', 'Dispatch file edited successfully!');
                }

            }else{
                return redirect()->route('registry.file-circulations.index')->with('success', 'Circulation file edited successfully!');
            }

        } catch (\Exception $e) {
            \Log::error('Error updating file', ['message' => $e->getMessage(), 'file_id' => $file->id]);
            return back()->withErrors(['error' => 'Error updating file: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Remove the specified file from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $file = $this->files->getById($id);
        // dd($file);
        $this->files->delete($file);
        return redirect()->route('registry.files.index')->with('message', 'File deleted successfully.');
    }


    /**
     * Download the specified file.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized access.');
        }
        $file = $this->files->getById($id);
        // dd($file->main_file_path);
        if (!$file->main_file_path) {
            abort(404, 'File path not set.');
        }
        if (!Storage::disk('local')->exists($file->main_file_path)) {
            abort(404, 'File not found.');
        }
        return Storage::disk('local')->download($file->main_file_path, basename($file->main_file_path));
    }


    public function downloadAdditionalFile($id, $number)
    {
        $userOrgId = Auth::user()->organisation_id;
        $file = $this->files->getById($id);
        $filePath = storage_path('app/private/' . $file->main_file_path);

        if($file->organisation_id === $userOrgId || in_array($userOrgId, $file->recipientMinistries->pluck('organisation_id')->toArray())) {
            $file = $this->files->getById($id);

            $additionalField = 'additional_file' . $number . '_path';
            if (!isset($file->$additionalField)) {
                abort(404, 'Invalid additional file number');
            }
            $filePath = storage_path('app/private/' . $file->$additionalField);

            if (!file_exists($filePath)) {
                abort(404, 'Additional file not found');
            }
            return response()->download($filePath);
        }

        abort(403, 'Unauthorized access.');
    }



    public function archive(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $file = File::findOrFail($request->file_id);

        MinistryArchivedFile::firstOrCreate(
            [
                'file_id' => $file->id,
                'ministry_id' => auth()->user()->ministry_id,
            ],
            [
                'archived_by' => auth()->id(),
                'archived_at' => now(),
            ]
        );
    }


    public function close(File $file)
    {

        MinistryClosedFile::firstOrCreate(
            [
                'file_id' => $file->id,
                'ministry_id' => auth()->user()->ministry_id,
            ],
            [
                'closed_by' => auth()->id(),
                'closed_at' => now(),
            ]
        );

         return redirect()->route('registry.files.index')->with('success', 'File closed successfully!');

    }


    public function viewAudit(File $file)
    {
        $file->load(['audits.user']);

        $dispatch = $this->dispatches->getById($file->id);
        $fileCirculations = $this->fileCirculations->ministryCirculations($file->id, auth()->user()->id)->latest()->get();
        // dd($fileCirculations);
        $dispatch->load(['audits.user']);

        return view('national.eregistry.files.audit', compact('file', 'dispatch', 'fileCirculations'));
    }



    public function sign(File $file)
    {
        $file->signature()->create([
            'signed_by'       => auth()->id(),
            'signed_name'     => auth()->user()->full_name,
            'signed_title'    => auth()->user()->ministry?->reviewer_title,
            'signed_ministry' => auth()->user()->ministry?->name,
            'signature_image' => auth()->user()->signature_path,
            'signed_at'       => now(),
        ]);

        $file->update([
            'status' => 'Signed',
        ]);

        return back()->with('success', 'File signed successfully.');
    }

}
