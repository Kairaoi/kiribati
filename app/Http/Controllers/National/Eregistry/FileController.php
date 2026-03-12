<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Organisation;
use App\Models\National\Eregistry\OrganisationType;
use App\Repositories\National\Eregistry\CategoryRepository;
use App\Repositories\National\Eregistry\DispatchRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
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
    private $organisations;
    private $organisation_types;
    private $file_types;
    private $divisions;
    private $categories;
    private $dispatches;
    private $fileCirculations;

    public function __construct(
        FileRepository $files,
        OrganisationRepository $organisations,
        OrganisationTypeRepository $organisation_types,
        FileTypeRepository $file_types,
        CategoryRepository $categories,
        DivisionRepository $divisions,
        DispatchRepository $dispatches,
        FileCirculationRepository $fileCirculations
    ) {

        $this->files = $files;
        $this->organisations = $organisations;
        $this->organisation_types = $organisation_types;
        $this->file_types = $file_types;
        $this->divisions = $divisions;
        $this->categories = $categories;
        $this->dispatches = $dispatches;
        $this->fileCirculations = $fileCirculations;
    }

    /**
     * Get files for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function getDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->files->getForDataTable($search);

        // dd($query->get());

        Log::info($query->toSql());
        Log::info($query->getBindings());

        return DataTables::of($query)->make(true);
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
       
        return Datatables::of($query)->make(true);

    }
       


    /**
     * Display a listing of the files.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $divisions = $this->divisions->list();
        $officers = $this->fileCirculations->listOfficers();
        $organisations = $this->organisations->list();

        // $archives = DB::table('organisation_archived_files')
        //     ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
        //     ->where('organisation_id', auth()->user()->organisation_id)
        //     ->groupBy('year', 'month')
        //     ->orderBy('year', 'desc')
        //     ->orderBy('month', 'desc')
        //     ->get()
        //     ->groupBy('year');

        $orgFilters = DB::table('organisation_archived_files as oaf')
            ->join('organisations as o', 'o.id', '=', 'oaf.organisation_id')
            ->select('o.id', 'o.code', DB::raw('COUNT(*) as total'))
            ->groupBy('o.id', 'o.code')
            ->orderBy('o.code')
            ->get();

        $files= DB::table('organisation_archived_files as oaf')
            ->join('files as f', 'f.id', '=', 'oaf.file_id')
            ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
            ->where('oaf.organisation_id', auth()->user()->organisation_id)
            ->select(
                'f.id as file_id',
                'f.subject as file_subject', 
                'o.name as organisation_name',
                'o.id as organisation_id',
                'o.code as organisation_code',
                'oaf.created_at as archived_date'
            )
            ->orderBy('oaf.created_at', 'desc')
            ->get();

        $organisations = DB::table('organisation_archived_files as oaf')
            ->join('files as f', 'f.id', '=', 'oaf.file_id')
            ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
            ->where('oaf.organisation_id', auth()->user()->organisation_id)
            ->select('o.id', 'o.name', DB::raw('COUNT(*) as total'))
            ->groupBy('o.id', 'o.name')
            ->orderBy('o.name')
            ->get();

        $monthlyArchives = DB::table('organisation_archived_files as oaf')
            ->join('files as f', 'f.id', '=', 'oaf.file_id')
            ->join('organisations as o', 'o.id', '=', 'f.organisation_id') // sender ministry
            ->where('oaf.organisation_id', auth()->user()->organisation_id)
            ->select(
                DB::raw('YEAR(oaf.created_at) as year'),
                DB::raw('MONTH(oaf.created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy('year', 'month');


        return view('national.eregistry.files.index', compact('divisions', 
        'officers', 'organisations', 'orgFilters', 'files', 'monthlyArchives'));
    }


    /**
     * Show the form for creating a new file (for dispatch & circulation).
     *
     * @return \Illuminate\View\View
     */
    public function createType($createType)
    {

        $allowedTypes = ['dispatch', 'internal'];
        if (!in_array($createType, $allowedTypes)) {
            abort(404, 'Invalid file type specified.');
        }
        
        $organisationTypes = $this->organisation_types->list(); // Fetch all organisation types using the list method from OrganisationRepository

        $organisations = $this->organisations->list(); // Fetch all organisations using the list method from OrganisationRepository
    
        $ministries = $this->organisations->listMinistries(); // Fetch all ministries using the list method from OrganisationRepository
        
        $otherTypeId = OrganisationType::where('name', 'Other')->value('id');        

        $organisationId = Auth::user()->organisation_id;
        $file_types = $this->file_types->listWithDescriptions(); 
        $categories = $this->categories->listWithDescriptions();
        $divisions = $this->divisions->listWithOrganisation($organisationId); // Fetch divisions for the logged-in organisation
        $allDivisions = $this->divisions->list(); // Fetch all divisions
        
        // Return the view and pass the data
        return view('national.eregistry.files.create', compact('organisations',
                                                                'organisationTypes',
                                                                'ministries',       
                                                                'divisions',
                                                                'allDivisions',
                                                                'categories',
                                                                'file_types',
                                                                'createType',
                                                                'otherTypeId'
            
        ));
    }


    public function create() {
        // return redirect()->route('registry.files.index'); // or abort(404)
    }


    /**
     * Store a newly created file in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $otherTypeId = OrganisationType::where('name', 'Other')->value('id');

        $request->validate([
            'organisation_id'   => 'nullable|exists:organisations,id',
            // 'organisation_name' => 'required_if:organisation_id,__add_new__|string|max:255',
            // 'organisation_name' => 'nullable|string|max:255',
            'organisation_name' => 'nullable|string|required_if:organisation_id,__add_new__|max:255',
        ]);

        if ($request->filled('organisation_id')) {
            $fromOrganisationId = $request->organisation_id;
        } else {
            // create new organisation from text input
            $organisation = Organisation::firstOrCreate(
                [
                    'name' => trim($request->organisation_name),
                    'organisation_type_id' => $otherTypeId, // 'Other' is a valid organisation type for manually entered organisations
                ],
                [
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
            $fromOrganisationId = $organisation->id;
        }

        $request->merge([
            'fromOrganisationId' => $fromOrganisationId,
        ]);

        $validated = $request->validate([
            'fromOrganisationId' => 'required|exists:organisations,id',
            'division_id' => 'nullable|exists:divisions,id',
            'subject' => 'required|string|max:255',
            'initial_type' => 'required|in:dispatch,internal',
            'main_file' => 'required|file|mimes:pdf|max:10240',
            'additional_files' => 'nullable|array|max:3',
            'additional_files.*' => 'file|mimes:pdf,xls,xlsx,png,jpg,jpeg,doc,docx,ppt,pptx|max:10240',
            'letter_ref_no' => 'nullable|string|unique:files,letter_ref_no',
            'file_type_id' => 'required|exists:file_types,id',
            'category_id' => 'required|exists:categories,id',
            'recipient_organisations' => 'required|array',
            'recipient_organisations.*' => 'exists:organisations,id',
        ]);

        // dd($request);

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

            // Build file data for the main record
            $fileData = array_merge(
                Arr::except($validated, ['recipient_organisations']),
                [
                    'main_file_path' => $mainFilePath,
                    'additional_file_paths' => $additionalFilePaths, // Store as JSON
                    'file_reference' => null,
                    'file_index' => null,
                    'status' => $validated['initial_type'] === 'internal' ? 'Pending Circulation' : 'Pending Dispatch',
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'letter_date' => now()->toDateString(),
                    'organisation_id' => $fromOrganisationId,
                ]
            );

            // Create file record
            $file = File::create($fileData);
            
            // create an internal file circulation within the ministry
            if ($file->initial_type === 'internal') {
                foreach ($validated['recipient_organisations'] as $organisationId) {
                    $organisation = Organisation::with('reviewOfficer')->find($organisationId); //get the organisation along with its review officer

                    $file->recipientMinistries()->attach($organisationId, ['status' => 'Pending Circulation']); 
                    //and a new FileCirculation record is created for the logged in organisation
                    FileCirculation::create([                                                       
                        'file_id' => $file->id,
                        'from_organisation_id' => $fromOrganisationId, 
                        'to_organisation_id' => $organisationId,
                        'to_review_file' => $organisation->reviewOfficer ? $organisation->reviewOfficer->id : null,         
                    ]);
                }

                $file->status = "Pending Circulation";
                
            //first step in creating dispatch file (with status Pending Dispatch)
            } elseif ($file->initial_type === 'dispatch') {
            
                foreach ($validated['recipient_organisations'] as $organisationId) {
                    $file->recipientMinistries()->attach($organisationId, ['status' => 'Pending Dispatch']);
                }

                // Create initial dispatch record
                Dispatch::create([
                    'file_id' => $file->id,
                    'from_organisation_id' => $fromOrganisationId,
                    'from_division_id' => $validated['division_id'],
                    'updated_by' => auth()->id(),
                ]);

                $file->status = "Pending Dispatch";
            }

            // dd($file->recipient_organisations);
            Log::info('File successfully stored in database', ['file_id' => $file->id]);

            if($file->initial_type === 'dispatch') {
                if(auth()->user()->hasRole('user') || (auth()->user()->hasRole('admin')) ){
                    return redirect()->route('registry.dispatches.user.index')->with('success', 'New Dispatch created successfully!');
                }

                if(auth()->user()->hasRole('registry')) {
                    return redirect()->route('registry.dispatches.index')->with('success', 'New Dispatch created successfully!');
                }

            }else{
                return redirect()->route('registry.file-circulations.index')->with('success', 'New Circulation created successfully!');
            }

            
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
    public function show($id)
    {
        $file = $this->files->getById($id);
        $organisations = $this->organisations->pluck(); 
        $divisions = $this->divisions->pluck();
        
        return view('national.eregistry.files.show', compact('file', 'organisations'));
    }



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
        
        $userOrgId = Auth::user()->organisation_id;
        $file = $this->files->getById($id);
        $recipientOrgIds = $file->recipientMinistries->pluck('id')->toArray();
        $filePath = storage_path('app/private/' . $file->main_file_path);
       
        // dd($file->id, $recipientOrgIds);

        Log::info('Attempting to view file', ['file_id' => $id, 'user_org_id' => $userOrgId, 'file_org_id' => $file->organisation_id, 'recipient_org_ids' => $recipientOrgIds]);

        // Check if the user belongs to the organisation that owns the file 
        // and if the user organisation is in the recipient ministries
        if($file->organisation_id == $userOrgId || in_array($userOrgId, $recipientOrgIds)) {
             if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }

            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
            ]);
            return Storage::disk('local')->response($file->main_file_path);
        }
  
        abort(403, 'Unauthorized action.');
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
