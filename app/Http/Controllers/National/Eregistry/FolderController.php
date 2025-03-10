<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class FolderController extends Controller
{
    private $folders;
    private $ministries;

    public function __construct(FolderRepository $folders, MinistryRepository $ministries)
    {
        $this->folders = $folders;
        $this->ministries = $ministries;
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
        $query = $this->folders->getForDataTable($search);
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
        return view('national.eregistry.folders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('folder.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $ministries = $this->ministries->pluck();

        return view('national.eregistry.folders.create')->with('ministries', $ministries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (!Auth::user()->can('folder.store')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $input = $request->all();

        // Validation
        $request->validate([
            'index_no' => 'required|string|unique:folders',
            'folder_name' => 'required|string',
            'folder_description' => 'nullable|string',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Create the folder
        $this->folders->create($input);

        return redirect()->route('registry.folder.index')->with('message', 'Folder created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('folder.show')) {
            abort(403, 'Unauthorized action.');
        }

        $folder = $this->folders->getById($id);

        return view('national.eregistry.folders.show')->with('folder', $folder);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('folder.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $folder = $this->folders->getById($id);

        return view('national.eregistry.folders.edit')->with('folder', $folder);
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
        if (!Auth::user()->can('folder.update')) {
            abort(403, 'Unauthorized action.');
        }

        $folder = $this->folders->getById($id);
        $this->folders->update($folder, $request->all());

        return redirect()->route('folder.index')->with('message', 'Folder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('folder.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $folder = $this->folders->getById($id);
        $this->folders->delete($folder);

        return redirect()->route('folder.index')->with('message', 'Folder deleted successfully.');
    }
}
