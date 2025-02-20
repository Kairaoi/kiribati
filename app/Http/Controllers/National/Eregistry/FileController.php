<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\FolderRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\FileTypeRepository;
use App\Repositories\National\Eregistry\DivisionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private $files;
    private $folders;
    private $ministries;
    private $file_types;
    private $divisions;

    public function __construct(
        FileRepository $files,
        FolderRepository $folders,
        MinistryRepository $ministries,
        FileTypeRepository $file_types,
        DivisionRepository $divisions
    ) {
        $this->files = $files;
        $this->folders = $folders;
        $this->ministries = $ministries;
        $this->file_types = $file_types;
        $this->divisions = $divisions;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = $search['value'];
        }
        $query = $this->files->getForDataTable($search);
        return DataTables::make($query)->make(true);
    }

    

    public function index()
    {
        return view('national.eregistry.files.index');
    }

    public function create()
    {
        $folders = $this->folders->pluck();
        // dd($folders);
        $ministries = $this->ministries->pluck();
        $file_types = $this->file_types->pluck();
        // $divisions = $this->divisions->pluck();

        return view('national.eregistry.files.create', [
            'folders' => $folders,
            'ministries' => $ministries,
            'file_types' => $file_types,
            // 'divisions' => $divisions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'ministry_id' => 'required|exists:ministries,id',
            // 'division_id' => 'nullable|exists:divisions,id',
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx',
            'receive_date' => 'required|date',
            'letter_date' => 'required|date',
            'letter_ref_no' => 'nullable|string|unique:files,letter_ref_no',
            'details' => 'required|string',
            'from_details_name' => 'required|string',
            'to_details_person_name' => 'required|string',
            'security_level' => 'required|in:public,internal,confidential,strictly_confidential',
            'file_type_id' => 'required|exists:file_types,id',
        ]);

        try {
            Log::info('Attempting to store file', ['file' => $request->file('file')->getClientOriginalName()]);
            $filePath = $request->file('file')->store('files', 'public');
            Log::info('File stored successfully', ['file_path' => $filePath]);

            $fileData = array_merge($validated, [
                'path' => $filePath,
                'file_reference' => null,
                'file_index' => null,
                'status' => 'draft',
                'circulation_status' => false,
                'requires_response' => false,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $file = $this->files->create($fileData);
            Log::info('File successfully stored in database', ['file_id' => $file->id]);

            return redirect()->route('registry.files.index')->with('success', 'File stored successfully');
        } catch (\Exception $e) {
            Log::error('Error storing file', ['message' => $e->getMessage(), 'file_data' => $validated]);
            return back()->withErrors(['error' => 'Error storing file: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $file = $this->files->getById($id);
        return view('national.eregistry.files.show', compact('file'));
    }

    public function viewFile($id)
    {
        $file = $this->files->getById($id);
        $filePath = public_path('storage/' . $file->path);
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        return response()->file($filePath);
    }

    public function edit($id)
    {
        $file = $this->files->getById($id);
        return view('national.eregistry.files.edit', compact('file'));
    }

    public function update(Request $request, $id)
    {
        $file = $this->files->getById($id);
        $validated = $request->validate([
            'file_reference' => 'required|string|unique:files,file_reference,' . $id,
            'letter_ref_no' => 'nullable|string|unique:files,letter_ref_no,' . $id,
            'status' => 'required|in:draft,pending_registry_review,pending_secretary_review,pending_staff_action,in_circulation,completed,archived',
        ]);
        $this->files->update($file, array_merge($validated, ['updated_by' => auth()->id()]));
        return redirect()->route('file.index')->with('message', 'File updated successfully.');
    }

    public function destroy($id)
    {
        $file = $this->files->getById($id);
        $this->files->delete($file);
        return redirect()->route('file.index')->with('message', 'File deleted successfully.');
    }

    public function download($id)
    {
        $file = $this->files->getById($id);
        return response()->download(storage_path('app/' . $file->path));
    }
}
