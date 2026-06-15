<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\IdentityOrganisationRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class IdentityOrganisationController extends Controller
{

    private $organisations;

    public function __construct(IdentityOrganisationRepository $organisations,
                               
                                )
    {
    
        $this->organisations = $organisations;
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
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->organisations->getForDataTable($selectedType, $search);
        $datatables = DataTables::make($query)->make(true);
        return $datatables;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organisations = $this->organisations->listAll();
        // dd($organisations);
        return view('national.eregistry.organisations.index', compact('organisations'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
