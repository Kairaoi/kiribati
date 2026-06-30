<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\File;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Nullable;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\Models\National\Eregistry\DocumentOverlay;



class FileCirculationController extends Controller
{
    private $fileCirculations;
    private $divisions;
    private $ministries;
    private $users;
    private $files;

    public function __construct(DivisionRepository $divisions, 
                                MinistryRepository $ministries, 
                                UserRepository $users,
                                FileRepository $files,
                                FileCirculationRepository $fileCirculations)
    {
        $this->divisions = $divisions;
        $this->ministries = $ministries;
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

        $datatables = DataTables::of($query)->make(true);

        return DataTables::of($query)
                ->addColumn('status', function ($row) {
                            if ($row->initial_status === 'internal') {
                                return $row->circulation_status ?? '-'; // file_circulations.status
                            }

                            if ($row->initial_status === 'dispatch') {
                                return $row->file_recipient_status ?? '-';
                            }

                            return '-';
                        })
                        ->make(true);

    }


    public function getReviewDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }  

        $query = $this->fileCirculations->getForReviewDataTable($search);

        $datatables = DataTables::of($query)->make(true);
                    
        return $datatables;
    }




    // public function getAssignedDataTables(Request $request)
    // {
    //     $search = $request->get('search', '');
    //     if (is_array($search)) {
    //         $search = $search['value'];
    //     }
    //     $query = $this->fileCirculations->getForAssignedDataTable($search);

    //     $datatables = DataTables::of($query)->make(true);
                        
    //     return $datatables;            
    // }      

    // public function getActivityDataTables(Request $request)
    // {
    //     $search = $request->get('search', '');
    //     if (is_array($search)) {
    //         $search = $search['value'];
    //     }
    //     $query = $this->fileCirculations->getForActivityDataTable($search);

    //     $datatables = DataTables::of($query)->make(true);
                        
    //     return $datatables;            
    // }
 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        if (Auth::user()->hasRole('registry')) {
            return view('national.eregistry.circulations.index');         
        }   

        abort(403, 'Unauthorized action.'); 
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
        $fileCirculation->load(['activeAssignments.officer','activeAssignments.assignedBy']); // Eager load the assigned officers and the users who assigned them
        $fileRecipientOrganisations = $file->recipientMinistries()->pluck('organisations.id')->toArray();
        $loggedInOrganisation = $this->organisations->getById(Auth()->user()->organisation_id); // Get the logged-in user's organisation

        $fileAssignment = $fileCirculation->activeAssignments()->where('officer_id', Auth::id())->first(); // Get the file assignment for the logged-in user, if it exists

        // Check if the user's organisation is a recipient of the file
        if ($file->initial_type === 'internal') {
            // Only check circulation
            if ($fileCirculation->to_organisation_id != $loggedInOrganisation->id) {
                abort(403, 'Unauthorized access to this file circulation');
            }
        } elseif ($file->initial_type === 'dispatch') {
            // Only check recipients
            if (!in_array($loggedInOrganisation->id, $fileRecipientOrganisations)) {
                abort(403, 'Unauthorized access to this file');
            }
        }

        if (!$fileCirculation) {
            return redirect()->back()->withErrors(['error' => 'File circulation not found.']);
        }

        $status = null;
        if ($file->initial_type == 'internal') {
            $status = $fileCirculation->status;
        } elseif ($file->initial_type == 'dispatch') {
            $recipient = $file->recipients()->where('organisations.id', $loggedInOrganisation->id)->first();
            $status = $recipient ? $recipient->pivot->status : null;
        }
   
        return view('national.eregistry.circulations.show', compact('usersWithDivision', 
                                                                    'loggedInOrganisation', 
                                                                    'status',
                                                                    'file',
                                                                    'fileCirculation',
                                                                    'fileAssignment'));
    }


    // public function reviewIndex()// This method is used to display the review page for file circulations
    // {
    //     // if (!Auth::user()->can('division.create')) {
    //     //     abort(403, 'Unauthorized action.');
    //     // }

    //     return view('national.eregistry.circulations.reviewIndex');
    // }

    // public function allReceivedIndex()  // for secretary, HM
    // {
    //     if (!Auth::user()->hasRole('sro')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     return view('national.eregistry.circulations.allReviewIndex');
    // }


    // public function assignedIndex()  // This method is used to display the review page for file circulations
    // {
    //     // if (!Auth::user()->can('division.create')) {
    //     //     abort(403, 'Unauthorized action.');
    //     // }

    //     return view('national.eregistry.circulations.assignedIndex');
    // }

    // public function activityIndex()  // This method is used to display the activity page for file circulations
    // {
    //     // if (!Auth::user()->can('division.create')) {
    //     //     abort(403, 'Unauthorized action.');
    //     // }

    //     return view('national.eregistry.circulations.activityIndex');
    // }


    /**
     * Store the circulation of the file when registry users circulate file to Review Officer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        // dd($request);
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',

        ]);

        $ministryId = Auth::user()->ministry_id;

        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id'        => $validated['file_id'],
                'to_ministry_id' => $ministryId,
            ],
            [
                'circulated_by'  => auth()->id(),
                'circulated_at'  => now(),
                'updated_by'     => auth()->id(),
                'status'         => 'Pending SRO Approval',
            ]
        );

        $fileCirculation->file()->update([
            'status' => 'Pending SRO Approval',
        ]);

        return redirect()->route('registry.files.index')->with('success', 'File circulated ');
    }



    public function update(Request $request, FileCirculation $fileCirculation) 
    {

        $fileCirculation->update(
            [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
                'status'     => 'Pending SRO Approval',
            ]
        );
        
        return redirect()->route('registry.files.index')->with('success', 'File circulated ');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }


    public function colleagueStore(Request $request) 
    {
        // dd($request);
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'colleague' => 'required|exists:users,id'
        ]);

        $ministryId = Auth::user()->ministry_id;

        $fileCirculation = FileCirculation::updateOrCreate(
            [
                'file_id'        => $validated['file_id'],
                'to_ministry_id' => $ministryId,
            ],
            [
                'circulated_by'  => auth()->id(),
                'circulated_at'  => now(),
                'updated_by'     => auth()->id(),
                'status'         => 'Pending Colleague Review',
                'colleague_id'   => $validated['colleague']
            ]
        );

        $fileCirculation->file()->update([
            'status' => 'Pending Colleague Review',
        ]);

        return redirect()->route('registry.files.index')->with('success', 'File circulated ');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function colleagueUpdate(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'colleague_comment' => ['required', 'string'],
            'action' => ['required', 'in:return,approve'],
            'circulation' => 'required|exists:file_circulations,id'
        ]);


        // Ensure only assigned reviewer can review
        // if ($circulation->review_officer !== auth()->id()) {
        //     abort(403);
        // }

        $circulation = FileCirculation::with('file')
            ->findOrFail($validated['circulation']);

        $status = match ($validated['action']) {
            'return'  => 'Returned for Amendment',
            'approve' => 'Pending SRO Approval',
            default   => $circulation->status,
        };

        $circulation->update([
            'colleague_comment' => $validated['colleague_comment'],
            'reviewed_by'       => auth()->id(),
            'date_reviewed'     => now(),
            'updated_by'        => auth()->id(),
            'status'            => $status,
        ]);

        $circulation->file->update([
            'status' => $status,
        ]);

        return redirect()
            ->route('registry.files.show', $circulation->file_id)
            ->with('success', 'File reviewed');
    }

    public function receive(FileCirculation $fileCirculation)
    {  

        if ($fileCirculation->status === 'Pending Receipt') {
            $fileCirculation->update([
                'status' => 'Received',
                'received_at' => now(),
                'received_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('registry.files.index')->with('message', 'File marked as Received successfully.');
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
