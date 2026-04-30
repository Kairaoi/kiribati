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
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

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
        $query = $this->files->getForDataTable(auth()->user()->ministry_id);

        return DataTables::of($query)
            ->editColumn('file_status', function ($file) {
                return $file->ministry_id == auth()->user()->ministry_id
                    ? $file->file_status
                    : ($file->circulation_status ?? 'Pending');
            })
            ->make(true);
    }


    // public function getFiles(Request $request)
    // {

    //     $organisationId = Auth::user()->organisation_id;

    //     $query = $this->files->getForDataTable($request->search['value'] ?? '');

    //     $query = $query->visibleToOrganisation($organisationId);

    //     $files = $query
    //         ->with('recipientMinistries')
    //         ->get();

    //     return response()->json([
    //         'data' => $files
    //     ]);
    // }

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
     * Display a listing of the files.
     *
     * @return \Illuminate\View\View
     */
    // public function index()
    // {
    //     $divisions = $this->divisions->list();
    //     $officers = $this->fileCirculations->listOfficers();
    //     $organisations = $this->organisations->list();

    //     // $archives = DB::table('organisation_archived_files')
    //     //     ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
    //     //     ->where('organisation_id', auth()->user()->organisation_id)
    //     //     ->groupBy('year', 'month')
    //     //     ->orderBy('year', 'desc')
    //     //     ->orderBy('month', 'desc')
    //     //     ->get()
    //     //     ->groupBy('year');

    //     $orgFilters = DB::table('organisation_archived_files as oaf')
    //         ->join('organisations as o', 'o.id', '=', 'oaf.organisation_id')
    //         ->select('o.id', 'o.code', DB::raw('COUNT(*) as total'))
    //         ->groupBy('o.id', 'o.code')
    //         ->orderBy('o.code')
    //         ->get();

    //     $files= DB::table('organisation_archived_files as oaf')
    //         ->join('files as f', 'f.id', '=', 'oaf.file_id')
    //         ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
    //         ->where('oaf.organisation_id', auth()->user()->organisation_id)
    //         ->select(
    //             'f.id as file_id',
    //             'f.subject as file_subject', 
    //             'o.name as organisation_name',
    //             'o.id as organisation_id',
    //             'o.code as organisation_code',
    //             'oaf.created_at as archived_date'
    //         )
    //         ->orderBy('oaf.created_at', 'desc')
    //         ->get();

    //     $organisations = DB::table('organisation_archived_files as oaf')
    //         ->join('files as f', 'f.id', '=', 'oaf.file_id')
    //         ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
    //         ->where('oaf.organisation_id', auth()->user()->organisation_id)
    //         ->select('o.id', 'o.name', DB::raw('COUNT(*) as total'))
    //         ->groupBy('o.id', 'o.name')
    //         ->orderBy('o.name')
    //         ->get();

    //     $monthlyArchives = DB::table('organisation_archived_files as oaf')
    //         ->join('files as f', 'f.id', '=', 'oaf.file_id')
    //         ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
    //         ->where('oaf.organisation_id', auth()->user()->organisation_id)
    //         ->select(
    //             DB::raw('YEAR(oaf.created_at) as year'),
    //             DB::raw('MONTH(oaf.created_at) as month'),
    //             DB::raw('COUNT(*) as total')
    //         )
    //         ->groupBy('year', 'month')
    //         ->orderBy('year', 'desc')
    //         ->orderBy('month', 'desc')
    //         ->get()
    //         ->groupBy('year', 'month');


    //     return view('national.eregistry.files.index', compact('divisions', 
    //     'officers', 'organisations', 'orgFilters', 'files', 'monthlyArchives'));
    // }

    public function index()
    {
        // if (!auth()->user()->hasRole(['registry','admin'])) {
        //     abort(403, 'Unauthorized access');
        // }
        return view('national.eregistry.files.index');

        
    }

    //     return view('national.eregistry.files.index', compact('identityOrganisations'));
    // }

    /**
     * Show the form for creating a new file (for dispatch & circulation).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        $identityOrganisations = IdentityOrganisation::select(
                'id',
                'name',
                'code',
                'organisation_type_id'
            )
            ->get();        
        $externalPartners = $this->externalPartners->list(); // Fetch external partners for the dropdown in the file creation form
        $ministryId = Auth::user()->ministry_id;
        $file_types = $this->file_types->getFileTypes(); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($ministryId); // Fetch divisions for the logged-in organisation
        
        // Return the view and pass the data
        return view('national.eregistry.files.create', compact('identityOrganisations',
                                                                'externalPartners',    
                                                                'divisions',
                                                                'categories',
                                                                'file_types',
                                                        
            
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
            'from_division_id' => 'nullable|exists:divisions,id',
            'subject' => 'required|string|max:255',
            'main_file' => 'required|file|mimes:pdf|max:10240',
            'additional_files' => 'nullable|array|max:3',
            'additional_files.*' => 'file|mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx|max:10240',
            'file_type_id' => 'required|exists:file_types,id',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
        ]);

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

        try {
            $mainFile = $request->file('main_file');
            $mainFilePath = $mainFile->store('uploads/main_files', 'public');
            $mainFileName = $mainFile->getClientOriginalName(); // save original name

            // Store up to 3 additional files
            $additionalFilePaths = [];

            if ($request->hasFile('additional_files')) {
                foreach ($request->file('additional_files') as $uploadedFile) {
                    $path = $uploadedFile->store('uploads/additional_files', 'public');
                    $additionalFilePaths[] = $path;
                }
            }

            $fileData = array_merge($validated,
                [
                    'main_file_path' => $mainFilePath,
                    'additional_file_paths' => $additionalFilePaths, 
                    'reference_no' => $referenceNo,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'letter_date' => now()->toDateString(),
                    'ministry_id' => auth()->user()->ministry_id,
                    'status' => 'Pending Action'
                ]
            );

            $file = File::create($fileData);

            activity('file')
                ->causedBy(auth()->user())
                ->performedOn($file)
                ->withProperties([
                    'file_name' => $file->name
                ])
                ->log('File created');

            Log::info('File successfully stored in database', ['file_id' => $file->id]);

            return view('national.eregistry.files.index')  
                ->with('success', 'File created successfully. Reference No: ' . $file->reference_no);


        } catch (\Exception $e) {
            Log::error('Error storing file', ['message' => $e->getMessage(), 'file_data' => $validated]);
            return back()->withErrors(['error' => 'Error storing file: ' . $e->getMessage()])->withInput();
        }
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
        $fileCirculations = $this->fileCirculations->ministryCirculations($fileId, $ministryId)->latest()->get();
        $circulation = $this->fileCirculations->thisCirculation($fileId, $ministryId);
        $fileAssignment = $circulation?->activeAssignments()->where('officer_id', Auth::id())->first(); // Get the file assignment for the logged-in user, if it exists
        // dd($fileAssignment);
        $dispatchedMinistries = $fileCirculations->pluck('to_ministry_id')->unique()->toArray();
        $ministries = $this->ministries->list()
                                        ->where('id', '!=', $file->ministry_id)
                                        ->whereNotIn(
                                            'id',
                                            $fileCirculations->pluck('to_ministry_id')->unique()
                                        )
                                        ->values();
        // dd($dispatchedMinistriesId);
        // dd($dispatchedMinistries);
        $officers = $this->users->pluck();
        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
                                ->first();
        $usersWithDivision = $this->users->getUsersDivision(); // Fetch all admin users using the listAdmins method from UserRepository  

        // dd($fileId);
        
        return view('national.eregistry.files.show', compact('file', 'fileId', 'ministries', 'officers', 'dispatchedMinistries', 'reviewOfficer', 'usersWithDivision', 'fileCirculations', 'circulation', 'fileAssignment'));
    }


    //display file dispatch or file circulation from e-filing or archive 
    // public function show($id)
    // {
    //     $file = File::findOrFail($id);

    //     //if the file is a dispatch file and the logged in user belongs to the sender ministry, show the dispatch details and circulation history for the file
    //     if ($file->initial_type === 'dispatch' && $file->organisation_id == auth()->user()->organisation_id) {
    //         $dispatch = $this->dispatches->where('file_id', $id)->first();        
    //         $fileCirculations = $file->circulations()->with('fromOrganisation', 'assignedOfficers')->get();

    //         return view('national.eregistry.dispatches.show', compact('file', 'dispatch', 'fileCirculations'));
        
    //     //if the file is also a dispatch file and the logged in user belongs to the recipient ministry, show the circulation details for the file
    //     //also if the file is an internal circulation file and the logged in user belongs to the recipient ministry, show the circulation details for the file
    //     } else if ( ($file->initial_type === 'dispatch' &&  $file->recipientMinistries->pluck('id')->contains(auth()->user()->organisation_id))  || 
    //                ($file->initial_type === 'internal' && $file->recipientMinistries->pluck('id')->contains(auth()->user()->organisation_id)) ) {
                    
    //                 $fileCirculation = $this->fileCirculations->where('file_id', $id)
    //                                                         ->where('to_organisation_id', auth()->user()->organisation_id)->first(); // Ensure the circulation record is for the logged-in user's organisation
                    
    //                 $fileCirculation->load('assignedOfficers'); // Load assigned officers for the circulation record
    //                 $usersWithDivision = $this->users->getUsersDivision(); // Fetch all admin users using the listAdmins method from UserRepository  
    //                 $loggedInOrganisation = $this->organisations->getById(Auth()->user()->organisation_id); // Get the logged-in user's organisation
    //                 $fileRecipientOrganisations = $file->recipientMinistries()->pluck('organisations.id')->toArray();

    //                 return view('national.eregistry.circulations.show', compact('file', 'usersWithDivision', 'loggedInOrganisation', 'fileCirculation'));       
                
    //         } 

    //     // fallback (optional)
    //     abort(403, 'Unauthorized access to this file');
    // }


    /**
     * View the file content directly in the browser.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // public function viewFile($id)
    // {
    //     $file = $this->files->getById($id);

    //     // Optional: Check permissions
    //     // if (!auth()->user()->can('view', $file)) {
    //     //     abort(403, 'Unauthorized');
    //     // }

    //     $filePath = storage_path('app/private/' . $file->main_file_path);

    //     if (!file_exists($filePath)) {
    //         abort(404, 'File not found');
    //     }

    //     return response()->file($filePath, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
    //     ]);
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

        $recipientOrgIds = $file->recipientMinistries->pluck('id')->toArray();

        Log::info('Attempting to view file', [
            'file_id' => $id,
            'user_org_id' => $userOrgId,
            'recipient_org_ids' => $recipientOrgIds
        ]);

        if ($file->ministry_id != $userOrgId && !in_array($userOrgId, $recipientOrgIds)) {
            abort(403, 'Unauthorized action.');
        }

        // use Storage (BEST PRACTICE)
        if (!Storage::disk('public')->exists($file->main_file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->response($file->main_file_path, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($file->main_file_path).'"'
        ]);
    }


    /**
     * Show the form for editing the specified file.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editType($id, $editType)
    {

        if ($editType === 'dispatch') {
            $dispatch = $this->dispatches->getById($id);
            $file = $this->files->getById($dispatch->file_id);
        } elseif ($editType === 'internal') {
            $circulation = $this->fileCirculations->getById($id);
            $file = $this->files->getById($circulation->file_id);
        }

        $allowedTypes = ['dispatch', 'internal'];
        if (!in_array($editType, $allowedTypes)) {
            abort(404, 'Invalid file type specified.');
        }
        
        $organisationTypes = $this->organisation_types->list(); // Fetch all organisation types using the list method from OrganisationRepository

        $organisations = $this->organisations->list(); // Fetch all organisations using the list method from OrganisationRepository
        $ministries = $this->organisations->listMinistries(); // Fetch all ministries using the list method from OrganisationRepository
        

        $organisationId = Auth::user()->organisation_id;
        $file_types = $this->file_types->listWithDescriptions(); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($organisationId); // Fetch divisions for the logged-in organisation
        $allDivisions = $this->divisions->list(); // Fetch all divisions
        
        // Return the view and pass the data
        return view('national.eregistry.files.edit', compact('organisations',
                                                                'organisationTypes',
                                                                'ministries',       
                                                                'divisions',
                                                                'allDivisions',
                                                                'categories',
                                                                'file_types',
                                                                'editType',
                                                                'file'
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

            // Remove selected files
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
    
        $org = auth()->user()->organisation;
    
        // Fill extra pivot columns
        $org->archivedFiles()->syncWithoutDetaching([
            $file->id => [
                'archived_by' => auth()->id(),
                'archived_at' => now()
            ]
        ]);

        return response()->json(['success' => true]);
    }

}
