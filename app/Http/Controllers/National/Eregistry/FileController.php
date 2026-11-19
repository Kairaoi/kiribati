<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\ExternalPartner;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Organisation;
use App\Models\National\Eregistry\OrganisationClosedFile;
use App\Models\User;
use App\Repositories\National\Eregistry\CategoryRepository;
use App\Repositories\National\Eregistry\DispatchRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\ExternalPartnerRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Services\FileReferenceService;
use App\Services\FileActionService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\National\Eregistry\FileStoreRequest;
use App\Models\National\Eregistry\FileFolderAssignment;
use Illuminate\Database\Query\Builder;

class FileController extends Controller
{
    private $files;
    private $users;
    private $externalPartners;
    private $organisation_types;
    private $organisations;
    private $file_types;
    private $divisions;
    private $categories;
    private $dispatches;
    private $fileCirculations;
    private $folders;

    public function __construct(
        FileRepository $files,
        UserRepository $users,
        ExternalPartnerRepository $externalPartners,
        OrganisationRepository $organisations,
        OrganisationTypeRepository $organisation_types,
        FileTypeRepository $file_types,
        CategoryRepository $categories,
        DivisionRepository $divisions,
        DispatchRepository $dispatches,
        FileCirculationRepository $fileCirculations,
        FolderRepository $folders,
    ) {

        $this->files = $files;
        $this->users = $users;
        $this->organisations = $organisations;
        $this->organisation_types = $organisation_types;
        $this->externalPartners = $externalPartners;
        $this->file_types = $file_types;
        $this->divisions = $divisions;
        $this->categories = $categories;
        $this->dispatches = $dispatches;
        $this->fileCirculations = $fileCirculations;
        $this->folders = $folders;
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
    
        $type = $request->input('type', 'active');

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->files->getForDataTable($search, 
                                               Auth::user(), 
                                               Auth::user()->organisation_id, 
                                               $type, 
                                            );

        return DataTables::of($query)
            ->editColumn('file_status', function ($row) {
                $userOrgId = Auth::user()->organisation_id;
                
                //display the circulation status
                //if the row is an internal circulation
                if ($row->organisation_id == $userOrgId && 
                    $row->circulation_organisation_id && 
                    $row->circulation_organisation_id == $userOrgId) 
                {
                    return $row->circulation_status;
                }

                //display the file status if the user organisation is equal the file organisation
                if ($row->organisation_id == $userOrgId) {
                    return $row->file_status;
                }

                //if the user organisation is not equal to the file organisation
                //in this case - the dispatched organisations file circulation status is displayed
                return $row->circulation_status ?? 'Pending';

            })->make(true);
    }

    
    public function index($type = 'active')
    {

        $organisationId = Auth::user()->organisation_id;
        $organisations = $this->organisations->list();
        $categories = $this->categories->listWithDescriptions();
        $file_types = $this->file_types->listWithOrganisationTypes($organisationId);
        $ministries = $this->organisations->listMinistries();
        $externalPartners = $this->externalPartners->list($organisationId);
        $reviewOfficer = User::role('review-officer')
                                ->where('organisation_id', $organisationId)
                                ->first();

        $folders = $this->folders->getTopFolders($organisationId);
        
        $sro = User::role('sro')
                    ->where('organisation_id', Auth::user()->organisation_id)
                    ->first();

        $organisation = $this->organisations->getReviewerTitle(Auth::user()->organisation_id);

        return view('national.eregistry.files.index', compact('type', 
                                                              'organisations', 
                                                              'categories', 
                                                              'file_types',
                                                              'externalPartners',
                                                              'reviewOfficer',
                                                              'sro',
                                                              'organisation',
                                                              'folders'));
    }


    // public function getArchiveFiles(Request $request)
    // {

    //     $organisationId = Auth::user()->organisation_id;
    //     $selectedType = $request->get('selected_type');
    //     $filterOrgIds = $request->get('organisation_ids', []); // Get array of selected organisation IDs
    //     $fromDate = $request->get('date_from');
    //     $toDate = $request->get('date_to');

