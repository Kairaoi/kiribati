<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Organisation;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class MinistryController extends Controller
{
    private $ministries;
    private $users;

    public function __construct(MinistryRepository $ministries,
                                UserRepository $users
                                )
    {
        $this->users = $users;
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
        $query = $this->organisations->getForDataTable($search);
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
        return view('national.eregistry.organisations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('organisation.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('national.eregistry.organisations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('organisation.store')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $request->all();

        // Validation
        $request->validate([
            'name' => 'required|string|unique:organisations',
            'code' => 'required|string|unique:organisations',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Create the organisation
        $this->organisations->create($input);

        return redirect()->route('organisation.index')->with('message', 'Organisation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('organisation.show')) {
            abort(403, 'Unauthorized action.');
        }

        $organisation = $this->organisations->getById($id);

        return view('national.eregistry.organisations.show')->with('organisation', $organisation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('organisation.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $organisation = $this->organisations->getById($id);

        return view('national.eregistry.organisations.edit')->with('organisation', $organisation);
    }


    public function showReviewOfficer($id)
    {
        // if (!Auth::user()->can('organisation.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $organisation = $this->organisations->getById($id); //logged in organisation
        $usersWithDivision = $this->users->getUsersDivision(); //users with division name
        // dd($usersWithDivision);

        return view('national.eregistry.organisations.show-review-officer', compact('organisation', 
                                                                                    'usersWithDivision'));
    }


    public function updateReviewOfficer(Request $request, $organisationId)
    {
        // if (!Auth::user()->can('organisation.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $validated = $request->validate([
            'review_officer_id' => 'required|exists:users,id',
        ]);

        $organisation = Ministry::find($organisationId);
        $organisation->review_officer_id = $validated['review_officer_id'];
        $organisation->save();

        //Update file circulations assigned to this organisation to the new review officer
        DB::table('file_circulations')
            ->join('file_recipients', 'file_circulations.to_organisation_id', '=', 'file_recipients.organisation_id')
            ->where('file_circulations.to_organisation_id', $organisationId)
            ->whereColumn('file_circulations.file_id', '=', 'file_recipients.file_id') //compare these two columns
            ->whereIn('file_recipients.status', ['Pending Review', 'Pending Circulation'])
            ->update(['to_review_file' => $validated['review_officer_id']]);

        return back()->with('success', 'Review officer updated successfully.');
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
        if (!Auth::user()->can('organisation.update')) {
            abort(403, 'Unauthorized action.');
        }

        $organisation = $this->organisations->getById($id);
        $this->organisations->update($organisation, $request->all());

        return redirect()->route('organisation.index')->with('message', 'Organisation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('organisation.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $organisation = $this->organisations->getById($id);
        $this->organisations->delete($organisation);

        return redirect()->route('organisation.index')->with('message', 'Organisation deleted successfully.');
    }
}
