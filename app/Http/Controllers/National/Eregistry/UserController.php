<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\UnitRepository;
use Spatie\Permission\Models\Role;
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
    private $ministries;
    private $units;

    public function __construct(UserRepository $users,
                                DivisionRepository $divisions,
                                MinistryRepository $ministries,
                                UnitRepository $units)
    {
        $this->users = $users;
        $this->divisions = $divisions;
        $this->ministries = $ministries;
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
        $this->authorize('viewAny', User::class);
        
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        
        $query = $this->users->getForDataTable($search, Auth::user());
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
        $this->authorize('viewAny', User::class);

        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', Auth::user()->ministry_id)
                                ->first();

        $sro = User::role('sro')
                    ->where('ministry_id', Auth::user()->ministry_id)
                    ->first();

        $ministry = $this->ministries->getReviewerTitle(Auth::user()->ministry_id);
        
        return view('national.eregistry.users.index', compact('reviewOfficer', 'sro', 'ministry'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $ministryId = Auth::user()->ministry_id;
        $divisions = $this->divisions->listWithMinistry($ministryId); // Fetch divisions for the logged-in ministry
        $units = $this->units->listWithMinistry($ministryId); // Fetch units for the logged-in ministry
        $ministries = $this->ministries->listAll();
        $roles = Role::query()
                    ->whereNotIn('name', [
                        'system-admin',
                        'ministry-admin',
                        'hod',
                        'admin',
                        'review-officer',
                        'sro'
                    ])
                    ->pluck('name', 'id');
        
        return view('national.eregistry.users.create', compact('divisions', 'units', 'roles', 'ministries'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'division_id' => 'required|integer|exists:divisions,id',
            'designation' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'role'    => [
                'required',
                Rule::exists('roles', 'id'),
            ],

        ]);

       $user = User::create([
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'division_id' => $request->division_id,
            'ministry_id' => Auth::user()->ministry_id,
            'designation' => $request->designation,
            'is_active'   => $request->is_active ?? true
        ]);
    
        $role = Role::findOrFail($request->role);
        $user->assignRole($role);

        return redirect()->route('registry.users.index')->with('success', 'New user created!');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

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
        $this->authorize('view', $user);
        $divisions = $this->divisions->listWithMinistry(Auth::user()->ministry_id); // Fetch divisions for the logged-in organisation
        $units = $this->units->listWithMinistry(Auth::user()->ministry_id); // Fetch units for the logged-in ministry
        $roles = Role::query()
                    ->whereNotIn('name', [
                        'system-admin',
                        'ministry-admin',
                        'hod',
                        'admin',
                        'review-officer',
                        'sro'
                    ])
                    ->pluck('name', 'id');

        $currentRole = $user->roles->whereIn('name', $roles->values())
                                   ->first();
        
        return view('national.eregistry.users.edit', compact('user', 
                                                             'currentRole',
                                                             'divisions', 
                                                             'units', 
                                                             'roles'));
    }


    public function editSignature()
    {
        $user = Auth::user();
        return view('national.eregistry.users.edit_signature', compact('user'));
    }


    public function updateSignature(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:3072', // 3 MB
        ], [
            'signature.required' => 'Please upload your signature.',
            'signature.image'    => 'The signature must be an image.',
            'signature.mimes'    => 'The signature must be a JPG or PNG image.',
            'signature.max'      => 'The signature must not exceed 2 MB.',
        ]);

        $user = Auth::user();
        // dd($user);
        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            $filename = 'signature_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                'signatures',
                $filename,
                'public'
            );
            $user->signature_path = $path;
            $user->save();
        }
        return redirect()->route('registry.users.signature.edit')->with('success', 'Signature updated successfully!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // dd($request->all());
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::exists('roles', 'id'),
            ],
            'division_id' => 'required|integer|exists:divisions,id',
            'designation' => 'required|string|max:255',
        ]);

        $this->users->update($user, $validated);

        // Handle the editable/main role separately
        $this->users->updateMainRole($user, $validated['role']);

        return redirect()->route('registry.users.index')->with('success', 'User updated successfully.');
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
        $ministryId = Auth::user()->ministry_id;
        $usersWithDivision = $this->users->getUsersDivision();
        $reviewOfficer = User::role('review-officer')
                                ->where('ministry_id', $ministryId)
                                ->first();

        return view('national.eregistry.users.editReviewOfficer', compact('usersWithDivision', 
                                                                        'reviewOfficer',));
    }


    //edit page for Secretary or Chairperson or Auditor General, etc in other organisations
    public function editSecretary()
    {
        $usersWithDivision = $this->users->getUsersDivision();
        $sro= User::role('sro')
                    ->where('ministry_id', Auth::user()->ministry_id)
                    ->first();

        $ministry = $this->ministries->getReviewerTitle(Auth::user()->ministry_id);

        return view('national.eregistry.users.editSecretary', compact('usersWithDivision', 'sro', 'ministry'));
    }


    //update Secretary or Chairperson or Auditor General, etc in other organisations
    public function updateSecretary(Request $request)
    {
        $ministryId = Auth::user()->ministry_id;
        $validated = $request->validate([
            'secretary_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($ministryId) {
                    $query->where('ministry_id', $ministryId);
                }),
            ],
        ]);

        $currentReviewOfficer = User::role('sro')
            ->where('ministry_id', $ministryId)
            ->first();

        if ($currentReviewOfficer) {
            $currentReviewOfficer->removeRole('sro');
        }

        $newReviewOfficer = User::find($validated['secretary_id']);
        $newReviewOfficer->assignRole('sro');

        return back()->with('success', 'Review officer updated successfully.');
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
        $ministryId = Auth::user()->ministry_id;
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
     * Deactivate the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate(User $user)
    {
        $user->update([
            'is_active' => false,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'User deactivated.'
        ]);
    }

    public function activate(User $user)
    {
        $user->update([
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'User activated.'
        ]);
    }
}