    //     $query = $this->files->getForFilteredTable($selectedType, 
    //                                                $organisationId, 
    //                                                $filterOrgIds, 
    //                                                $fromDate, $toDate);
        
    //     // Log::info('Archive query: '.$query->toSql(), $query->getBindings());
        
    //     return Datatables::of($query)->make(true);

    // }
       

    /**
     * Show the form for creating a new file (for dispatch & circulation).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $organisationId = Auth::user()->organisation_id;

        $ownOrganisationId = Auth::user()->organisation_id;

        $organisations = $this->organisations
            ->listAll()
            ->reject(function ($organisation) use ($ownOrganisationId) {
                return $ownOrganisationId !== null
                    && (string) $organisation->id === (string) $ownOrganisationId;
            })
            ->values();
        $externalPartners = $this->externalPartners->list($organisationId);
        $organisationId = Auth::user()->organisation_id;
        $file_types = $this->file_types->listWithOrganisationTypes($organisationId);

        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ownOrganisationId); // Get divisions for the user's organisation
        $ministries = $this->organisations->listMinistries();
        $usersWithDivision = $this->users->getDivisionUsers(Auth::user()->division_id);
       
        $notMinistriesOrgs = $organisations->filter(function($org) {
            return $org->type->name !== 'Ministry';
        });

        $currentReviewOfficer = User::role('review-officer')
            ->where('organisation_id', $organisationId)
            ->first();

        $sro = User::role('sro')
            ->where('organisation_id', $organisationId)
            ->first();

        $officerInCharge = $currentReviewOfficer?->id !== $sro?->id;
    
        $isRegistry = auth()->user()->hasRole('registry');
    
        return view('national.eregistry.files.create', compact('organisations',
                                                                'externalPartners',  
                                                                'ministries',  
                                                                'divisions',
                                                                'categories',
                                                                'file_types',
                                                                'notMinistriesOrgs',
                                                                'usersWithDivision',
                                                                'officerInCharge',
                                                                'organisationId',
                                                                'isRegistry'
        ));
    }


    /**
     * Create a new file record, and also create a new file circulation record for the sender ministry if it's an internal circulation file, or a new dispatch record if it's a dispatch file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FileStoreRequest $request)
    {

        $validated = $request->validated();

        $sourceType = $validated['source_type'];

        // Handle the case when the source type is "own_ministry"
        if ($sourceType === 'own_ministry') {
            $organisation = Organisation::findOrFail(Auth::user()->organisation_id);
            $validated['source_type'] = Organisation::class;
            $validated['source_id'] = $organisation->id;

        } else {
            $sourceTypeMap = [
                'identity_organisation' => Organisation::class,
                'external_partner'      => ExternalPartner::class,
            ];

            $validated['source_type'] = $sourceTypeMap[$sourceType];
        }

        //generate reference number using the FileReferenceService
        $referenceNo = FileReferenceService::generate(
            Auth::user()->organisation_id,
            $request->file_type_id
        );

        $letterRecipients = [];
        $memoRecipients = [];
        $correspondenceType = $validated['correspondence_type'] ?? null;

        if ($correspondenceType !== null && $validated['correspondence_type'] === 'letter') {
                $request->validate([
                    'registered_organisations'   => ['nullable', 'array'],
                    'registered_organisations.*' => ['integer', 'exists:organisations,id'],

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
                    'memo_recipients.*' => ['integer', 'exists:organisations,id'],
                ]);
        }else if ($correspondenceType !== null && $validated['correspondence_type']=== 'internal') {
                $request->validate([
                    'internal_ufs_id' => [
                        'required',
                        Rule::exists('users', 'id')->where(fn ($q) =>
                            $q->where('division_id', Auth::user()->division_id)
                        ),
                    ],
                ], [
                    'internal_ufs_id.required' => 'Please select a UFS officer.',
                    'internal_ufs_id.exists' => 'The selected UFS officer must belong to your division.',
                ]);
        }
        
        $memoRecipients = $request->memo_recipients ?? [];

        $mainFilePath = null;
        if ($request->hasFile('main_file')) {
            $mainFile = $request->file('main_file');
            $mainFilePath = $mainFile->store('uploads/main_files', 'public');
        }
       
        $additionalFilePaths = [];
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $uploadedFile) {
                $path = $uploadedFile->store('uploads/additional_files', 'public');

                $additionalFilePaths[] = [
                    'name' => $uploadedFile->getClientOriginalName(),
                    'file_path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),];
            }
        }
           
        $file = $this->files->createFile(
                validated: $validated,
                mainFilePath: $mainFilePath,
                additionalFilePaths: $additionalFilePaths,
                referenceNo: $referenceNo,
                letterRecipients: $letterRecipients,
                memoRecipients: $memoRecipients,
                userId: auth()->id(),
                organisationId: auth()->user()->organisation_id,
        );
            
        activity('file')
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->withProperties([
                'file_name' => $file->name
            ])
            ->log('File created');

        return redirect()->route('registry.files.index')->with('success', 'File created successfully!');
    }


    public function preview(File $file)
    {
        $path = storage_path('app/public/' . $file->main_file_path);

        abort_unless(file_exists($path), 404);

        return response()->file($path);
    }


    // public function download(File $file)
    // {
    //     abort_unless($file->main_file_path, 404);

    //     return Storage::disk('public')->download(
    //         $file->main_file_path,
    //         basename($file->main_file_path)
    //     );
    // }

    
    /**
     * Display the specified file.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(File $file)
    {
        $this->authorize('view', $file);

        //to display who dispatch the files
        $file->load('dispatches.dispatchedBy');

        $organisationId = Auth::user()->organisation_id;
        $closedRecord = DB::table('organisation_closed_files')
                ->join('users', 'organisation_closed_files.closed_by', '=', 'users.id')
                ->where('organisation_closed_files.file_id', $file->id)
                ->where('organisation_closed_files.organisation_id', $organisationId)
                ->select(
                    'organisation_closed_files.created_at',
                    'users.first_name as closed_by_name',
                    'users.last_name as closed_by_last_name',
                    'organisation_closed_files.remarks as remarks'
                )
                ->first();

        // dd($closedRecord);

        $isClosed = !is_null($closedRecord);
        $closeByFirstName = $closedRecord?->closed_by_name ?? '';
        $closeByLastName = $closedRecord?->closed_by_last_name ?? '';
        $closeRemarks = $closedRecord?->remarks ?? '-';
        $closeDate = $closedRecord?->closed_at ?? null;
    
        //get dispatch ministries circulations, not internal circulation
        $fileCirculations = $this->fileCirculations
                                ->organisationCirculations($file->id, $organisationId)
                                ->with([
                                    'toOrganisation:id,name',
                                    'dispatch:id,dispatch_date,dispatched_by',
                                    'dispatch.dispatchedBy:id,first_name,last_name',
                                ])
                                ->latest()
                                ->get();

        $dispatchedMinistries = $fileCirculations
                                ->filter(fn ($circulation) =>
                                    $circulation->dispatch !== null && $circulation->toOrganisation !== null
                                )
                                ->pluck('toOrganisation')
                                ->unique('id')
                                ->values();

        // dd($dispatchedMinistries);

        //get internal circulation 
        $circulation = $this->fileCirculations
                            ->thisCirculation($file->id, $organisationId)
                            ->with(['activeAssignments.officer:id,division_id,first_name,last_name',
                                    'activeAssignments.officer.division:id,name',
                                    'activeAssignments.assignedBy:id,first_name,last_name',
                            ])
                            ->first();
      
        // Get the file assignment for the logged-in user, if it exists
        $fileAssignment = $circulation?->activeAssignments->firstWhere('officer_id', Auth::id()); 

        
        $ministries = $this->organisations->listMinistries()
                                       ->where('id', '!=', $file->organisation_id)
                                       ->whereNotIn('id', $fileCirculations->pluck('to_organisation_id')->unique())
                                       ->values();


        $officers = $this->users->pluck();

        $hods = User::role('hod')
                    ->where('organisation_id', $organisationId)
                    ->get();
        // dd($hods);

        $reviewOfficer = User::role('review-officer')
                                ->where('organisation_id', $organisationId)
                                ->first();

        $usersWithDivision = $this->users->getUsersDivision();

        $divisionUsers = $this->users->getDivisionUsers(Auth::user()->division_id);

        $assignedOfficerIds = $circulation?->activeAssignments
            ->pluck('officer_id')
            ->toArray();

        $notAssignedOfficers = $usersWithDivision->whereNotIn('id', $assignedOfficerIds);
        

        $memoRecipients = $file->getMemoRecipients();
        $hod = Auth::user()->division?->hod;
        return view('national.eregistry.files.show', compact('file',
                                                             'hods', 
                                                             'isClosed', 
                                                             'closeByFirstName',
                                                             'closeByLastName',
                                                             'closeRemarks',
                                                             'closeDate', 
                                                             'organisationId', 
                                                             'ministries', 
                                                             'officers',  
                                                             'reviewOfficer', 
                                                             'usersWithDivision', 
                                                             'fileCirculations',
                                                             'circulation', 
                                                             'fileAssignment',
                                                             'notAssignedOfficers',
                                                             'divisionUsers',
                                                             'memoRecipients',
                                                             'hod',
                                                             'dispatchedMinistries'));
    }


    // public function show(File $file, FileActionService $fileActionService)
    // {
    //     $actions = $fileActionService->getActions($file, Auth::user());

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
    public function viewOnlineFile(File $file)
    {
        // $this->authorize('view', $file);

        if ($file->document_source !== 'online') {
            abort(403, 'This file is not available for online viewing.');
        }

        $circulation = $this->fileCirculations->thisCirculation($file->id, $file->ministry_id);
        
        //GENERATE LIVE TEMPLATE PREVIEW
            $file->load('organisation');
            // dd($file);

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
            } elseif ($file->correspondence_type === 'memo' && !empty($file->memo_recipients)) {
                $ministries = $this->organisations->listMinistries();

                $recipientIds = collect($file->memo_recipients ?? [])
                    ->map(fn ($id) => (int) $id);

                $allMinistryIds = $ministries
                    ->where('id', '!=', $file->ministry_id)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id);

                $isAllMinistries = $allMinistryIds->isNotEmpty()
                    && $recipientIds->sort()->values()->all()
                    === $allMinistryIds->sort()->values()->all();

                $recipients = $file->getMemoRecipients();

                $showRecipientListAtEnd = $recipients->count() > 6 && !$isAllMinistries;

                $pdf = Pdf::loadView('national.eregistry.files.pdf.templates.memo', [
                    'file' => $file,
                    'ministries' => $ministries,
                    'isAllMinistries' => $isAllMinistries,
                    'recipients' => $recipients,
                    'showRecipientListAtEnd' => $showRecipientListAtEnd 
                ])->setPaper('a4', 'portrait');

                 return $pdf->stream($file->reference_no . '.pdf');
            } 


            $pdf = Pdf::loadView($templateView, [
                'file' => $file,
                'fileCirculation' => $circulation,
            ])->setPaper('a4', 'portrait');

            return $pdf->stream($file->reference_no . '.pdf');
       
    }

    
    public function ufsCirculate(Request $request, File $file) 
    {
        $request->validate([
            'internal_ufs_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) =>
                    $q->where('division_id', Auth::user()->division_id)
                ),
            ],
        ], [
            'internal_ufs_id.required' => 'Please select a UFS officer.',
            'internal_ufs_id.exists' => 'The selected UFS officer must belong to your division.',
        ]);

        $organisationId = Auth::user()->organisation_id;

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_organisation_id' => $organisationId,
            ],
            [
                'circulated_by'  => auth()->id(),
                'circulated_at'  => now(),
                'updated_by'     => auth()->id(),
                'status'         => 'Pending UFS',
                'ufs_status'      => 'Pending',
            ]
        );

        $file->update([
            'status' => 'Pending UFS',
        ]);


        return redirect()->route('registry.files.index')->with('success', 'File circulated for UFS');
    }


    //sign and create final pdf 
    public function signFile(File $file) 
    {
        $file->update([
            'status'          => 'Pending finalisation',
            'signed_at' => now(),
            'signed_by' => Auth::user()->id,
        ]);
       
        return redirect()->route('registry.files.show', $file)->with('success', 'File signed');
    }

    public function finalisePdf(File $file) 
    {
         //if file is internal memo - set status to Pending UFS Circulation otherwise status is Pending Dispatch
        $status = $file->correspondence_type === 'internal' ? 'Pending UFS Circulation' : 'Pending Dispatch';

        $file->update([
            'signature_path' => Auth::user()->signature_path,
            'status' => $status,
            'signed_at' => now(),
            'signed_by' => Auth::user()->id,
        ]);

        $file->refresh();

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_organisation_id' => Auth::user()->organisation_id,
            ],
            [
                'status'         => $status,
                'reviewed_by'    => Auth::user()->id,
                'date_reviewed'  => now(),
                'updated_by'     => Auth::user()->id,
            ]
        );

        $templateView = match ($file->correspondence_type) {
            'memo' => 'national.eregistry.files.pdf.templates.memo',
            'letter' => 'national.eregistry.files.pdf.templates.letter',
            'internal' => 'national.eregistry.files.pdf.templates.internal',
            default => throw new \Exception(
                'Unsupported correspondence type: ' . $file->correspondence_type
            ),
        };

        if ($file->correspondence_type === 'memo') {
            $ministries = $this->organisations->listMinistries();
                
            $recipientIds = collect($file->memo_recipients ?? [])
                    ->map(fn ($id) => (int) $id);

            $allMinistryIds = $ministries
                    ->where('id', '!=', $file->ministry_id)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id);

            $isAllMinistries = $allMinistryIds->isNotEmpty()
                    && $recipientIds->sort()->values()->all()
                    === $allMinistryIds->sort()->values()->all();

            $recipients = $file->getMemoRecipients();

            $showRecipientListAtEnd = $recipients->count() > 6 && !$isAllMinistries;

            $pdf = Pdf::loadView($templateView, [
                'file' => $file,
                'ministries' => $ministries,
                'isAllMinistries' => $isAllMinistries,
                'showRecipientListAtEnd' => $showRecipientListAtEnd,
                'recipients' => $recipients

            ])->setPaper('a4', 'portrait');

            $pdfContent = $pdf->output();

            $finalPath = "final-files/memo/file-{$file->id}-final.pdf";

        } elseif ($file->correspondence_type === 'internal') {
            $pdf = Pdf::loadView($templateView, [
                        'file' => $file
            ])->setPaper('a4', 'portrait');

            $pdfContent = $pdf->output();

            $finalPath = "final-files/internal/file-{$file->id}-final.pdf";

        } elseif ($file->correspondence_type === 'letter') {
            $recipientCopies = $file->correspondence_type === 'letter'
                ? ($file->letter_recipient_copies ?? collect())
                : collect();

            $pdf = Pdf::loadView($templateView, [
                        'file' => $file,
                        'recipientCopies' => $recipientCopies,
            ])->setPaper('a4', 'portrait');

            $pdfContent = $pdf->output();

            $finalPath = "final-files/letter/file-{$file->id}-final.pdf";

        } else {
            return redirect()->back()->withErrors(['error' => 'Unknown correspondence type.']);
        }

        Storage::disk('public')->put($finalPath, $pdfContent);

        $file->update([
            'final_pdf_path'        => $finalPath,
            'final_pdf_rendered_at' => now(),
            'final_pdf_hash'        => hash('sha256', $pdfContent),
        ]);

        return redirect()->route('registry.files.show', $file)->with('success', 'File is signed and final PDF generated.');


    }


    public function cancelSignature(Request $request, File $file) 
    {

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $file->update([
            'status'          => 'Signature Cancelled',
            'updated_at'      => now(),
            'updated_by'      => Auth::user()->id,
            'signature_cancelled_at' => now(),
            'signature_cancelled_by' => Auth::user()->id,
            'signature_cancelled_comment' => $validated['comment'],
        ]);

        $file->refresh();

        // FileCirculation::updateOrCreate(
        //     [
        //         'file_id'        => $file->id,
        //         'to_organisation_id' => Auth::user()->organisation_id,
        //     ],
        //     [
        //         'updated_by'     => Auth::user()->id,
        //         'status'         => 'Signature Cancelled',
        //         'final_review_status' => 'Signature Cancelled',
        //         'review_comment' => $validated['comment'],
        //         'reviewed_by'    => Auth::user()->id,
        //         'date_reviewed'  => now(),
        //     ]
        // );

        return redirect()->route('registry.files.index')->with('success', 'Signature cancelled');
    }



    // public function storeInternalReviewer(Request $request, File $file) 
    // {
    
    //     $validated = $request->validate([
    //         'review_officer' => 'required|exists:users,id'
    //     ]);

    //     $ministryId = Auth::user()->ministry_id;
        
    //     $fileCirculation = FileCirculation::updateOrCreate(
    //         [
    //             'file_id'        => $file->id,
    //             'to_ministry_id' => $ministryId,
    //         ],
    //         [
    //             'circulated_by'  => Auth::user()->id,
    //             'circulated_at'  => now(),
    //             'updated_by'     => Auth::user()->id,
    //             'status'         => 'Pending Review',
    //             'review_officer' => $validated['review_officer']
    //         ]
    //     );
        
    //     return redirect()->route('registry.files.index')->with('success', 'File circulated ');
    // }


    
    public function edit(File $file)
    {
        $this->authorize('update', $file);

        $organisationId = Auth::user()->organisation_id;

        $ownOrganisationId = Auth::user()->organisation_id;

        $organisations = $this->organisations
            ->listAll()
            ->reject(function ($organisation) use ($ownOrganisationId) {
                return $ownOrganisationId !== null
                    && (string) $organisation->id === (string) $ownOrganisationId;
            })
            ->values();
            
        $externalPartners = $this->externalPartners->list($organisationId);
        $organisationId = Auth::user()->organisation_id;
        $file_types = $this->file_types->listWithOrganisationTypes($organisationId);

        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ownOrganisationId); // Get divisions for the user's organisation
        $ministries = $this->organisations->listMinistries();
        $usersWithDivision = $this->users->getDivisionUsers(Auth::user()->division_id);
       
        $notMinistriesOrgs = $organisations->filter(function($org) {
            return $org->type->name !== 'Ministry';
        });

        $currentReviewOfficer = User::role('review-officer')
            ->where('organisation_id', $organisationId)
            ->first();

        $sro = User::role('sro')
            ->where('organisation_id', $organisationId)
            ->first();

        $officerInCharge = $currentReviewOfficer?->id !== $sro?->id;
    
        $isRegistry = auth()->user()->hasRole('registry');
    
        return view('national.eregistry.files.edit', compact('file',
                                                            'organisations',
                                                            'externalPartners',  
                                                            'ministries',  
                                                            'divisions',
                                                            'categories',
                                                            'file_types',
                                                            'notMinistriesOrgs',
                                                            'usersWithDivision',
                                                            'officerInCharge',
                                                            'organisationId',
                                                            'isRegistry'));

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
                if ($file->main_file_path && Storage::exists($file->main_file_path)) {
                    Storage::delete($file->main_file_path);
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
                if(Auth::user()->hasRole('user') || (Auth::user()->hasRole('admin')) ){
                    return redirect()->route('registry.dispatches.user.index')->with('success', 'Dispatch file edited successfully!');
                }

                if(Auth::user()->hasRole('registry')) {
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

    public function destroy(File $file)
    {
        $this->authorize('delete', $file);

        $paths = [$file->main_file_path];

        foreach ($file->additional_file_paths ?? [] as $attachment) {
            $paths[] = $attachment['file_path'] ?? null;
        }

        $paths = array_values(array_unique(array_filter(
            $paths,
            fn ($path) => is_string($path) && trim($path) !== ''
        )));

        // dd([
        //     'attachments' => $file->additional_file_paths,
        //     'raw_attachments' => $file->getRawOriginal('additional_file_paths'),
        //     'paths_to_delete' => $paths,
        // ]);

        // If repository returns false on failure, stop before touching storage.
        if ($this->files->deleteById($file->id) === false) {
            return back()->with('error', 'The file record could not be deleted.');
        }

        try {
            if ($paths !== [] && !Storage::disk('public')->delete($paths)) {
                throw new \RuntimeException("Upload cleanup failed for file {$file->id}.");
            }
        } catch (\Exception $exception) {
            report($exception);

            return redirect()->route('registry.files.index')
                ->with('warning', 'File record deleted, but some uploads could not be removed.');
        }

        return redirect()->route('registry.files.index')
            ->with('success', 'File and its uploads deleted.');
    }

    /**
     * Download the specified file.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // public function download($id)
    // {
    //     if (!auth()->check()) {
    //         abort(403, 'Unauthorized access.');
    //     }
    //     $file = $this->files->getById($id);
    //     // dd($file->main_file_path);
    //     if (!$file->main_file_path) {
    //         abort(404, 'File path not set.');
    //     }
    //     if (!Storage::disk('local')->exists($file->main_file_path)) {
    //         abort(404, 'File not found.');
    //     }
    //     return Storage::disk('local')->download($file->main_file_path, basename($file->main_file_path));
    // }


    public function downloadAdditionalFile(File $file, int $number): StreamedResponse
    {
        $this->authorize('view', $file);

        $additionalFiles = $file->additional_file_paths ?? [];

        abort_unless(isset($additionalFiles[$number]), 404, 'Additional file not found.');

        $document = $additionalFiles[$number];

        // Supports the new array structure.
        if (is_array($document)) {
            $path = $document['file_path'] ?? null;
            $downloadName = $document['name'] ?? ($path ? basename($path) : null);
        } else {
            // Supports older records where only the path was stored.
            $path = $document;
            $downloadName = basename($path);
        }

        abort_unless($path, 404, 'Additional file path is missing.');

        abort_unless(
            Storage::disk('public')->exists($path),
            404,
            'Additional file does not exist in storage.'
        );

        return Storage::disk('public')->download($path, $downloadName);
    }


    // public function archive(Request $request)
    // {
    //     $request->validate([
    //         'file_id' => 'required|exists:files,id',
    //     ]);

    //     $file = File::findOrFail($request->file_id);

    //     MinistryArchivedFile::firstOrCreate(
    //         [
    //             'file_id' => $file->id,
    //             'ministry_id' => Auth::user()->ministry_id,
    //         ],
    //         [
    //             'archived_by' => auth()->id(),
    //             'archived_at' => now(),
    //         ]
    //     );
    // }


    public function close(Request $request, File $file)
    {
        $validated = $request->validate([
            'close_remarks' => ['required', 'string', 'max:1000'],
        ]);
        
        OrganisationClosedFile::firstOrCreate(
            [
                'file_id' => $file->id,
                'organisation_id' => Auth::user()->organisation_id,
            ],
            [
                'closed_by' => auth()->id(),
                'closed_at' => now(),
                'remarks' => $validated['close_remarks'],
            ]
        );

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_organisation_id' => auth()->user()->organisation_id,
            ],
            [
                'updated_by'     => auth()->id(),
                'status'         => 'Closed',
            ]
        );


        return redirect()->route('registry.files.index')->with('success', 'File moved to Closed Files!');

    }


    public function fileToFolder(File $file, Request $request)
    {
        $organisationId = Auth::user()->organisation_id;

        $validated = $request->validate([
            'folder_id' => 'required',
                           'integer',
                           Rule::exists('folders', 'id')
                                ->where(fn (Builder $query) =>
                                    $query->where('organisation_id', $organisationId)
                                            ->where('is_active', true)
                            ),
        ]);


        FileFolderAssignment::firstOrCreate(
            [
                'file_id' => $file->id,
                'organisation_id' => $organisationId,
            ],
            [
                'folder_id' => $validated['folder_id'],
                'filed_by' => auth()->id(),
                'filed_at' => now(),
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]
        );
        

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_organisation_id' => auth()->user()->organisation_id,
            ],
            [
                'updated_by'     => auth()->id(),
                'status'         => 'Filed',
            ]
        );


        return response()->json(['message' => 'Document filed successfully.',]);
    }


    public function viewAudit(File $file)
    {
        $file->load(['audits.user']);

        $dispatch = $this->dispatches->getById($file->id);
        $fileCirculations = $this->fileCirculations->ministryCirculations($file->id, Auth::user()->id)->latest()->get();
        // dd($fileCirculations);
        $dispatch->load(['audits.user']);

        return view('national.eregistry.files.audit', compact('file', 'dispatch', 'fileCirculations'));
    }
   

    public function submitForSroSignature(Request $request, File $file) 
    {
        $validated = $request->validate([
            'review_officer_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $organisationId = Auth::user()->organisation_id;

        $reviewOfficer = User::role('review-officer')
            ->where('organisation_id', $organisationId)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->find($validated['review_officer_id']);

        if (! $reviewOfficer) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'review_officer_id' => 'The selected user is not an eligible review officer for your organisation.',
            ]);
        }

        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id' => $file->id,
                'to_organisation_id' => $organisationId,
            ],
            [
                'circulated_by' => auth()->id(),
                'circulated_at' => now(),
                'updated_by' => auth()->id(),
                'status' => 'Pending SRO Signature',
                'review_officer' => $reviewOfficer?->id,
            ]
        );

        $fileCirculation->file->update([
            'status' => 'Pending SRO Signature',
        ]);

        return redirect()->route('registry.files.index')->with('success', 'File submitted for SRO review.');
    }


    public function submitForHodSignature(File $file) 
    {
        $divisionId = Auth::user()->division_id;

        $hod = User::role('hod')
                ->where('division_id', $divisionId)
                ->first();
        
        if (!$hod) {
            return back()
                ->withInput()
                ->with('error','No Head of Division (HOD) has been assigned to your division. Please contact your administrator.',
            );
        }

        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id' => $file->id,
                'to_organisation_id' => auth()->user()->organisation_id,
            ],
            [
                'circulated_by' => auth()->id(),
                'circulated_at' => now(),
                'updated_by' => auth()->id(),
                'status' => 'Pending HOD Signature',
                'hod_reviewer' => $hod->id
            ]
        );


        $fileCirculation->file->update([
            'status' => 'Pending HOD Signature',
        ]);


        return redirect()->route('registry.files.index')->with('success', 'File submitted for HOD signature.');
    }

    public function submitForSroReview(Request $request, File $file) 
    {
        $validated = $request->validate([
            'review_officer_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $organisationId = Auth::user()->organisation_id;

        $reviewOfficer = User::role('review-officer')
            ->where('organisation_id', $organisationId)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->find($validated['review_officer_id']);

        if (! $reviewOfficer) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'review_officer_id' => 'The selected user is not an eligible review officer for your organisation.',
            ]);
        }

        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id' => $file->id,
                'to_organisation_id' => $organisationId,
            ],
            [
                'circulated_by' => auth()->id(),
                'circulated_at' => now(),
                'updated_by' => auth()->id(),
                'status' => 'Pending SRO Review',
                'final_review_status' => 'Pending SRO Review',
                'review_officer' => $reviewOfficer?->id,
            ]
        );

        $fileCirculation->file->update([
            'status' => 'Pending SRO Review',
        ]);

        return redirect()->route('registry.files.index')->with('success', 'File submitted for SRO review.');
    }

}
