<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Services\DivisionService;
use App\Http\Requests\National\Eregistry\UserStoreRequest;
use App\Http\Requests\National\Eregistry\UserUpdateRequest;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
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
    private $organisations;
    private $units;

    public function __construct(UserRepository $users,
                                DivisionRepository $divisions,
                                OrganisationRepository $organisations,
                                UnitRepository $units)
    {
        $this->users = $users;
        $this->divisions = $divisions;
        $this->organisations = $organisations;
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
                                ->where('organisation_id', auth()->user()->organisation_id)
                                ->first();

        $sro = User::role('sro')->where('organisation_id', auth()->user()->organisation_id)
                                ->first();

        $user = Auth::user();

        $isSystemAdmin = auth()->user()->hasRole('system-admin');
        $isRegistry = auth()->user()->hasRole('registry');
        $isMinistryAdmin = auth()->user()->hasRole('ministry-admin');
        $organisation = $this->organisations->getReviewerTitle(Auth::user()->organisation_id);
        
        return view('national.eregistry.users.index', compact('reviewOfficer', 
                                                              'sro', 
                                                              'user',
                                                              'organisation',
                                                              'isSystemAdmin',
                                                              'isRegistry',
                                                              'isMinistryAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $isSystemAdmin = auth()->user()->hasRole('system-admin');

        $user = Auth::user();

        $divisions = $this->divisions->listWithOrganisation(
            $isSystemAdmin ? null : $user->organisation_id
        );

        $organisations = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        $roles = Role::query()->when(
                        !$isSystemAdmin,
                        fn ($query) => $query->whereNotIn('name', [
                            'system-admin',
                            'hod',
                            'admin',
                            'review-officer',
                            'sro'
                        ])
                    )
                    ->pluck('name', 'id');
        
        return view('national.eregistry.users.create', compact('divisions', 
                                                                'user',
                                                                'roles', 
                                                                'organisations',
                                                                'isSystemAdmin'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        
        $validated = $request->validated();
        
        //ensure that the user is not creating other ministries user.
        if (auth()->user()->hasRole('ministry-admin')) {
            $validated['organisation_id'] = auth()->user()->organisation_id;
        }
        
        $user = $this->users->create($validated);

        if ($user->hasRole('hod')) {
            $division = $this->divisions->getById($user->division_id);

            app(DivisionServices::class)->updateHod($division, $user);
        } else {
            $user->assignRole($validated['role']);
        }


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
        $this->authorize('update', $user);

        $isSystemAdmin = auth()->user()->hasRole('system-admin');
        
        $organisations = $isSystemAdmin ? $this->organisations->listMinistries() : collect();

        $divisions = $this->divisions->listWithOrganisation(
                    $isSystemAdmin ? null : $user->organisation_id
                );        
        // dd($divisions);
        $roles = ($isSystemAdmin 
                ? Role::query() 
                : Role::query()->whereNotIn('name', [
                        'system-admin',
                        'ministry-admin',
                        'hod',
                        'admin',
                        'review-officer',
                        'sro'
                    ])
                )->pluck('name', 'id');

        $currentRole = $user->roles->whereIn('name', $roles->values())
                                   ->first();
        
        return view('national.eregistry.users.edit', compact('user', 
                                                             'isSystemAdmin',
                                                             'currentRole',
                                                             'divisions', 
                                                             'roles',
                                                             'organisations'));
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
    public function update(UserUpdateRequest $request, User $user)
    {
    
        $validated = $request->validated();
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
        $organisationId = Auth::user()->organisation_id;
        $usersWithDivision = $this->users->getUsersDivision();
        $reviewOfficer = User::role('review-officer')
                                ->where('organisation_id', $organisationId)
                                ->first();

        return view('national.eregistry.users.editReviewOfficer', compact('usersWithDivision', 
                                                                        'reviewOfficer',));
    }


    //edit page for Secretary or Chairperson or Auditor General, etc in other organisations
    public function editSecretary()
    {
        $usersWithDivision = $this->users->getUsersDivision();
        $sro= User::role('sro')
                    ->where('organisation_id', Auth::user()->organisation_id)
                    ->first();

        $organisation = $this->organisations->getReviewerTitle(Auth::user()->organisation_id);

        return view('national.eregistry.users.editSecretary', compact('usersWithDivision', 'sro', 'organisation'));
    }


    //update Secretary or Chairperson or Auditor General, etc in other organisations
    public function updateSecretary(Request $request)
    {
        $organisationId = Auth::user()->organisation_id;
        $validated = $request->validate([
            'secretary_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($organisationId) {
                    $query->where('organisation_id', $organisationId);
                }),
            ],
        ]);

        $currentReviewOfficer = User::role('sro')
            ->where('organisation_id', $organisationId)
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
        $organisationId = Auth::user()->organisation_id;

        $request->validate([
            'review_officer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($organisationId) {
                    $query->where('organisation_id', $organisationId);
                }),
            ],
        ]);

        $currentReviewOfficer = User::role('review-officer')
            ->where('organisation_id', $organisationId)
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
