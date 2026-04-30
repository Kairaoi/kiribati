<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\ExternalPartner;
use App\Repositories\National\Eregistry\ExternalPartnerRepository;
use App\Repositories\National\Eregistry\IdentityOrganisationRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ExternalPartnerController extends Controller
{

    private $organisation_types;
    private $identityOrganisations;
    private $externalPartners;
    
    public function __construct(
       
        IdentityOrganisationRepository $identityOrganisations,
        OrganisationTypeRepository $organisation_types,
        ExternalPartnerRepository $externalPartners
       
    ) {

        $this->identityOrganisations = $identityOrganisations;
        $this->organisation_types = $organisation_types;
        $this->externalPartners = $externalPartners;
        
    }

    /**
     * Get files for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function getDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->externalPartners->getForDataTable($search);

        $data = $query->get();
        Log::info('External Partners Results:', $data->toArray());
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('national.eregistry.external_partners.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identityOrganisations = $this->identityOrganisations->list();
        $organisationTypes = $this->organisation_types->list();
        $ministry = auth()->user()->ministry;
        return view('national.eregistry.external_partners.create', compact('identityOrganisations', 
                                                                           'organisationTypes',
                                                                           'ministry'));
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          $validated = $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    $name = trim(strtolower($value));
                    $ministryId = auth()->user()->ministry_id;

                    // Check External Partners (scoped to ministry)
                    $existsInPartners = DB::table('external_partners')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->where('ministry_id', $ministryId)
                        ->exists();

                    // Check Identity Organisations (global)
                    $existsInOrgs = DB::table('identity_organisations')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->exists();

                    if ($existsInPartners || $existsInOrgs) {
                        $fail('This name already exists as a registered organisation or external partner.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'identity_organisation_id' => 'nullable|exists:identity_organisations,id',
            'organisation_type_id' => [
                'nullable',
                'required_without:identity_organisation_id',
                'exists:organisation_types,id'
            ],
            
        ]);

        ExternalPartner::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'identity_organisation_id' => $validated['identity_organisation_id'] ?? null,
            'organisation_type_id' => $validated['organisation_type_id'] ?? null,
            'ministry_id' => auth()->user()->ministry_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('registry.external-partners.index')->with('message', 'External Partner created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
