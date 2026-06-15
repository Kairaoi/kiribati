<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use App\Repositories\National\Eregistry\DispatchRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;

use function Symfony\Component\Clock\now;

class DispatchController extends Controller
{
    private $dispatches;
    private $files;
    private $ministries;
    private $divisions;
    private $users;
    private $fileCirculations;

    public function __construct(
        DispatchRepository $dispatches,
        FileRepository $files,
        MinistryRepository $ministries,
        DivisionRepository $divisions,
        UserRepository $users,
        FileCirculationRepository $fileCirculations
    )
    {
        $this->dispatches = $dispatches;
        $this->files = $files;
        $this->ministries = $ministries;
        $this->divisions = $divisions;
        $this->users = $users;
        $this->fileCirculations = $fileCirculations;
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
        // dd($request->all());
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'recipient_ministries' => 'required|array',
            'recipient_ministries.*' => 'exists:ministries,id',
        ]);    

        // dd($validated);
        //create dispatch record and circulation record for each recipient ministry

        try {
            
            $dispatch = Dispatch::create([
                'file_id' => $validated['file_id'],
                'dispatched_by' => auth()->user()->id,
                'dispatch_date' => now(),
                'updated_by' => auth()->user()->id,
            ]);

            $file = File::findOrFail($validated['file_id']);
            $file->status = 'Dispatched';
            $file->save();

            $fileOwnerCirculation = $this->fileCirculations->thisCirculation($validated['file_id'], auth()->user()->ministry_id);

            if ($fileOwnerCirculation) {
                $fileOwnerCirculation->update([
                    'status' => 'Dispatched',
                    'updated_by' => auth()->id(),
                ]);
            }

            foreach ($validated['recipient_ministries'] as $ministryId) {
                FileCirculation::create([                                                       
                    'file_id' => $validated['file_id'],
                    'dispatch_id' => $dispatch->id,
                    'to_ministry_id' => $ministryId,
                    'circulated_by' => auth()->user()->id,
                    'circulated_at' => now(),
                    'status' => 'Pending',
                    'updated_by' => auth()->user()->id          
                ]);
            }

            //for activity log
            // activity()
            //     ->causedBy(auth()->user())
            //     ->performedOn($dispatch)
            //     ->log('File is dispatched');

            if(auth()->user()->hasRole('registry')) {
                return redirect()->route('registry.files.index')->with('success', 'File dispatched successfully!');
            }

        } catch (\Exception $e) {
            Log::error('Exception while creating file circulation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return back()->withErrors(['error' => 'Error creating file circulation: ' . $e->getMessage()])->withInput();
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
        $fileId = $file->id;
        $fileOrganisations = $file->recipientMinistries()->pluck('organisations.id')->toArray();
        $fileOrganisation = $file->organisation_id;
        $userOrgId = Auth()->user()->organisation_id;

        // Check if the user's organisation is either the sender or a recipient of the file
        if (!in_array($userOrgId, $fileOrganisations) && $userOrgId != $fileOrganisation) {
            abort(403, 'Unauthorized access to this file dispatch');
        }

        $organisations = $this->organisations->pluck(); 
        $fileCirculations = $file->circulations()->with('fromOrganisation', 'activeAssignments.officer')->get();

        return view('national.eregistry.dispatches.show', compact('file', 'organisations', 'dispatch', 'fileCirculations'));
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
