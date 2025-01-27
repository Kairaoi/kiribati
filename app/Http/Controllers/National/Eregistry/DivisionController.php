<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class DivisionController extends Controller
{
    private $divisions;
    private $ministries;

    public function __construct(DivisionRepository $divisions, MinistryRepository $ministries)
    {
        $this->divisions = $divisions;
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
        $query = $this->divisions->getForDataTable($search);
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
        return view('national.eregistry.divisions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('division.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $ministries = $this->ministries->pluck();

        return view('national.eregistry.divisions.create')->with('ministries', $ministries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (!Auth::user()->can('division.store')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $input = $request->all();

        // Validation
        $request->validate([
            'ministry_id' => 'required|exists:ministries,id',
            'name' => 'required|string',
            'code' => 'required|string|unique:divisions',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Create the division
        $this->divisions->create($input);

        return redirect()->route('division.index')->with('message', 'Division created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if (!Auth::user()->can('division.show')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);

        return view('national.eregistry.divisions.show')->with('division', $division);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (!Auth::user()->can('division.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);
        $ministries = $this->ministries->pluck();

        return view('national.eregistry.divisions.edit', [
            'division' => $division,
            'ministries' => $ministries,
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
        // if (!Auth::user()->can('division.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);
        $this->divisions->update($division, $request->all());

        return redirect()->route('division.index')->with('message', 'Division updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // if (!Auth::user()->can('division.delete')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);
        $this->divisions->delete($division);

        return redirect()->route('division.index')->with('message', 'Division deleted successfully.');
    }
}
