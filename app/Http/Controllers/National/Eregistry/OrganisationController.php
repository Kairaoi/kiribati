<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Http\Requests\National\Eregistry\OrganisationStoreRequest;
use App\Http\Requests\National\Eregistry\OrganisationUpdateRequest;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Models\National\Eregistry\Organisation;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganisationController extends Controller
{

    private $organisations;
    private $organisation_types;
    private $files;

    public function __construct(OrganisationRepository $organisations,
                                OrganisationTypeRepository $organisation_types,
                                FileRepository $files,
                                )
    {
        $this->organisations = $organisations;
        $this->organisation_types = $organisation_types;
        $this->files = $files;
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
        $query = $this->organisations->getForDataTable($selectedType, 'id', 'asc', $search);
        $datatables = DataTables::make($query)->make(true);
        return $datatables;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isSystemAdmin = auth()->user()->hasRole('system-admin');
        $isRegistry = auth()->user()->hasRole('registry');
        $isMinistryAdmin = auth()->user()->hasRole('admin');
        $organisations = $this->organisations->listAll();
        return view('national.eregistry.organisations.index', compact('organisations',
                                                                     'isSystemAdmin',
                                                                     'isRegistry',
                                                                     'isMinistryAdmin'));

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Organisation::class);

        $organisationTypes = $this->organisation_types->listAll();

        return view('national.eregistry.organisations.create', compact('organisationTypes'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganisationStoreRequest $request)
    {
        $this->authorize('create', Organisation::class);

        $validated = $request->validated();

        $validated['logo'] = $request->hasFile('logo')
            ? $request->file('logo')->store('organisation-logos', 'public')
            : null;

        $this->organisations->create($validated);
                
        return redirect()->route('registry.organisations.index')->with('success', 'Organisation created successfully.');
        
    }


    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation)
    {
        $isMinistry = $organisation->type->name === 'Ministry';

        $isSystemAdmin = auth()->user()->hasRole('system-admin');

        $ministryLogoPath = $organisation->logo_path ?? '';

        $ministryEmail =  $organisation->email?? '-';
        $ministryPhone =  $organisation->phone?? '-';

        $organisation->loadCount('users');
        $organisation->loadCount('files');

        $totalUsers = $organisation->users_count ?? 0;
        $totalFiles = $organisation->files_count ?? 0;

        return view('national.eregistry.organisations.show', compact('organisation',
                                                                     'isMinistry',
                                                                     'isSystemAdmin',
                                                                     'ministryLogoPath',
                                                                     'ministryEmail',
                                                                     'ministryPhone',
                                                                     'totalUsers',
                                                                     'totalFiles',
                                                                     ));

    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation)
    {

        $this->authorize('update', $organisation);

        $organisationTypes = $this->organisation_types->listAll();

        return view('national.eregistry.organisations.edit', compact('organisation',
                                                                     'organisationTypes'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(OrganisationUpdateRequest $request, Organisation $organisation)
    {

        $this->authorize('update', $organisation);

        $validated = $request->validated();

        $validated['logo'] = $request->hasFile('logo')
            ? $request->file('logo')->store('organisation-logos', 'public')
            : null;

        // Only system-admin can change these fields (if users insert values from the browser inspector tool)
        if (!$request->user()->hasRole('system-admin')) {
            unset($validated['name'],
                  $validated['code'],
                  $validated['organisation_type_id'],
                  $validated['reviewer_title'],
                );
        }

        $this->organisations->update($organisation, $validated);
    
        return redirect()->route('registry.organisations.edit', $organisation)->with('success', 'Organisation updated');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
