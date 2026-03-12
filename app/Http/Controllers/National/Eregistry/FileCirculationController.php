<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\FileCirculation;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\OrganisationRepository;
use App\Repositories\National\Eregistry\UserRepository;
use App\Repositories\National\Eregistry\FileRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class FileCirculationController extends Controller
{
    private $fileCirculations;
    private $divisions;
    private $organisations;
    private $users;
    private $files;

    public function __construct(DivisionRepository $divisions, 
                                OrganisationRepository $organisations, 
                                UserRepository $users,
                                FileRepository $files,
                                FileCirculationRepository $fileCirculations)
    {
        $this->divisions = $divisions;
        $this->organisations = $organisations;
        $this->users = $users;
        $this->files = $files;
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
        $userOrgId = Auth::user()->organisation_id;

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }

        $query = $this->fileCirculations->getForDataTable($search, $userOrgId);
        
        // dd('bla bla');
        // Log::info($query->get()); 

        $datatables = DataTables::of($query)->make(true);
                    
        return $datatables;
    }


    public function getReviewDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }

        $userId = Auth::id();

        $query = $this->fileCirculations->getForReviewDataTable($search, $userId);
        
        $datatables = DataTables::of($query)->make(true);
                    
        return $datatables;
    }


    public function getAssignedDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->fileCirculations->getForAssignedDataTable($search);

        $datatables = DataTables::of($query)->make(true);
                        
        return $datatables;            
    }      

    public function getActivityDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->fileCirculations->getForActivityDataTable($search);

        $datatables = DataTables::of($query)->make(true);
                        
        return $datatables;            
    }
 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        // if (!Auth::user()->) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (Auth::user()->hasRole('registry')) {
            return view('national.eregistry.circulations.index');         //this displays the list of received files and their circulation status
        }   

        abort(403, 'Unauthorized action.'); // For non-registry users, we can either show an error or redirect to a different page
    }


    /**
     * Show the form for creating the first part of the file circulation.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(FileCirculation $fileCirculation)  
    {

        $usersWithDivision = $this->users->getUsersDivision(); // Fetch all admin users using the listAdmins method from UserRepository  
        $file = $this->files->getById($fileCirculation->file_id); // Fetch the file using the file ID from the FileCirculation model
        $fileCirculation->load('assignedOfficers');    // Eager load the assigned officers relationship to avoid N+1 query problem
        // dd($fileCirculation->id);

        $fileRecipientOrganisations = $file->recipientMinistries()->pluck('organisations.id')->toArray();
        $fileOrganisation = $file->organisation_id;
        $loggedInOrganisation = $this->organisations->getById(Auth()->user()->organisation_id); // Get the logged-in user's organisation

        // Check if the user's organisation is either the sender or a recipient of the file
        if (!in_array($loggedInOrganisation->id, $fileRecipientOrganisations)) {
            abort(403, 'Unauthorized access to this file circulation');
        }

        if (!$fileCirculation) {
            return redirect()->back()->withErrors(['error' => 'File circulation not found.']);
        }
   
        // dd($file);
        return view('national.eregistry.circulations.show', compact('usersWithDivision', 
                                                                    'loggedInOrganisation', 
                                                                    'file',
                                                                    'fileCirculation'));
    }

    

    public function reviewIndex()  // This method is used to display the review page for file circulations
    {
        // if (!Auth::user()->can('division.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('national.eregistry.circulations.reviewIndex');
    }


    public function assignedIndex()  // This method is used to display the review page for file circulations
    {
        // if (!Auth::user()->can('division.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('national.eregistry.circulations.assignedIndex');
    }

    public function activityIndex()  // This method is used to display the activity page for file circulations
    {
        // if (!Auth::user()->can('division.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('national.eregistry.circulations.activityIndex');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $organisationId = Auth::user()->organisation_id;
        $reviewOfficerId = Auth::user()->organisation->review_officer_id;

        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);


        $fileCirculation = FileCirculation::where('file_id', $validated['file_id'])
                ->where('to_organisation_id', $organisationId)
                ->first();    

        if ($fileCirculation) {
                $fileCirculation->update([
                    'to_review_file' => $reviewOfficerId,
                    'circulated_by' => auth()->id(),
                    'circulated_at' => now(),
                    'updated_by' => auth()->id(),
                    'status' => 'Pending',
                    'is_active' => true,
                ]);
            } else {
                Log::warning('No existing FileCirculation found for update', $validated);
                return back()->withErrors(['error' => 'No existing file circulation found to update'])->withInput();
            }

            $file = $this->files->getById($validated['file_id']);

            $recipientOrganisationIds = $file->recipientMinistries()->pluck('organisations.id')->toArray();

            //update the status of the logged in recipient organisation to 'Pending Review'
            foreach ($recipientOrganisationIds as $recipientOrganisationId) {
                if ($recipientOrganisationId == $organisationId) {
                    $file->recipientMinistries()->updateExistingPivot($recipientOrganisationId, ['status' => 'Pending Review']);
                }
            }
            return redirect()->route('registry.file-circulations.index');               
    }


    public function storeAssignedOfficers(Request $request, FileCirculation $fileCirculation)
    {
        $organisationId = Auth::user()->organisation_id;
        // if (!Auth::user()->can('file-circulation.assign-officers')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'assignedOfficers' => 'required|array',
            'assignedOfficers.*' => 'exists:users,id',
            'review_comment' => 'nullable|string',
        ]);

        $fileCirculation->update([
            'review_comment' => $validated['review_comment'] ?? null,
            'status' => 'Assigned',
            'updated_by' => auth()->id(),
        ]);

        $fileCirculation->assignedOfficers()->sync($validated['assignedOfficers']);
        // dd($fileCirculation->assignedOfficers);

        $file = $this->files->getById($validated['file_id']);
        $recipientOrganisationIds = $file->recipientMinistries()->pluck('organisations.id')->toArray();

        //update the status of the logged in recipient organisation to 'Assigned to Officer'
        foreach ($recipientOrganisationIds as $recipientOrganisationId) {
                if ($recipientOrganisationId == $organisationId) {
                    $file->recipientMinistries()->updateExistingPivot($recipientOrganisationId, ['status' => 'Assigned']);
                }
            }               
        return redirect()->route('registry.file-circulations.review.index');
    }
    

    public function storeComplete(Request $request, FileCirculation $fileCirculation)
    {
        $loggedInUserId = Auth::user()->id;

        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $file = $this->files->getById($validated['file_id']);
        $assignedOfficersIds = $fileCirculation->assignedOfficers()->pluck('users.id')->toArray();

        //update the status of the logged in recipient organisation to 'Completed'
        foreach ($assignedOfficersIds as $officerId) {
                if ($officerId == $loggedInUserId) {
                    $fileCirculation->assignedOfficers()->updateExistingPivot($officerId, ['status' => 'completed']);
                }
            } 

        return redirect()->route('registry.file-circulations.assigned.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (!Auth::user()->can('division.edit')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);
        $organisations = $this->organisations->pluck();

        return view('national.eregistry.divisions.edit', [
            'division' => $division,
            'organisations' => $organisations,
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
        // if (!Auth::user()->can('division.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $division = $this->divisions->getById($id);
        $this->divisions->update($division, $request->all());

        return redirect()->route('division.index')->with('message', 'Division updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileCirculation $file_circulation)
    {
        // Delete associated file
        $this->files->newQuery()->where('id', $file_circulation->file_id);
        $this->files->delete();
        $this->files->unsetClauses(); // Clear any filters

        // Delete the circulation
        $this->fileCirculations->newQuery()->where('id', $file_circulation->id);
        $this->fileCirculations->delete();
        $this->fileCirculations->unsetClauses(); // Clear filters again

        return response()->json(['message' => 'Circulation and file deleted successfully.']);
    
    }
}
