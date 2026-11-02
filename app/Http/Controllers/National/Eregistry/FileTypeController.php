<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\FileType;
use App\Models\National\Eregistry\Ministry;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class FileTypeController extends Controller {

    private $fileTypes;
    private $organisations;
    private $divisions;

    public function __construct(FileTypeRepository $fileTypes,
                                OrganisationRepository $organisations,
                                DivisionRepository $divisions)
    {
        $this->fileTypes = $fileTypes;
        $this->organisations = $organisations;
        $this->divisions = $divisions;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        $selectedType = $request->get('selected_type');

        $organisationId = Auth::user()?->organisation_id;
        $user = Auth::user();

        if (!$organisationId) {
            abort(403, 'Organisation not found');
        }

        $search = $request->get('search', '') ;
        if (is_array($search)) {
            $search = $search['value'];
        }
        
        $query = $this->fileTypes->getForDataTable($selectedType, $organisationId, $user, $search);
        
        $datatables = DataTables::make($query)
                        ->addColumn('can_edit', function ($row) {
                            return Auth::user()->hasRole('system-admin');
                        })
                        ->make(true);
        
        return $datatables;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fileTypes = FileType::query()->withCount('files');
        return view('national.eregistry.file_types.index', compact('fileTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('file_type.create')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $fileTypes = $this->fileTypes->getFileTypes();

        $isSystemAdmin = auth()->user()->hasRole('system-admin');

        return view('national.eregistry.file_types.create', compact('fileTypes', 'isSystemAdmin'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',

                function ($attribute, $value, $fail) use ($user) {
                    $name = mb_strtolower(trim($value));

                    $query = DB::table('file_types')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name]);

                    if ($user->hasRole('system-admin')) {
                        /*
                        * A new global name should not duplicate any existing
                        * file type, because it will be visible to all ministries.
                        */
                        $exists = $query
                            ->where(function ($query) use ($user) {
                                $query
                                    ->where('is_global', true);
                            })
                            ->exists();
                    } else {
                        /*
                        * Registry users cannot duplicate a global file type
                        * or a file type from their own ministry.
                        */
                        $exists = $query
                            ->where(function ($query) use ($user) {
                                $query
                                    ->where('is_global', true)
                                    ->orWhere(function ($query) use ($user) {
                                        $query
                                            ->where('is_global', false)
                                            ->where('organisation_id', $user->organisation_id);
                                    });
                            })
                            ->exists();
                    }

                    if ($exists) {
                        $fail('This file type name is already available in this scope.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'code' => [
                'required',
                'string',
                'min:2',
                'max:3',
                'regex:/^[A-Z]{2,3}$/',
                Rule::unique('file_types')->where(function ($query) {
                    $orgId = Auth::user()->organisation_id;

                    $query->where(function ($q) use ($orgId) {
                        $q->where('is_global', 1)
                        ->orWhere('organisation_id', $orgId);
                    });
                }),
            ],
        ]);

        $this->fileTypes->createFileType($validated, $user);
       
        return redirect()->route('registry.file-types.index')->with('message', 'File Type created successfully.');
    }

    
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FileType $fileType)
    {
        
        $totalFiles = $fileType->files()
                                ->where('organisation_id', Auth::user()->organisation_id)
                                ->count();

        $activeFiles = $fileType->files()
            ->where('organisation_id', Auth::user()->organisation_id)
            ->where('is_active', true)
            ->count();

        return view('national.eregistry.file_types.show', compact('fileType',
                                                                   'totalFiles',
                                                                   'activeFiles'));
    }


    public function suggestions(Request $request)
    {
        $query = $request->q;
        $orgId = Auth::user()->organisation_id;

        return FileType::where('name', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($orgId) {
                $q->where('organisation_id', $orgId)
                ->orWhere('is_global', 1);
            })
            ->distinct()
            ->pluck('name');
      
    }

    public function codeSuggestions(Request $request)
    {
        $query = $request->q;
        $orgId = Auth::user()->organisation_id;

        return FileType::where('code', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($orgId) {
                $q->where('organisation_id', $orgId)
                ->orWhere('is_global', 1);
            })
            ->distinct()
            ->pluck('code');
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (!Auth::user()->can('file_type.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $fileType = $this->fileTypes->getById($id);

        return view('national.eregistry.file_types.edit', compact('fileType'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, FileType $fileType)
    // {
    //     $validated = $request->validate([
    //         'name' => [
    //             'required',
    //             'string',
    //             'max:255',
    //             function ($attribute, $value, $fail) use ($fileType) {

    //                 $orgId = Auth::user()->ministry_id;

    //                 $exists = DB::table('file_types')
    //                     ->where('name', trim($value))
    //                     ->where('id', '!=', $fileType->id)
    //                     ->where(function ($query) use ($orgId) {
    //                         $query->where('is_global', true)
    //                             ->orWhere('ministry_id', $orgId);
    //                     })
    //                     ->exists();

    //                 if ($exists) {
    //                     $fail('A file type with this name already exists.');
    //                 }
    //             },
    //         ],
    //         'description' => [
    //             'required',
    //             'string',
    //             'max:255',
    //         ],

    //     ]);
        

    //     $fileType = $this->fileTypes->getById($id);

    //     $this->fileTypes->update($fileType, $request->all());

    //     return redirect()->route('file_type.index')->with('message', 'File Type updated successfully.');
    // }


    public function activate(FileType $fileType)
    {
        $fileType->update([
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'File Type activated successfully.'
        ]);
    }


    public function deactivate(FileType $fileType)
    {
        $fileType->update([
            'is_active' => false,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'File Type deactivated successfully.'
        ]);
    }


    public function destroy(FileType $fileType)
    {
        $this->authorize('delete', $fileType);

        if ($fileType->files()->exists()) {
            return redirect()
                ->route('registry.file-types.index')
                ->with(
                    'error',
                    'This file type cannot be deleted because it is being used by one or more files.'
                );
        }

        $this->fileTypes->deleteById($fileType->id);

        return redirect()
            ->route('registry.file-types.index')
            ->with('success', 'File type deleted successfully.');
    }
}
