<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Organisation;
use App\Repositories\National\Eregistry\DispatchRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\UserRepository;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class DispatchController extends Controller
{
    private $dispatches;
    private $files;
    private $organisations;
    private $divisions;
    private $users;

    public function __construct(
        DispatchRepository $dispatches,
        FileRepository $files,
        OrganisationRepository $organisations,
        DivisionRepository $divisions,
        UserRepository $users
    )
    {
        $this->dispatches = $dispatches;
        $this->files = $files;
        $this->organisations = $organisations;
        $this->divisions = $divisions;
        $this->users = $users;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {
        $userOrgId = Auth()->user()->organisation_id;

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->dispatches->getForDataTable($search, $userOrgId);

        return DataTables::of($query)->make(true);
    }


    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserDataTables(Request $request)
    {
        
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->dispatches->getForUserDataTable($search);

        return DataTables::of($query)->make(true);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (!auth()->user()->hasRole(['registry','admin'])) {
        //     abort(403, 'Unauthorized access');
        // }
        return view('national.eregistry.dispatches.index');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        // dd('User Index');
        return view('national.eregistry.dispatches.user-index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $organisationId = $user->organisation_id; // Get the organisation ID of the authenticated user

        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'from_organisation_id' => 'required|exists:organisations,id',
            'from_division_id' => 'required|exists:divisions,id',
            'dispatch_date' => 'required|date',
            ]);

        try {
            Log::info('Attempting to update file dispatch', ['data' => $validated]);

            $dispatch = Dispatch::where('file_id', $validated['file_id'])->first();

            if ($dispatch) {
                $dispatch->update([
                    'from_organisation_id' => $validated['from_organisation_id'],
                    'from_division_id' => $validated['from_division_id'],
                    'dispatched_by' => auth()->user()->id,
                    'dispatch_date' => now(),
                    'updated_by' => auth()->user()->id,
                ]);
            } else {
                Log::warning('No existing File Dispatch found for update', $validated);
                return back()->withErrors(['error' => 'No existing file dispatch found to update'])->withInput();
            }

            $file = $this->files->getById($validated['file_id']);

            $file->status = 'Dispatched';
            $file->updated_by = auth()->user()->id;
            $file->save();

            // Get the recipient organisation IDs from the file
            $recipientOrganisationIds = $file->recipientMinistries()->pluck('organisations.id')->toArray();
            // dd($recipientOrganisationIds);
            // dd($file);
            foreach ($recipientOrganisationIds as $organisationId) {

                $organisation = Organisation::with('reviewOfficer')->find($organisationId); //get the organisation along with its review officer

                //Once a file is dispatched, the status of the recipient organisations is updated to 'Pending Circulation'
                $file->recipientMinistries()->updateExistingPivot($organisationId, ['status' => 'Pending Review']);  
                // Check pivot table row
            
                //and a new FileCirculation record is created for each recipient organisation
                FileCirculation::create([                                                       
                    'file_id' => $validated['file_id'],
                    'from_organisation_id' => $validated['from_organisation_id'], 
                    'to_organisation_id' => $organisationId,
                    'to_review_file' => $organisation->reviewOfficer ? $organisation->reviewOfficer->id : null,         
                ]);
            }

            if(auth()->user()->hasRole('user') || (auth()->user()->hasRole('admin')) ){
                return redirect()->route('registry.dispatches.user.index')->with('success', 'File dispatched successfully!');
            }

            if(auth()->user()->hasRole('registry')) {
                return redirect()->route('registry.dispatches.index')->with('success', 'File dispatched successfully!');
            }

        } catch (\Exception $e) {
            Log::error('Exception while updating file circulation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return back()->withErrors(['error' => 'Error updating file: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Dispatch $dispatch)
    {
        
        $file = $this->files->getById($dispatch->file_id);
        $fileOrganisations = $file->recipientMinistries()->pluck('organisations.id')->toArray();
        $fileOrganisation = $file->organisation_id;
        $userOrgId = Auth()->user()->organisation_id;

        // Check if the user's organisation is either the sender or a recipient of the file
        if (!in_array($userOrgId, $fileOrganisations) && $userOrgId != $fileOrganisation) {
            abort(403, 'Unauthorized access to this file dispatch');
        }

        $organisations = $this->organisations->pluck(); 
        
        return view('national.eregistry.dispatches.show', compact('file', 'organisations', 'dispatch'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (!Auth::user()->can('dispatch.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $dispatches = $this->dispatches->getById($id);
        $files = $this->files->pluck();
        $organisations = $this->organisations->pluck();
        $divisions = $this->divisions->pluck();
        $users = $this->users->pluck();

        return view('national.eregistry.dispatches.edit', [
            'dispatches' => $dispatches,
            'files' => $files,
            'organisations' => $organisations,
            'divisions' => $divisions,
            'users' => $users,
        ]);
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
        // if (!Auth::user()->can('dispatch.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $dispatch = $this->dispatches->getById($id);
        $this->dispatches->update($dispatch, $request->all());

        return redirect()->route('registry.files.index')->with('message', 'Dispatch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dispatch $dispatch)
    {
        // Delete associated file
        $this->files->newQuery()->where('id', $dispatch->file_id);
        $this->files->delete();
        $this->files->unsetClauses(); // Clear any filters

        // Delete the dispatch
        $this->dispatches->newQuery()->where('id', $dispatch->id);
        $this->dispatches->delete();
        $this->dispatches->unsetClauses(); // Clear filters again

        return response()->json(['message' => 'Dispatch and file deleted successfully.']);
    
    }



}
