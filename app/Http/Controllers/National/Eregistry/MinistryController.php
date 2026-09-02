<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Ministry;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\National\Eregistry\MinistryStoreRequest;


class MinistryController extends Controller
{
    
    private $ministries;
    private $users;

    public function __construct(MinistryRepository $ministries,
                                UserRepository $users
                                )
    {
        $this->users = $users;
        $this->ministries = $ministries;
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
        $query = $this->ministries->getForDataTable($search);
        $datatables = DataTables::make($query)->make(true);
        return $datatables;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        abort_unless($user->hasRole('system-admin'),403);

        return view('national.eregistry.ministries.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('organisation.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $user = Auth::user();

        abort_unless($user->hasRole('system-admin'),403);

        return view('national.eregistry.ministries.create');
    }

    public function store(MinistryStoreRequest $request )
    {
        // $validated = $request->validated();

        // $validated = $request->validate([
            
        //     'phone' => [
        //         'nullable',
        //         'string',
        //         'max:20',
        //         'regex:/^\+?[0-9\s\-()]+$/',
        //     ],
        //     'email' => 'nullable|email|max:255',
        //     'organisation_type_id' => [
        //         'required',
        //         'exists:organisation_types,id'
        //     ],
            
        // ]);

        // ExternalPartner::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'] ?? null,
        //     'phone' => $validated['phone'] ?? null,
        //     'identity_organisation_id' => $validated['identity_organisation_id'] ?? null,
        //     'organisation_type_id' => $validated['organisation_type_id'] ?? null,
        //     'ministry_id' => Auth::user()->ministry_id,
        //     'created_by' => Auth::id(),
        //     'updated_by' => Auth::id(),
        // ]);

        return redirect()->route('registry.external-partners.index')->with('success', 'External Partner created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ministry $ministry)
    {

        $validated = $request->validate([
            'reviewer_title' => ['required', 'string', 'max:100'],
            'address'     => ['required', 'string', 'max:255'],
            'po_box'      => ['nullable', 'string', 'max:100'],
            'phone'       => ['required', 'string', 'max:50'],
            'email'       => ['required', 'email', 'max:255'],
            'website'     => ['nullable', 'string', 'max:255'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($ministry->logo && Storage::disk('public')->exists($ministry->logo)) {
                Storage::disk('public')->delete($ministry->logo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('ministries/logos', 'public');

        } else {
            // keep old logo
            $validated['logo'] = $ministry->logo_path;
        }

        $ministry->update([
            'reviewer_title' => $validated['reviewer_title'],
            'description' => $validated['description'] ?? null,
            'address'     => $validated['address'],
            'po_box'      => $validated['po_box'],
            'phone'       => $validated['phone'],
            'email'       => $validated['email'],
            'website'     => $validated['website'],
            'logo_path'   => $validated['logo'],
            'updated_at'  => now(),
            'updated_by'  => Auth::user()->id
        ]);

        return redirect()
            ->route('registry.ministries.edit', $ministry)
            ->with('success', 'Ministry details updated');
    }

    /*
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ministry $ministry)
    {
        
        $user = Auth::user();

        abort_unless($user->hasRole('system-admin'),403);

        return view('national.eregistry.ministries.show', compact('ministry'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Ministry $ministry)
    {
        return view('national.eregistry.ministries.edit', compact('ministry'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('organisation.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $minsitry = $this->ministries->getById($id);
        $this->ministries->delete($ministry);

        return redirect()->route('organisation.index')->with('message', 'Organisation deleted successfully.');
    }
}
