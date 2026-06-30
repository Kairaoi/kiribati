<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\National\Eregistry\Division;
use App\Models\User;

class DivisionController extends Controller
{
    private $divisions;
    private $ministries;
    private $users;

    public function __construct(DivisionRepository $divisions, 
                                MinistryRepository $ministries,
                                UserRepository $users)
    {
        $this->divisions = $divisions;
        $this->ministries = $ministries;
        $this->users = $users;
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
    
        $user = Auth::user();
        $ministryId = Auth::user()->ministry_id;
        $query = $this->divisions->getForDataTable($search, 'id', 'asc', $ministryId, $user);
    
        return DataTables::of($query)
                    ->addColumn('can_delete', function ($row) {
                        return auth()->user()->hasRole('system-admin');
                    })
                    ->addColumn('can_edit', function ($row) {
                        return auth()->user()->hasRole('system-admin');
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
        return view('national.eregistry.divisions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('Division.create')) {
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
        $users = $this->users->getDivisionUsers($id);

        return view('national.eregistry.divisions.show', compact('division', 'users'));
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
        $users = $this->users->getDivisionUsers(auth()->user()->division_id);
        $ministries = $this->ministries->pluck();

        return view('national.eregistry.divisions.edit', [
            'division' => $division,
            'ministries' => $ministries,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Division $division)
    {
        // if (!Auth::user()->can('division.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        $division->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('registry.divisions.index')->with('message', 'Division updated successfully.');
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
