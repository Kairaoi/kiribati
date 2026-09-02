<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Http\Requests\National\Eregistry\IdentityOrganisationStoreRequest;
use App\Http\Requests\National\Eregistry\IdentityOrganisationUpdateRequest;
use App\Models\National\Eregistry\IdentityOrganisation;
use App\Repositories\National\Eregistry\IdentityOrganisationRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Models\National\Eregistry\Ministry;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdentityOrganisationController extends Controller
{

    private $organisations;
    private $organisation_types;
    private $ministries;
    private $files;

    public function __construct(IdentityOrganisationRepository $organisations,
                                OrganisationTypeRepository $organisation_types,
                                MinistryRepository $ministries,
                                FileRepository $files,
                                )
    {
        $this->organisations = $organisations;
        $this->organisation_types = $organisation_types;
        $this->ministries = $ministries;
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
        $query = $this->organisations->getForDataTable($selectedType, $search);
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
        $isMinistryAdmin = auth()->user()->hasRole('ministry-admin');
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
        $organisationTypes = $this->organisation_types->listAll();

        return view('national.eregistry.organisations.create', compact('organisationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IdentityOrganisationStoreRequest $request)
    {
        $validated = $request->validated();

        $organisation = $this->organisations->create($validated);
        
        $ministryTypeId = $this->organisation_types->getIdByName('Ministry');
        
        if($validated['organisation_type_id'] == $ministryTypeId) {
            Ministry::create([
                'identity_organisation_id' => $organisation->id,
                'organisation_type_id' => $validated['organisation_type_id'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
        
        return redirect()->route('registry.organisations.index')->with('success', 'Organisation created successfully.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(IdentityOrganisation $organisation)
    {
        $isMinistry = $organisation->type->name === 'Ministry';
        $isSystemAdmin = auth()->user()->hasRole('system-admin');
        $ministryLogoPath = $organisation->ministry?->logo_path ?? '';

        $ministryEmail =  $organisation->ministry?->email?? '-';
        $ministryPhone =  $organisation->ministry?->phone?? '-';
        $organisation->load([
            'ministry' => function ($query) {
                $query->withCount('users');
            },
        ]);

        $totalUsers = $organisation->ministry?->users_count ?? 0;

        $organisation->load([
            'ministry' => function ($query) {
                $query->withCount('files');
            },
        ]);

        $totalFiles = $organisation->ministry?->files_count ?? 0;
        $ministry = $organisation->ministry;
        return view('national.eregistry.organisations.show', compact('organisation',
                                                                     'isMinistry',
                                                                     'isSystemAdmin',
                                                                     'ministryLogoPath',
                                                                     'ministryEmail',
                                                                     'ministryPhone',
                                                                     'totalUsers',
                                                                     'totalFiles',
                                                                     'ministry'));

    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdentityOrganisation $organisation)
    {
        $organisationTypes = $this->organisation_types->listAll();

        return view('national.eregistry.organisations.edit', compact('organisation',
                                                                     'organisationTypes'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(IdentityOrganisationUpdateRequest $request, IdentityOrganisation $organisation)
    {
        $validated = $request->validated();

        
        $organisation = $this->organisations->update($organisation, $validated);

        $ministryTypeId = $this->organisation_types->getIdByName('Ministry');
        
         if ((int) $validated['organisation_type_id'] === (int) $ministryTypeId) {
            Ministry::updateOrCreate(
                [
                    'identity_organisation_id' => $organisation->id,
                ],
                [
                    'organisation_type_id' => $ministryTypeId,
                    'updated_by'           => auth()->id(),
                    'created_by'           => auth()->id(),
                ]
            );
        }

        return redirect()->route('registry.organisations.index')->with('success', 'Organisation updated.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
