<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\ExternalPartner;
use App\Http\Requests\National\Eregistry\ExternalStoreRequest;
use App\Repositories\National\Eregistry\ExternalPartnerRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\OrganisationTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ExternalPartnerController extends Controller
{

    private $organisation_types;
    private $organisations;
    private $externalPartners;
    
    public function __construct(
       
        OrganisationRepository $organisations,
        OrganisationTypeRepository $organisation_types,
        ExternalPartnerRepository $externalPartners
       
    ) {

        $this->organisations = $organisations;
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
        $organisationId = Auth::user()->organisation_id;

        $search = $request->get('search');

        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->externalPartners->getForDataTable($organisationId, $search);

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

        $organisations = $this->organisations->list();
        $organisationTypes = $this->organisation_types->list();
        $organisation = Auth::user()->organisation; // Get the ministry of the logged-in user
        return view('national.eregistry.external_partners.create', compact('organisations', 
                                                                           'organisationTypes',
                                                                           'organisation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExternalStoreRequest $request)
    {
        $validated = $request->validated();

        //ensure that the user is not creating other ministries partners.
        if (auth()->user()->hasRole('registry')) {
            $validated['organisation_id'] = auth()->user()->organisation_id;
        }

        $this->externalPartners->create($validated);

        return redirect()->route('registry.external-partners.index')->with('success', 'External Partner created');
    }

    public function suggestions(Request $request)
    {
        $query = $request->q;
        $orgId = Auth::user()->ministry_id;

        return ExternalPartner::where('name', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($orgId) {
                $q->where('ministry_id', $orgId)
                ->orWhere('is_global', 1);
            })
            ->distinct()
            ->pluck('name');
      
    }


    /**
     * Display the specified resource.
     */
    public function show(ExternalPartner $externalPartner)
    {
        $totalFiles = $externalPartner->files()
                                ->where('organisation_id', $externalPartner->organisation_id)
                                ->count();

        $activeFiles = $externalPartner->files()
            ->where('organisation_id', $externalPartner->organisation_id)
            ->where('is_active', true)
            ->count();

        return view('national.eregistry.external_partners.show', compact('externalPartner',
                                                                         'activeFiles',
                                                                         'totalFiles'
                                                                ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExternalPartner $externalPartner)
    {
        // dd($externalPartner);
        $organisationPartners = $this->externalPartners->list(Auth::user()->organisation_id);
        $organisations = $this->organisations->list();
        $organisationTypes = $this->organisation_types->list();
    
        return view('national.eregistry.external_partners.edit', compact('externalPartner', 
                                                                        'organisationPartners',
                                                                        'organisationTypes',
                                                                        'organisations'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExternalPartner $externalPartner)
    {

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-()]+$/',
            ],
            'email' => 'nullable|email|max:255',
            'organisation_type_id' => [
                'required',
                'exists:organisation_types,id'
            ],
        ]);

        $externalPartner->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'organisation_type_id' => $validated['organisation_type_id'] ?? null,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->route('registry.external-partners.index')->with('success', 'External Partner updated');
        
    }


    public function activate(ExternalPartner $partner)
    {
        $partner->update([
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'External Partner activated successfully.'
        ]);
    }


    public function deactivate(ExternalPartner $partner)
    {
        $partner->update([
            'is_active' => false,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'External Partner deactivated successfully.'
        ]);
    }
    


}
