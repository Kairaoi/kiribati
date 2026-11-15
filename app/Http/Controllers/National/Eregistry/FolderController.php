<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\National\Eregistry\FolderStoreRequest;
use App\Http\Requests\National\Eregistry\FolderUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Models\National\Eregistry\Folder;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileFolderAssignment;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class FolderController extends Controller
{

    private $folders;
    private $organisations;

    public function __construct(FolderRepository $folders,
                                OrganisationRepository $organisations)
    {
        $this->folders = $folders;
        $this->organisations = $organisations;
    }


    public function filesDataTable(Folder $folder)
    {
        abort_if(
            $folder->organisation_id !== auth()->user()->organisation_id,
            404
        );

        $query = FileFolderAssignment::query()
            ->where('folder_id', $folder->id)
            ->where('organisation_id', auth()->user()->organisation_id)
            ->with([
                'file:id,uuid,reference_no,subject,status,letter_date',
                'filedBy:id,first_name,last_name',
            ]);

        return DataTables::eloquent($query)
            ->addColumn('reference_no', function ($assignment) {
                return $assignment->file?->reference_no ?? '-';
            })
            ->addColumn('subject', function ($assignment) {
                return $assignment->file?->subject ?? '-';
            })
            ->addColumn('filed_by', function ($assignment) {
                return $assignment->filedBy?->name ?? '-';
            })
            ->addColumn('action', function ($assignment) {
                return view(
                    'registry.folders.partials.file-actions',
                    compact('assignment')
                )->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }



    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $folders = $this->folders->getTopFolders(auth()->user()->organisation_id)
    //                              ->get(['id', 'uuid', 'name']);

    //     return view('national.eregistry.folders.index', compact('folders'));
    // }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $search = trim($validated['search'] ?? '');

        $organisationId = $request->user()->organisation_id;
        $pattern = '%' . $search . '%';

        $folders = Folder::query()
            ->where('organisation_id', $organisationId)
            ->when(
                $search === '',
                fn ($query) => $query->whereNull('parent_id'),
                fn ($query) => $query->where('name', 'like', $pattern)
            )
            ->withCount('fileAssignments')
            ->orderBy('name')
            ->paginate(20, ['*'], 'folders_page')
            ->withQueryString();

        $fileResults = null;

        if ($search !== '') {
            $fileResults = FileFolderAssignment::query()
                ->where('organisation_id', $organisationId)
                ->whereHas('folder', fn ($folders) =>
                    $folders->where('organisation_id', $organisationId)
                )
                ->whereHas('file', function ($files) use ($pattern) {
                    $files->where(function ($files) use ($pattern) {
                        $files->where('subject', 'like', $pattern)
                            ->orWhere('reference_no', 'like', $pattern);
                    });
                })
                ->with(['file', 'folder'])
                ->orderByDesc('filed_at')
                ->orderByDesc('id')
                ->paginate(20, ['*'], 'files_page')
                ->withQueryString();
        }

        return view('national.eregistry.folders.index', compact('folders', 'fileResults'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $parent = null;

        $user = auth()->user();

        if ($request->filled('parent')) {
            $parent = Folder::where('uuid', $request->parent)
                ->where('organisation_id', $user->organisation_id)
                ->firstOrFail();
        }

        $breadcrumbs = collect();
        $currentFolder = $parent;
        $visited = [];

        while ($currentFolder) {
            // Prevent a loop if the hierarchy contains a cycle.
            if (isset($visited[$currentFolder->id])) {
                break;
            }

            $visited[$currentFolder->id] = true;
            $breadcrumbs->prepend($currentFolder);

            $currentFolder = Folder::where('id', $currentFolder->parent_id)
                ->where('organisation_id', $user->organisation_id)
                ->first();
        }

        $isSystemAdmin = $user->hasRole('system-admin');

        $organisations = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        return view('national.eregistry.folders.create', compact('organisations', 
                                                                 'user', 
                                                                 'isSystemAdmin',
                                                                 'parent',
                                                                 'breadcrumbs'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(FolderStoreRequest $request)
    {
        $validated = $request->validated();

        $folder = $this->folders->create($validated);

        return redirect()->route('registry.folders.show', $folder->uuid)->with('success', 'Folder created successfully.');
    }


    public function moveFile(Request $request, Folder $folder, File $file)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'destination_folder_id' => [
                'required',
                'integer',
                Rule::exists('folders', 'id')
                    ->where('organisation_id', $user->organisation_id),
            ],
        ]);

        
        $assignment = FileFolderAssignment::where('file_id', $file->id)
            ->where('organisation_id', $user->organisation_id)
            ->where('folder_id', $folder->id)
            ->firstOrFail();


        if ($assignment->folder_id == $validated['destination_folder_id']) {
            return back()->with('info', 'The file is already in this folder.');
        }


        $assignment->update([
            'folder_id' => $validated['destination_folder_id'],
            'updated_by' => $user->id,
        ]);

        return response()->json(['message' => 'File moved.',]);
    }


    
    /**
     * Display the specified resource.
     */
    public function show(Folder $folder)
    {
        // dd($folder);
        $subfolders = $folder->children()->where('is_active', true)
                                         ->orderBy('name')
                                        ->get();

        $breadcrumbs = collect();
        $currentFolder = $folder;
        $visited = [];

        while ($currentFolder) {
            // Prevent an endless loop if the folder hierarchy contains a cycle.
            if (isset($visited[$currentFolder->id])) {
                break;
            }

            $visited[$currentFolder->id] = true;

            $breadcrumbs->prepend($currentFolder);

            $currentFolder = $currentFolder->parent;
        }

        $folders = $this->folders->getTopFolders(auth()->user()->organisation_id);

        // $countFiles = function ($folder) use (&$countFiles) {
        //     return ($folder->file_assignments_count ?? 0)
        //         + $folder->childrenRecursive->sum(
        //             fn ($child) => $countFiles($child)
        //         );
        // };

        // foreach ($folders as $folder) {
        //     $folder->total_files_count = $countFiles($folder);
        // }

        return view('national.eregistry.folders.show', compact('folder', 
                                                                'subfolders',
                                                                'breadcrumbs',
                                                                'folders'));
    }

    public function files(Folder $folder)
    {
        $userOrganisationId = auth()->user()->organisation_id;

        // Prevent access to another organisation's folder.
        abort_unless(
            (int) $folder->organisation_id === (int) $userOrganisationId,
            403
        );

        $query = DB::table('file_folder_assignments as ffa')
            ->join('files', 'files.id', '=', 'ffa.file_id')
            ->where('ffa.folder_id', $folder->id)
            ->where('ffa.organisation_id', $userOrganisationId)
            ->select([
                'files.id',
                'files.uuid',
                'files.reference_no',
                'files.subject',
                'files.letter_date',
                'ffa.filed_at',
            ]);

        return DataTables::of($query)->make(true);
    }

    public function children(Folder $folder)
    {
        abort_if(
            $folder->organisation_id !== Auth::user()->organisation_id,
            404
        );

        $folders = $folder->children()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'uuid',
                'name',
                'parent_id',
            ]);

        return response()->json([
            'folders' => $folders,
        ]);
    }

    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Folder $folder)
    {
        $parent = null;

        $user = auth()->user();

        if ($request->filled('parent')) {
            $parent = Folder::where('uuid', $request->parent)
                ->where('organisation_id', $user->organisation_id)
                ->firstOrFail();
        }

        $breadcrumbs = collect();
        $currentFolder = $parent;
        $visited = [];

        while ($currentFolder) {
            // Prevent a loop if the hierarchy contains a cycle.
            if (isset($visited[$currentFolder->id])) {
                break;
            }

            $visited[$currentFolder->id] = true;
            $breadcrumbs->prepend($currentFolder);

            $currentFolder = Folder::where('id', $currentFolder->parent_id)
                ->where('organisation_id', $user->organisation_id)
                ->first();
        }

        $isSystemAdmin = $user->hasRole('system-admin');

        $ministries = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        return view('national.eregistry.folders.edit', compact('ministries', 
                                                                 'folder',
                                                                 'user', 
                                                                 'isSystemAdmin',
                                                                 'parent',
                                                                 'breadcrumbs'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(FolderUpdateRequest $request, string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
