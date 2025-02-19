<?php

namespace App\Http\Controllers\National\Eregistry;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\MinistryRepository;


class UserController extends Controller
{
    private $users;
    private $ministries;
    private $divisions;

    public function __construct(UserRepository $users,
                                MinistryRepository $ministries,
                                DivisionRepository $divisions)
    {
        $this->users = $users;
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

        // Query to include ministry and roles relationships
        $query = $this->users->getForDataTable($search)
                    ->with(['ministry', 'roles']) // Eager load ministry and roles relationships
                    ->select('users.*'); // Ensure all user fields are selected

        $datatables = DataTables::of($query)
            // Add column for ministry name
            ->addColumn('ministry_name', function ($user) {
                return $user->ministry ? $user->ministry->name : 'N/A'; // Handle cases where ministry is null
            })
            // Add column for roles
            ->addColumn('roles', function ($user) {
                // Pluck the role names and join them with commas
                return $user->roles->pluck('name')->implode(', ') ?: 'No Roles Assigned';
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

        $roles = Role::all(); // Fetch all roles from the database
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();

        return view('national.eregistry.users.create', [
            'roles' => $roles,
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
        // if (!Auth::user()->can('user.store')) {
        //     abort(403, 'Unauthorized action.');
        // }

       // Validate incoming request data
    //    dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'ministry_id' => 'required|exists:ministries,id', // Ensures the ministry exists
            // 'role' => 'required|exits:roles,name',
        ]);

        // Check if the 'secretary' role is being assigned
        if ($request->role === 'Secretary') {
            $secretaryRole = \Spatie\Permission\Models\Role::where('name', 'Secretary')->first();

            // Ensure only one user has the 'secretary' role
            if ($secretaryRole && $secretaryRole->users()->count() > 0) {
                return redirect()->route('registry.users.create')->with('error', 'Secretary can only be assigned to one user!');

            }
        }
        // Create the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'ministry_id' => $request->input('ministry_id'),
            'password' => Hash::make('defaultpassword'), // Set a default password (should be changed later)
        ]);

        $user->syncRoles($request->role);

        // Redirect with success message
        return redirect()->route('registry.users.index')->with('success', 'User created successfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('user.show')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->users->getById($id);

        return view('national.eregistry.users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('user.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->users->getById($id);

        return view('national.eregistry.users.edit')->with('user', $user);
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
        if (!Auth::user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->users->getById($id);
        $this->users->update($user, $request->all());

        return redirect()->route('user.index')->with('message', 'User updated successfully.');
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
