<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileCirculationRepository extends BaseRepository
{
    /**
     * Specify Model class name
     */
    public function model()
    {
        return FileCirculation::class;
    }

    /**
     * Create a new file record
     */
    public function create(array $input)
    {
        $data = [
            'file_id' => $input['file_id'],
            'organisation_id' => $input['organisation_id'],
            'circulated_by' => Auth::id(),
            'circulated_at' => now(),
            'to_review_file' => $input['to_review_file'] ?? null,
            'assigned_officer' => $input['assigned_officer'] ?? null,
            'read_at' => null,
            'read_status' => false,
            'requires_action' => $input['requires_action'] ?? false,
            'action_taken' => $input['action_taken'] ?? null,
            'updated_by' => Auth::id(),
            'assigned_division_id' => $input['assigned_division_id'] ?? null,
        ];

        $file = new File($data);
        $file->save();

        // Update file_index after ID is available
        // $file->update(['file_index' => "{$file->organisation_id}/{$file->id}"]);

        return $file;
    }

    /**
     * Update a file record
     */
    public function update(File $model, array $input)
    {
        $data = array_merge($model->toArray(), [
            'file_id' => $input['file_id'],
            'organisation_id' => $input['organisation_id'],
            'circulated_by' => Auth::id(),
            'circulated_at' => now(),
            'to_review_file' => $input['to_review_file'] ?? null,
            'assigned_officer' => $input['assigned_officer'] ?? null,
            'read_at' => null,
            'read_status' => false,
            'requires_action' => $input['requires_action'] ?? false,
            'action_taken' => $input['action_taken'] ?? null,
            'updated_by' => Auth::id(),
            'assigned_division_id' => $input['assigned_division_id'] ?? null,

        ]);

        return $model->update($data);
    }

    public function getForDataTable($search = '', $userOrgId = null, $order_by = 'file_circulations.id', $sort = 'asc')
    {
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.name as file_name',  
                    'files.id as file_id',           
                    'files.letter_date',
                    'files.letter_ref_no',
                    'organisations.code as file_organisation_code',  
                    'file_types.name as file_type_name', 
                    'file_recipients.status as file_recipient_status',
                    'dispatches.dispatch_date as dispatch_date',
                    'file_circulations.to_review_file as file_reviewer',
                    DB::raw("CONCAT(reviewers.first_name, ' ', reviewers.last_name) as reviewer_name"),
                    'files.initial_type as file_initial_type',
                    
                ])
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->join('file_recipients', function ($join) use ($userOrgId)  {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', $userOrgId);
                })
                ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                ->join('users as reviewers', 'file_circulations.to_review_file', '=', 'reviewers.id') // Join to get reviewer details
                ->leftJoin('divisions', 'files.division_id', '=', 'divisions.id')
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->leftJoin('dispatches', 'dispatches.file_id', '=', 'files.id')
                ->leftJoin('organisation_archived_files as archives', function ($join) use ($userOrgId) {
                    $join->on('archives.file_id', '=', 'file_circulations.file_id')
                        ->where('archives.organisation_id', $userOrgId);
                })
                ->whereNull('archives.file_id')
                ->where('file_circulations.to_organisation_id', $userOrgId);
                

            if (!empty($search)) {
                $search = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(files.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
                });
            }

            return $query->orderBy($order_by, $sort);

    }

    //For Review index - files to be reviewed and assigned to officers
    public function getForReviewDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'asc')
    {

            $userOrgId = Auth::user()->organisation_id;
            
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.name as file_name',                
                    'files.letter_date as file_date',
                    'organisations.code as file_organisation_code',   
                    'file_recipients.status as file_recipient_status',
                ])
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->join('file_recipients', function ($join) {
                    $join->on('file_circulations.to_organisation_id', '=', 'file_recipients.organisation_id')
                        ->where('file_recipients.organisation_id', Auth::user()->organisation_id)
                        ->where('file_recipients.status', 'Pending Review');
                })
                ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                ->leftJoin('organisation_archived_files as archives', function ($join) use ($userOrgId) {
                    $join->on('archives.file_id', '=', 'files.id')
                        ->where('archives.organisation_id', $userOrgId);
                })
            ->whereNull('archives.file_id')
                ->whereColumn('file_circulations.to_organisation_id', 'file_recipients.organisation_id')
                ->whereColumn('file_circulations.file_id', 'file_recipients.file_id')
                ->where('file_circulations.to_review_file', Auth::user()->id);
                
            if (!empty($search)) {
                $search = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(files.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
                });
            }

            return $query->orderBy($order_by, $sort);
    }

    
  public function getForAssignedDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'asc')
    {
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.name as file_name',                
                    'files.letter_date as file_date',
                    'organisations.code as file_organisation_code',  
                ])
                ->groupBy('file_circulations.id')
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->join('file_recipients', function ($join) {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', Auth::user()->organisation_id)
                        ->where('file_recipients.status', 'Assigned');
                })
                ->join('file_circulation_officer', function ($join) {
                    $join->on('file_circulations.id', '=', 'file_circulation_officer.file_circulation_id')
                        ->where('file_circulation_officer.officer_id', Auth::user()->id)
                        ->where('file_circulation_officer.status', 'pending');
                })
                ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                ->where('file_circulations.to_organisation_id', Auth::user()->organisation_id);
                
            if (!empty($search)) {
                $search = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(files.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
                });
            }

            return $query->orderBy($order_by, $sort);
    }


    /**
     * Get a list of files for dropdowns
     */
    public function pluck($column = 'name', $key = 'id')
    {
        $organisationId = auth()->user()->organisation_id;
    
        return $this->model()::query()
                ->where('organisation_id', $organisationId)
                ->where('is_active', true) 
                ->orderBy($column)
                ->pluck($column, $key);
    }


    public function list($column = 'name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }    

 
        

}



