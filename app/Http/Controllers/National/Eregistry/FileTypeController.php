<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\FileType;
use App\Models\National\Eregistry\Ministry;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class FileTypeController extends Controller {

    private $fileTypes;
    private $ministries;
    private $divisions;

    public function __construct(FileTypeRepository $fileTypes,
                                MinistryRepository $ministries,
                                DivisionRepository $divisions)
    {
        $this->fileTypes = $fileTypes;
        $this->ministries = $ministries;
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

        $ministryId = auth()->user()?->ministry_id;
        $user = auth()->user();

        if (!$ministryId) {
            abort(403, 'Ministry not found');
        }

        $search = $request->get('search', '') ;
        if (is_array($search)) {
            $search = $search['value'];
        }
        
        $query = $this->fileTypes->getForDataTable($selectedType, $ministryId, $user, $search);
        
        $datatables = DataTables::make($query)
                        ->addColumn('can_edit', function ($row) {
                            return auth()->user()->hasRole('system-admin');
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
    
        return view('national.eregistry.file_types.index');
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
        // dd($fileTypes); 
        return view('national.eregistry.file_types.create')->with('fileTypes', $fileTypes);
    }

    // public function dynamicForm($fileTypeId)
    // { 
    //     \Log::info('Dynamic form called with ID: ' . $fileTypeId);

    //     $fileType = $this->fileTypes->getById($fileTypeId);
        
    //     if (!$fileType) {
    //         return response()->json(['message' => 'File type not found'], 404);
    //     }
        
    //     if ($fileType->type === 'Outward') {
    //         return view('national.eregistry.outward_files.create', [
    //             'organisations' => $this->organisations->pluck(),
    //             'divisions' => $this->divisions->pluck(),
    //             'fileTypes' => $this->fileTypes->pluck(),
    //         ])->render();
    //     } elseif ($fileType->type === 'Inward') {
    //         return view('national.eregistry.file_types.inward_create')->render();
    //     }
        
    //     return response()->json(['message' => 'Invalid file type'], 400);
    // }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (!Auth::user()->can('file_type.store')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    $orgId = auth()->user()->ministry_id;

                    $exists = DB::table('file_types')
                        ->where('name', $value)
                        ->where(function ($q) use ($orgId) {
                            $q->where('is_global', 1)
                            ->orWhere('ministry_id', $orgId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The name is already taken.');
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
                    $orgId = auth()->user()->ministry_id;

                    $query->where(function ($q) use ($orgId) {
                        $q->where('is_global', 1)
                        ->orWhere('ministry_id', $orgId);
                    });
                }),
            ],
        ]);

        FileType::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'code' => $validated['code'],
            'ministry_id' => auth()->user()->ministry_id,
            'is_global' => false,
        ]);

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
        // if (!Auth::user()->can('file_type.show')) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('national.eregistry.file_types.show', compact('fileType'));
    }


    public function suggestions(Request $request)
    {
        $query = $request->q;
        $orgId = auth()->user()->ministry_id;

        return FileType::where('name', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($orgId) {
                $q->where('ministry_id', $orgId)
                ->orWhere('is_global', 1);
            })
            ->distinct()
            ->pluck('name');
        // Log::info('suggestions called by user: ' . auth()->id());
        // return ['Test1', 'Test2', 'Test3'];
    }

    public function codeSuggestions(Request $request)
    {
        $query = $request->q;
        $orgId = auth()->user()->ministry_id;

        return FileType::where('code', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($orgId) {
                $q->where('ministry_id', $orgId)
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

        return view('national.eregistry.file_types.edit')->with('fileType', $fileType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('file_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        $fileType = $this->fileTypes->getById($id);
        $this->fileTypes->update($fileType, $request->all());

        return redirect()->route('file_type.index')->with('message', 'File Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('file_type.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $fileType = $this->fileTypes->getById($id);
        $this->fileTypes->delete($fileType);

        return redirect()->route('file_type.index')->with('message', 'File Type deleted successfully.');
    }
}
