<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\FileAccessRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class FileAccessController extends Controller
{
    private $file_access;
    private $files;
    private $ministries;
    private $divisions;

    public function __construct(
        FileAccessRepository $file_access,
        FileRepository $files,
        MinistryRepository $ministries,
        DivisionRepository $divisions
    )
    {
        $this->file_access = $file_access;
        $this->files = $files;
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
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->file_access->getForDataTable($search);
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
        return view('national.eregistry.file_access.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('file_access.create')) {
            abort(403, 'Unauthorized action.');
        }

        $files = $this->files->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();

        return view('national.eregistry.file_access.create', [
            'files' => $files,
            'ministries' => $ministries,
            'divisions' => $divisions,
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
        if (!Auth::user()->can('file_access.store')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $request->all();

        // Validation
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'ministry_id' => 'required|exists:ministries,id',
            'division_id' => 'required|exists:divisions,id',
            'access_type' => 'required|in:view,edit,full',
            'is_active' => 'nullable|boolean',
        ]);

        // Store the file access record
        $this->file_access->create($input);

        return redirect()->route('file_access.index')->with('message', 'File access created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('file_access.show')) {
            abort(403, 'Unauthorized action.');
        }

        $file_access = $this->file_access->getById($id);

        return view('national.eregistry.file_access.show')->with('file_access', $file_access);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('file_access.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $file_access = $this->file_access->getById($id);
        $files = $this->files->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();

        return view('national.eregistry.file_access.edit', [
            'file_access' => $file_access,
            'files' => $files,
            'ministries' => $ministries,
            'divisions' => $divisions,
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
        if (!Auth::user()->can('file_access.update')) {
            abort(403, 'Unauthorized action.');
        }

        $file_access = $this->file_access->getById($id);
        $this->file_access->update($file_access, $request->all());

        return redirect()->route('file_access.index')->with('message', 'File access updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('file_access.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $file_access = $this->file_access->getById($id);
        $this->file_access->delete($file_access);

        return redirect()->route('file_access.index')->with('message', 'File access deleted successfully.');
    }
}
