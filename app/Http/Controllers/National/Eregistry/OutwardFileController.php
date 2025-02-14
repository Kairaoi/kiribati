<?php

namespace App\Http\Controllers\National\Eregistry;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\National\Eregistry\Ministry;
use App\Models\National\Eregistry\OutwardFile;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\OutwardFileRepository;

class OutwardFileController extends Controller
{
    private $outward_files;
    // private $folders;
    private $ministries;
    private $divisions;
    private $file_types;

    public function __construct(
        OutwardFileRepository $outward_files,
        // FolderRepository $folders,
        MinistryRepository $ministries,
        DivisionRepository $divisions,
        FileTypeRepository $file_types
    )
    {
        $this->outward_files = $outward_files;
        // $this->folders = $folders;
        $this->ministries = $ministries;
        $this->divisions = $divisions;
        $this->file_types = $file_types;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDataTables(Request $request)
    {

        // Get the logged-in user's ministry_id
        $ministryId = Auth::user()->ministry_id;

        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }

        // Filter outward files by ministry_id
        $query = $this->outward_files->getForDataTable($search)
                                       ->where('ministry_id', $ministryId) // Filter by the ministry_id
                                       ->select('out_ward_files.*', 'out_ward_files.recipient_display')
                                       ->with(['recipientMinistries' => function($query) {
                                            $query->select('ministries.id', 'ministries.name'); // Include 'id' for correct mapping
                                       }]);

        // Execute the query and get the results
        $results = $query->get();  // Execute the query

        // Check if results are empty
        if ($results->isEmpty()) {
            Log::debug('No results found.');
        } else {
            Log::debug('Query Results: ', $results->toArray());
        }

        $datatables = DataTables::of($results)->make(true);
        // dd($datatables);

        return $datatables;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('national.eregistry.outward_files.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!Auth::user()->can('outward_file.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        // $folders = $this->folders->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $fileTypes = $this->file_types->pluck();

        return view('national.eregistry.outward_files.create', [
            // 'folders' => $folders,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'fileTypes' => $fileTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (!Auth::user()->can('outward_file.store')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $ministriesSentTo = $request->input('recipient_ministries', []);

        // dd($ministriesSentTo);

        // $allSelected = false;

        if (in_array('all', $ministriesSentTo)) {  // Check if "all" is selected, and if so, replace it with all ministry IDs
            $ministriesSentTo = Ministry::all()->pluck('id')->toArray(); // Fetch all ministry IDs and replace the array with them
            $allSelected = true;
        } else {
            $ministriesSentTo = array_map('intval', $ministriesSentTo); // Convert all other values to integers
            $allSelected = false;
        }

        // dd($allSelected);

        $validated = $request->validate([
            // 'folder_id' => 'nullable|exists:folders,id',
            'ministry_id' => 'required|exists:ministries,id',
            'division_id' => 'nullable|exists:divisions,id',
            'name' => 'required|string',
            'path' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'send_date' => 'required|date',
            'letter_date' => 'required|date',
            'letter_ref_no' => 'required|string',
            'details' => 'nullable|string',
            'from_details_name' => 'required|string',
            'to_details_name' => 'required|string',
            'security_level' => 'required|in:public,internal,confidential,strictly_confidential',
            'file_type_id' => 'required|exists:file_types,id',
            // Removed validation for ministries_sent_to since it's already processed
        ]);

        // Process file upload
        $filePath = $request->file('path');
        $path = $filePath->store('uploads', 'public');

        $outwardFile = new OutwardFile(); // Create a new outward file
        // $outwardFile->folder_id = $validated['folder_id'];
        $outwardFile->ministry_id = $validated['ministry_id'];  // The ministry that owns the file
        $outwardFile->division_id = $validated['division_id'];
        $outwardFile->name = $validated['name'];
        $outwardFile->path = $filePath;
        $outwardFile->send_date = $validated['send_date'];
        $outwardFile->letter_date = $validated['letter_date'];
        $outwardFile->letter_ref_no = $validated['letter_ref_no'];
        $outwardFile->details = $validated['details'];
        $outwardFile->from_details_name = $validated['from_details_name'];
        $outwardFile->to_details_name = $validated['to_details_name'];
        $outwardFile->security_level = $validated['security_level'];
        $outwardFile->file_type_id = $validated['file_type_id'];
        $outwardFile->created_by = Auth::id();  // Set created_by to the currently authenticated user's ID
        $outwardFile->updated_by = Auth::id();  // Optionally, you can set updated_by as well
        $outwardFile->save();

        // dd($ministriesSentTo);

        // // Attach selected ministries to the outward file
        // $outwardFile->ministriesSentTo()->attach($ministriesSentTo); // This should populate the pivot table

        $outwardFile->owningMinistry()->attach($request->ministry_id, ['role' => 'owner']);

        // Attach recipient ministries
        foreach ($ministriesSentTo as $recipient) {
            $outwardFile->recipientMinistries()->attach($recipient, ['role' => 'recipient']);
        }

        // Store "All" in a column (if applicable)
        $outwardFile->update(['recipient_display' => $allSelected ? 'all' : null]);

        // dd($allSelected);

        $outwardFile->refresh();
        // dd($outwardFile);

        return redirect()->route('registry.outward-files.index')->with('message', 'Outward file created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('outward_file.show')) {
            abort(403, 'Unauthorized action.');
        }

        $outward_file = $this->outward_files->getById($id);

        return view('national.eregistry.outward_files.show')->with('outward_file', $outward_file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('outward_file.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $outward_file = $this->outward_files->getById($id);
        $folders = $this->folders->pluck();
        $ministries = $this->ministries->pluck();
        $divisions = $this->divisions->pluck();
        $file_types = $this->file_types->pluck();

        return view('national.eregistry.outward_files.edit', [
            'outward_file' => $outward_file,
            'folders' => $folders,
            'ministries' => $ministries,
            'divisions' => $divisions,
            'file_types' => $file_types,
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
        if (!Auth::user()->can('outward_file.update')) {
            abort(403, 'Unauthorized action.');
        }

        $outward_file = $this->outward_files->getById($id);
        $this->outward_files->update($outward_file, $request->all());

        return redirect()->route('outward_file.index')->with('message', 'Outward file updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('outward_file.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $outward_file = $this->outward_files->getById($id);
        $this->outward_files->delete($outward_file);

        return redirect()->route('outward_file.index')->with('message', 'Outward file deleted successfully.');
    }
}
