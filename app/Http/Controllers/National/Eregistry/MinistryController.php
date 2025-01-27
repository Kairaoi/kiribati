<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\MinistryRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class MinistryController extends Controller
{
    private $ministries;

    public function __construct(MinistryRepository $ministries)
    {
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
        $query = $this->ministries->getForDataTable($search);
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
        return view('national.eregistry.ministries.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('ministry.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('national.eregistry.ministries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('ministry.store')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $request->all();

        // Validation
        $request->validate([
            'name' => 'required|string|unique:ministries',
            'code' => 'required|string|unique:ministries',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Create the ministry
        $this->ministries->create($input);

        return redirect()->route('ministry.index')->with('message', 'Ministry created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('ministry.show')) {
            abort(403, 'Unauthorized action.');
        }

        $ministry = $this->ministries->getById($id);

        return view('national.eregistry.ministries.show')->with('ministry', $ministry);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('ministry.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $ministry = $this->ministries->getById($id);

        return view('national.eregistry.ministries.edit')->with('ministry', $ministry);
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
        if (!Auth::user()->can('ministry.update')) {
            abort(403, 'Unauthorized action.');
        }

        $ministry = $this->ministries->getById($id);
        $this->ministries->update($ministry, $request->all());

        return redirect()->route('ministry.index')->with('message', 'Ministry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('ministry.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $ministry = $this->ministries->getById($id);
        $this->ministries->delete($ministry);

        return redirect()->route('ministry.index')->with('message', 'Ministry deleted successfully.');
    }
}
