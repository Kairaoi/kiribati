<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\UnitRepository;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\National\Eregistry\DivisionStoreRequest;
use App\Http\Requests\National\Eregistry\DivisionUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\National\Eregistry\Division;
use App\Models\User;

class DivisionController extends Controller
{
    private $divisions;
    private $organisations;
    private $users;
    private $units;

    public function __construct(DivisionRepository $divisions, 
                                OrganisationRepository $organisations,
                                UserRepository $users,
                                UnitRepository $units)
    {
        $this->divisions = $divisions;
        $this->organisations = $organisations;
        $this->users = $users;
        $this->units = $units;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        $this->authorize('viewAny', Division::class);

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
    
        $user = Auth::user();
        $organisationId = Auth::user()->organisation_id;
        $query = $this->divisions->getForDataTable($search, 'id', 'asc', $organisationId, $user);
    
        return DataTables::of($query)
                    ->addColumn('can_delete', function ($row) {
                        return Auth::user()->hasRole('system-admin');
                    })
                    ->addColumn('can_edit', function ($row) {
                        return Auth::user()->hasRole('system-admin');
                    })
                    ->addColumn('hod_name', function ($row) {
                        return $row->hod_name;
                    })
                    ->make(true);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Division::class);

        return view('national.eregistry.divisions.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Division::class);
        
        $isSystemAdmin = auth()->user()->hasRole('system-admin');

        $user = Auth::user();

        $organisations = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        return view('national.eregistry.divisions.create', compact('organisations',
                                                                    'user',
                                                                    'isSystemAdmin'                                                    ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DivisionStoreRequest $request)
    {
        $validated = $request->validated();

        if (auth()->user()->hasRole('registry')) {
            $validated['organisation_id'] = auth()->user()->organisation_id;
        }

        $this->divisions->create($validated);
    
        return redirect()->route('registry.divisions.index')->with('success', 'Division created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Division $division)
    {

        // $this->authorize('view', $division);
        $isRegistry = auth()->user()->hasRole('registry');
        $isSystemAdmin = auth()->user()->hasRole('system-admin');
        return view('national.eregistry.divisions.show', compact('division', 'isRegistry', 'isSystemAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $division)
    {

        $this->authorize('create', Division::class);
        
        $isSystemAdmin = auth()->user()->hasRole('system-admin');

        $user = Auth::user();

        $organisations = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        return view('national.eregistry.divisions.edit', compact('division',
                                                                 'organisations',
                                                                 'user',
                                                                 'isSystemAdmin'                                                 
                                                                ));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DivisionUpdateRequest $request, Division $division)
    {

        $validated = $request->validated();

        // Only system-admin can change these fields (if users insert values from the browser inspector tool)
        if (!$request->user()->hasRole('system-admin')) {
            unset($validated['organisation_id']);
        }

        $this->divisions->update($division, $validated);

        return redirect()->route('registry.divisions.index')->with('success', 'Division updated.');
    }

    
    public function assignHod(Division $division)
    {
        
        $users = $this->users->getDivisionUsers($division->id);

        return view('national.eregistry.divisions.assignHod', compact('division', 'users'));
    }



    public function updateHod(Request $request, Division $division)
    {
        $validated = $request->validate([
            'hod_id' => ['required', 'exists:users,id'],
        ]);

        // Ensure the selected user belongs to this division
        $user = User::where('division_id', $division->id)
            ->findOrFail($validated['hod_id']);

        // Remove the HOD role from the previous HOD (if different)
        if ($division->hod_id && $division->hod_id != $user->id) {
            $previousHod = User::find($division->hod_id);

            if ($previousHod) {
                $previousHod->removeRole('hod');
            }
        }

        // Assign the HOD role to the selected user
        if (! $user->hasRole('hod')) {
            $user->assignRole('hod');
        }

        // Update the division
        $division->update([
            'hod_id' => $user->id,
        ]);

        return redirect()
            ->route('registry.divisions.index')
            ->with('success', 'Head of Division assigned successfully.');
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

        return redirect()->route('division.index')->with('message', 'division deleted successfully.');
    }
}
