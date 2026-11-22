<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\UnitRepository;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\National\Eregistry\DivisionUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\HodAppointment;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;


class HodAppointmentController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('create', Division::class);
        $user = auth()->user();
        
        $isRegistry = auth()->user()->hasRole('registry');

        $divisions = $isRegistry
            ? $this->divisions->listWithOrganisation(auth()->user()->organisation_id)
            : collect();

        $users = User::query()
            ->withoutRole('hod')
            ->where('organisation_id', $user->organisation_id)
            ->where('is_active', true)
            ->whereNotNull('division_id')
            ->when(!$isRegistry, function ($query) use ($user) {
                $query->where('division_id', $user->division_id);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $currentAppointments = HodAppointment::query()
            // ->active()
            ->with(['division', 'officer'])
            ->whereHas('division', function ($query) use ($user, $isRegistry) {
                $query->where('organisation_id', $user->organisation_id);

                if (!$isRegistry) {
                    $query->whereKey($user->division_id)
                        ->where('hod_id', $user->id);
                }
            })
            ->orderBy('ends_at')
            ->get();

        return view('national.eregistry.hod_assignments.create', compact('divisions',
                                                                   'isRegistry',
                                                                   'users',
                                                                   'user',
                                                                   'currentAppointments'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $isRegistry = $user->hasRole('registry');

        abort_unless(
            $user->organisation_id && ($isRegistry || $user->hasRole('hod')),
            403
        );

        // dd($request);

        $validated = $request->validate([
            'division_id' => $isRegistry
                ? [
                    'bail',
                    'required',
                    'integer',
                    Rule::exists('divisions', 'id')
                        ->where('organisation_id', $user->organisation_id),
                ]
                : ['exclude'],

            'user_id' => ['required', 'integer'],
            'start_date' => ['bail', 'required', 'date_format:Y-m-d'],
            'end_date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
        ]);

        $divisionId = $isRegistry
            ? $validated['division_id']
            : $user->division_id;

        if (!$isRegistry) {
            $headsDivision = Division::query()
                ->whereKey($divisionId)
                ->where('organisation_id', $user->organisation_id)
                ->where('hod_id', $user->id)
                ->exists();

            abort_unless($headsDivision, 403);
        }

        $officerIsEligible = User::query()
            ->whereKey($validated['user_id'])
            ->where('organisation_id', $user->organisation_id)
            ->where('division_id', $divisionId)
            ->where('is_active', true)
            ->withoutRole('hod')
            ->exists();

        if (!$officerIsEligible) {
            throw ValidationException::withMessages([
                'user_id' => 'Select an active officer in this division who does not hold the HOD role.',
            ]);
        }

        HodAppointment::create([
            'division_id' => $divisionId,
            'officer_id' => $validated['user_id'],
            'appointed_by' => $user->id,
            'starts_at' => Carbon::parse($validated['start_date'])->startOfDay(),
            'ends_at' => Carbon::parse($validated['end_date'])->endOfDay(),
        ]);

        return back()->with('success', 'Temporary HOD appointed successfully.');

    }


    public function revokeHod(Request $request, HodAppointment $appointment)
    {
        $user = $request->user();
        $division = $appointment->division;

        $isRegistry = $user->hasRole('registry');
        $isPermanentHod = $user->hasRole('hod')
            && (int) $division->hod_id === (int) $user->id
            && (int) $division->id === (int) $user->division_id;

        abort_unless(
            $user->organisation_id
                && (int) $division->organisation_id === (int) $user->organisation_id
                && ($isRegistry || $isPermanentHod),
            403
        );

        $revoked = HodAppointment::query()
            ->whereKey($appointment->id)
            ->active()
            ->update(['revoked_at' => now()]);

        if (!$revoked) {
            return back()->with('error', 'This temporary HOD appointment is no longer active.');
        }

        return back()->with('success', 'Temporary HOD appointment revoked.');
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
