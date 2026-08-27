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
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $ministry = $request->get('ministry_id');
        $partner = $request->get('partner');
        $organisationId = $request->get('organisation_id');

        $fromDate = $request->get('date_from');
        $toDate = $request->get('date_to');

        // $query = $this->files->getForDataTable(Auth::user()->ministry_id, $type, $selectedType, $organisationId, $fromDate, $toDate);

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->files->getForDataTable($search, Auth::user()->ministry_id, 
                                                $type, 
                                                $selectedType,
                                                $fileType,
                                                $category,
                                                $ministry,
                                                $organisationId,
                                                $partner,
                                                $fromDate,
                                                $toDate
                                            );

        return DataTables::of($query)
            ->editColumn('file_status', function ($row) {
                $userMinistryId = Auth::user()->ministry_id;

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

        $ministryId = Auth::user()->ministry_id;
        $organisations = $this->identityOrganisations->listNotMinistries();
        $categories = $this->categories->listWithDescriptions();
        $file_types = $this->file_types->listWithMinistryTypes($ministryId);
        $ministries = $this->ministries->list();
        $externalPartners = $this->externalPartners->list($ministryId);

        return view('national.eregistry.files.index', compact('type', 
                                                              'organisations', 
                                                              'categories', 
                                                              'file_types',
                                                              'externalPartners',
                                                              'ministries'));
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
        $ministryId = Auth::user()->ministry_id;

        $ownOrganisationId = Auth::user()->ministry?->identityOrganisation?->id;

        $identityOrganisations = $this->identityOrganisations
            ->listAll()
            ->reject(function ($organisation) use ($ownOrganisationId) {
                return $ownOrganisationId !== null
                    && (string) $organisation->id === (string) $ownOrganisationId;
            })
            ->values();
        $externalPartners = $this->externalPartners->list($ministryId);
        $ministryId = Auth::user()->ministry_id;
        $file_types = $this->file_types->listWithMinistryTypes($ministryId); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ministryId);
        $ministries = $this->ministries->list();
        $usersWithDivision = $this->users->getDivisionUsers(Auth::user()->division_id);
       
        $notMinistriesOrgs = $identityOrganisations->filter(function($org) {
            return $org->type->name !== 'Ministry';
        });

        $currentReviewOfficer = User::role('review-officer')
            ->where('ministry_id', $ministryId)
            ->first();

        $sro = User::role('sro')
            ->where('ministry_id', $ministryId)
            ->first();

        $officerInCharge = $currentReviewOfficer->id === $sro->id;
    
        return view('national.eregistry.files.create', compact('identityOrganisations',
                                                                'externalPartners',  
                                                                'ministries',  
                                                                'divisions',
                                                                'categories',
                                                                'file_types',
                                                                'notMinistriesOrgs',
                                                                'usersWithDivision',
                                                                'officerInCharge'
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
            'source_type' => 'required|in:identity_organisation,external_partner,own_ministry',
            'source_id' => 'required_unless:source_type,own_ministry|integer',
            'document_source' => [
                'required',
                Rule::in(
                    request('source_type') === 'own_ministry'
                        ? ['upload', 'online']
                        : ['upload']
                ),
            ],
            'from_division_id' => 'nullable|exists:divisions,id',
            'subject' => 'required|string|max:255',
            'file_type_id' => 'required|exists:file_types,id',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'additional_files' => 'nullable|array|max:3',
            'additional_files.*' => 'file|mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx|max:10240',
            'main_file' => 'exclude_unless:document_source,upload|required|file|mimes:pdf,jpg,jpeg,png,gif,webp,tif,tiff|max:10240',
            'content' => [
                    'exclude_unless:document_source,online',
                    'required',
                    'string',
            ],
            'correspondence_type' => 'exclude_unless:document_source,online|required|in:letter,internal,memo',
            'memo_from_field' => [
                'exclude_unless:correspondence_type,memo',
                'required',
                'string',
                'max:255',
            ],

            'memo_cc_field' => [
                'exclude_unless:correspondence_type,memo',
                'nullable',
                'string',
                'max:255',
            ],

            'memo_attention_to' => [
                'exclude_unless:correspondence_type,memo',
                'nullable',
                'string',
                'max:255',
            ],

            'internal_from_field' => [
                'exclude_unless:correspondence_type,internal',
                'required',
                'string',
                'max:255',
            ],

            'internal_to_field' => [
                'exclude_unless:correspondence_type,internal',
                'required',
                'string',
                'max:255',
            ],

            'internal_cc_field' => [
                'exclude_unless:correspondence_type,internal',
                'nullable',
                'string',
                'max:255',
            ],

            'internal_ufs_id' => [
                'exclude_unless:correspondence_type,internal',
                'required',
                'exists:users,id',
            ],
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

        $sourceType = $validated['source_type'];

        // Handle the case when the source type is "own_ministry"
        if ($sourceType === 'own_ministry') {
            $ministry = Ministry::findOrFail(Auth::user()->ministry_id);
            $validated['source_type'] = IdentityOrganisation::class;
            $validated['source_id'] = $ministry->identity_organisation_id;

        } else {
            $sourceTypeMap = [
                'identity_organisation' => IdentityOrganisation::class,
                'external_partner'      => ExternalPartner::class,
            ];

            $validated['source_type'] = $sourceTypeMap[$sourceType];
        }

        //generate reference number using the FileReferenceService
        $referenceNo = FileReferenceService::generate(
            Auth::user()->ministry_id,
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
       
            // Store up to 3 additional files
            $additionalFilePaths = [];

            if ($request->hasFile('additional_files')) {
                foreach ($request->file('additional_files') as $uploadedFile) {
                    $path = $uploadedFile->store('uploads/additional_files', 'public');

                    $additionalFilePaths[] = [
                        'name' => $uploadedFile->getClientOriginalName(),
                        'file_path' => $path,
                        'created_at' => now(),
                    ];
                }
            }

           
            $correspondenceType = $validated['correspondence_type'] ?? null;
           
            $fileData = array_merge($validated, [
                'main_file_path' => $mainFilePath,
                'additional_file_paths' => $additionalFilePaths,
                'reference_no' => $referenceNo,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'letter_date' => now()->toDateString(),
                'ministry_id' => Auth::user()->ministry_id,
                'status' => $validated['document_source'] === 'online' ? 'Pending Signature' : 'Pending Action',

                'letter_recipients' => $correspondenceType === 'letter'
                    ? $letterRecipients
                    : [],

                'memo_recipients' => $correspondenceType === 'memo'
                    ? $memoRecipients
                    : [],

                ''
            ]);

            $file = File::create($fileData);
            
            activity('file')
                ->causedBy(Auth::user())
                ->performedOn($file)
                ->withProperties([
                    'file_name' => $file->name
                ])
                ->log('File created');

            Log::info('File successfully stored in database', ['file_id' => $file->id]);
            return redirect()->route('registry.files.index')->with('success', 'File created successfully!');
    }


    public function preview(File $file)
    {
        $path = storage_path('app/public/' . $file->main_file_path);

        abort_unless(file_exists($path), 404);

        return response()->file($path);
    }


    public function download(File $file)
    {
        abort_unless($file->main_file_path, 404);

        return Storage::disk('public')->download(
            $file->main_file_path,
            basename($file->main_file_path)
        );
    }

    
    /**
     * Display the specified file.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(File $file)
    {
        $this->authorize('view', $file);

        $ministryId = Auth::user()->ministry_id;
        $closedRecord = DB::table('ministry_closed_files')
                ->join('users', 'ministry_closed_files.closed_by', '=', 'users.id')
                ->where('ministry_closed_files.file_id', $file->id)
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
    
        //get dispatch ministries circulations, not internal circulation
        $fileCirculations = $this->fileCirculations
                                    ->ministryCirculations($file->id, $ministryId)
                                    ->with([
                                        'toMinistry:id,name',
                                        'dispatch:id,dispatch_date',
                                    ])
                                    ->latest()
                                    ->get();

        //get internal circulation 
        $circulation = $this->fileCirculations
                            ->thisCirculation($file->id, $ministryId)
                            ->with(['activeAssignments.officer:id,division_id,first_name,last_name',
                                    'activeAssignments.officer.division:id,name',
                                    'activeAssignments.assignedBy:id,first_name,last_name',
                            ])
                            ->first();
      
        // Get the file assignment for the logged-in user, if it exists
        $fileAssignment = $circulation?->activeAssignments->firstWhere('officer_id', Auth::id()); 

        
        $ministries = $this->ministries->list()
                                       ->where('id', '!=', $file->ministry_id)
                                       ->whereNotIn('id', $fileCirculations->pluck('to_ministry_id')->unique())
                                       ->values();

        $officers = $this->users->pluck();

        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
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
                                                             'isClosed', 
                                                             'closedBy',
                                                             'closedDate', 
                                                             'ministryId', 
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
                                                             'hod'));
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
            $file->load('ministry');
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
                $ministries = $this->ministries->list();

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

        $ministryId = Auth::user()->ministry_id;

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_ministry_id' => $ministryId,
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
    public function signFile(Request $request, File $file) 
    {

        $status = $file->correspondence_type === 'internal' ? 'Pending UFS Circulation' : 'Pending Dispatch';

        $file->update([
            'signature_path' => Auth::user()->signature_path,
            'signed_at'       => now(),
            'signed_by'       => Auth::user()->id,
            'status'          => $status,
        ]);

        $file->refresh();

        FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_ministry_id' => Auth::user()->ministry_id,
            ],
            [
                'updated_by'     => auth()->id(),
                'status'         => $status,
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
            $ministries = $this->ministries->list();
                
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



        return redirect()->route('registry.files.index')->with('success', 'File is signed and final PDF generated.');
    }


    public function storeInternalReviewer(Request $request, File $file) 
    {
        // dd($request);
        $validated = $request->validate([
            'review_officer' => 'required|exists:users,id'
        ]);

        $ministryId = Auth::user()->ministry_id;
        
        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id'        => $file->id,
                'to_ministry_id' => $ministryId,
            ],
            [
                'circulated_by'  => auth()->id(),
                'circulated_at'  => now(),
                'updated_by'     => auth()->id(),
                'status'         => 'Pending Review',
                'review_officer' => $validated['review_officer']
            ]
        );
        
        return redirect()->route('registry.files.index')->with('success', 'File circulated ');
    }


    
    public function edit(File $file)
    {
        $this->authorize('update', $file);

        $ministryId = Auth::user()->ministry_id;

        $identityOrganisations = IdentityOrganisation::with('type')->orderBy('name')->get(); 
        // dd($identityOrganisations);       
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


    public function archive(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $file = File::findOrFail($request->file_id);

        MinistryArchivedFile::firstOrCreate(
            [
                'file_id' => $file->id,
                'ministry_id' => Auth::user()->ministry_id,
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
                'ministry_id' => Auth::user()->ministry_id,
            ],
            [
                'closed_by' => auth()->id(),
                'closed_at' => now(),
            ]
        );

         return redirect()->route('registry.files.index')->with('success', 'File moved to Closed Files!');

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



    public function sign(File $file)
    {
        $file->signature()->create([
            'signed_by'       => auth()->id(),
            'signed_name'     => Auth::user()->full_name,
            'signed_title'    => Auth::user()->ministry?->reviewer_title,
            'signed_ministry' => Auth::user()->ministry?->name,
            'signature_image' => Auth::user()->signature_path,
            'signed_at'       => now(),
        ]);

        $file->update([
            'status' => 'Signed',
        ]);

        return back()->with('success', 'File signed successfully.');
    }

}
