<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\FileTypeRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class FileTypeController extends Controller {

    private $fileTypes;

    public function __construct(FileTypeRepository $fileTypes)
    {
        $this->fileTypes = $fileTypes;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->get('search', '') ;
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->fileTypes->getForDataTable($search);
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
        $fileTypes = $this->fileTypes->pluck();
        return view('national.eregistry.file_types.create')->with('fileTypes', $fileTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('file_type.store')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $request->all();

        // Validate the input here, for example:
        // $request->validate([
        //     'name' => 'required|string|unique:file_types',
        //     'description' => 'nullable|string',
        // ]);

        $this->fileTypes->create($input);

        return redirect()->route('file_type.index')->with('message', 'File Type created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('file_type.show')) {
            abort(403, 'Unauthorized action.');
        }

        $fileType = $this->fileTypes->getById($id);

        return view('national.eregistry.file_types.show')->with('fileType', $fileType);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('file_type.edit')) {
            abort(403, 'Unauthorized action.');
        }

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
