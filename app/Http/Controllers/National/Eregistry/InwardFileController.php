<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\InwardFileRepository;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


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
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->files->getForDataTable($search);
        $datatables = DataTables::make($query)->make(true);
        return $datatables;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('national.eregistry.files.index');
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

        $input = $request->all();

        // Validation
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'ministry_id' => 'required|exists:ministries,id',
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string',
            'path' => 'required|string',
            'receive_date' => 'required|date',
            'letter_date' => 'required|date',
            'letter_ref_no' => 'required|string',
            'details' => 'nullable|string',
            'from_details_name' => 'required|string',
            'to_details_person_name' => 'required|string',
            'comments' => 'nullable|string',
            'security_level' => 'required|in:public,internal,confidential,strictly_confidential',
            'circulation_status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'file_type_id' => 'required|exists:file_types,id',
        ]);

        // Store the file
        $this->files->create($input);

        return redirect()->route('file.index')->with('message', 'File created successfully.');
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
