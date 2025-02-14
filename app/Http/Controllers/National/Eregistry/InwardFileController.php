<?php

namespace App\Http\Controllers\National\Eregistry;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\National\Eregistry\OutwardFile;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\InwardFileRepository;


class InwardFileController extends Controller
{
    private $inwardfiles;
    private $folders;
    private $ministries;
    private $divisions;
    private $file_types;

    public function __construct(
        InwardFileRepository $inwardfiles,
        FolderRepository $folders,
        MinistryRepository $ministries,
        DivisionRepository $divisions,
        FileTypeRepository $file_types
    )
    {
        $this->inwardfiles = $inwardfiles;
        $this->folders = $folders;
        $this->ministries = $ministries;
        $this->divisions = $divisions;
        $this->file_types = $file_types;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        // Get the logged-in user's ministry_id
        $ministryId = Auth::user()->ministry_id;

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }

        $query = OutwardFile::where('ministry_id', '!=', $ministryId) // Exclude outward files created by the logged-in ministry
                ->whereHas('recipientMinistries', function ($q) {
                    $q->whereNotNull('ministries.id'); // Ensure there are recipient ministries
                })
                ->orWhereHas('recipientMinistries', function ($q) use ($ministryId) {
                    $q->where('ministries.id', $ministryId); // Check if the ministry is a recipient
                })
                ->whereDoesntHave('owningMinistry', function ($q) use ($ministryId) {
                    $q->where('ministries.id', $ministryId); // Exclude files owned by the logged-in ministry
                }) //Is this code necessary??
                ->with(['owningMinistry:id,name']); // Eager load owning ministry name

    // Select ministry name

        // // Execute the query and get the results
        // $results = $query->get();  // Execute the query

        // // Check if results are empty
        // if ($results->isEmpty()) {
        //     Log::debug('No results found.');
        // } else {
        //     Log::debug('Query Results: ', $results->toArray());
        // }

        // $datatables = DataTables::of($results)->make(true);
        // // dd($datatables);

        // return $datatables;

        return datatables()->of($query)
                            ->addColumn('owning_ministry_name', function ($outwardFile) {
                                return $outwardFile->owningMinistry->first()->name ?? 'N/A'; // Get the first owning ministry
                            })
                            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $dataTables = app(OutwardFileController::class)->getDataTables($request);

        return view('national.eregistry.inward_files.index', ['inwardFiles' => $dataTables]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('file.create')) {
            abort(403, 'Unauthorized action.');
        }

        $folders = $this->folders->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $file_types = $this->file_types->pluck();

        return view('national.eregistry.files.create', [
            'folders' => $folders,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'file_types' => $file_types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('file.store')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('file.show')) {
            abort(403, 'Unauthorized action.');
        }

        $file = $this->files->getById($id);

        return view('national.eregistry.files.show')->with('file', $file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('file.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $file = $this->files->getById($id);
        $folders = $this->folders->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $file_types = $this->file_types->pluck();

        return view('national.eregistry.files.edit', [
            'file' => $file,
            'folders' => $folders,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'file_types' => $file_types,
        ]);
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
        if (!Auth::user()->can('file.update')) {
            abort(403, 'Unauthorized action.');
        }

        $file = $this->files->getById($id);
        $this->files->update($file, $request->all());

        return redirect()->route('file.index')->with('message', 'File updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('file.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $file = $this->files->getById($id);
        $this->files->delete($file);

        return redirect()->route('file.index')->with('message', 'File deleted successfully.');
    }
}
