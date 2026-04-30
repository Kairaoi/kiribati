<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;

use App\Repositories\National\Eregistry\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{
    private $users;
    private $divisions;
    private $organisations;


    public function __construct(UserRepository $users,
                                DivisionRepository $divisions)
    {
        $this->users = $users;
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
        $query = $this->users->getForDataTable($search);
        $datatables = DataTables::make($query)
                                ->addColumn('role_name', function ($user) {
                                    return $user->role_name ?? '';
                                })
                                ->addColumn('division_name', function ($user) {
                                    return $user->division_name ?? '';
                                })
                                
                                ->make(true);
        return $datatables;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('national.eregistry.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('user.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $organisationId = Auth::user()->organisation_id;
        $divisions = $this->divisions->listWithOrganisation($organisationId); // Fetch divisions for the logged-in organisation
        $roles = Role::all();
        return view('national.eregistry.users.create', compact('divisions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Authorization (uncomment if needed)
        // if (!Auth::user()->can('user.store')) {
        //     abort(403, 'Unauthorized action.');
        // }
    
        // Validate and extract only the allowed fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|string|exists:roles,name',
            'division_id' => 'required|integer|exists:divisions,id',
            'organisation_id' => 'required|integer|exists:organisations,id',
            'is_active' => 'sometimes|boolean',
        ]);
    
        // Remove 'role' from input so it doesn't go into the model (if present)
        // $role = $input['role'];
        // unset($input['role']);

    
        // Create the user
        $user = $this->users->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password, // Hashing is handled in the repository
            'division_id' => $request->division_id,
            'organisation_id' => $request->organisation_id,
            'is_active' => $request->is_active ?? true, // Default to true if not provided
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

    
        // Assign role via Spatie
        $user->assignRole($request->role);
        // dd($user);
        return redirect()->route('registry.users.index')->with('success', 'New user created successfully!');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // if (!auth()->user()->hasRole(['registry','admin'])) {
        //     abort(403, 'Unauthorized access');
        // }

        $user = $this->users->getById($user->id);
        
        return view('national.eregistry.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // if (!Auth::user()->can('user.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        // $user = $this->users->getById($id);
        $organisationId = Auth::user()->organisation_id;
        $divisions = $this->divisions->listWithOrganisation($organisationId); // Fetch divisions for the logged-in organisation
        $roles = Role::all();
        
        return view('national.eregistry.users.edit', compact('user', 'divisions', 'roles'));
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
        // if (!Auth::user()->can('user.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $user = $this->users->getById($id);
        $this->users->update($user, $request->all());

        return redirect()->route('user.index')->with('message', 'User updated successfully.');
    }


     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editReviewOfficer()
    {
        $ministryId = auth()->user()->ministry_id;
        $usersWithDivision = $this->users->getUsersDivision();
        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
                                ->first();

        return view('national.eregistry.users.editReviewOfficer', compact('usersWithDivision', 'reviewOfficer'));
    }


         /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReviewOfficer(Request $request)
    {
        $ministryId = auth()->user()->ministry_id;
        $request->validate([
            'review_officer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($ministryId) {
                    $query->where('ministry_id', $ministryId);
                }),
            ],
        ]);
        $currentReviewOfficer = User::role('review-officer')
            ->where('ministry_id', $ministryId)
            ->first();

        if ($currentReviewOfficer) {
            $currentReviewOfficer->removeRole('review-officer');
        }

        $newReviewOfficer = User::find($request->review_officer_id);
        $newReviewOfficer->assignRole('review-officer');
        return back()->with('success', 'Review officer updated successfully.');

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->users->getById($id);
        $this->users->delete($user);

        return redirect()->route('user.index')->with('message', 'User deleted successfully.');
    }
}
