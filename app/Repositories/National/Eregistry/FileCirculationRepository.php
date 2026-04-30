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

    public function getForDataTable($search = '', $userOrgId = null, $order_by = 'file_circulations.id', $sort = 'desc')
    {
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'file_circulations.status as circulation_status',
                    'files.initial_type as initial_status',
                    'files.subject as file_subject',  
                    'files.id as file_id',           
                    'files.letter_date as file_date' ,
                    'files.reference_no as reference_no',
                    'file_recipients.status as file_recipient_status',
                    'file_circulations.to_review_file as file_reviewer',
                    DB::raw("CONCAT(reviewers.first_name, ' ', reviewers.last_name) as reviewer_name"),
                    DB::raw('GROUP_CONCAT(officers.first_name, " ", officers.last_name SEPARATOR ", ") as officers')
                ])
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->leftJoin('file_recipients', function ($join) use ($userOrgId)  {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', $userOrgId);
                })
                ->leftJoin('organisations', 'file_circulations.from_organisation_id', '=', 'organisations.id')
                // ->join('file_circulations', 'file_circulations.from_organisation_id', '=', 'files.organisation_id')
                ->join('users as reviewers', 'file_circulations.to_review_file', '=', 'reviewers.id') // Join to get reviewer details
                ->leftJoin('divisions', 'files.division_id', '=', 'divisions.id')
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->leftJoin('dispatches', 'dispatches.file_id', '=', 'files.id')
                //double join to exclude archived files for the user's organisation
                ->leftJoin('organisation_archived_files as archives', function ($join) use ($userOrgId) {
                    $join->on('archives.file_id', '=', 'file_circulations.file_id')
                        ->where('archives.organisation_id', $userOrgId);
                })
                ->whereNull('archives.file_id')
                ->leftJoin('file_assignments', function ($join) {
                    $join->on('file_circulations.id', '=', 'file_assignments.file_circulation_id');
                 })
                ->leftJoin('users as officers', 'file_assignments.officer_id', '=', 'officers.id')
                ->where('file_circulations.to_organisation_id', $userOrgId)
                ->groupBy('file_circulations.id', 'file_recipients.status', 'dispatches.dispatch_date', 'reviewers.first_name', 'reviewers.last_name');
        

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

    public function getForReviewDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'desc')
    {

            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.subject as file_subject',                
                    'files.letter_date as file_date',
                    'files.reference_no as reference_no',
                    'organisations.code as organisation_code',  
                    'file_recipients.status as file_recipient_status',
                    
                ])
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->join('file_recipients', function ($join) {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', Auth::user()->organisation_id)
                        ->where('file_recipients.status', 'Pending Review');
                })
                ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                ->where('file_circulations.to_review_file', Auth::id())
                ->where('file_circulations.to_organisation_id', Auth::user()->organisation_id);
                
            if (!empty($search)) {
                $search = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(files.subject) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
                });
            }

            return $query->orderBy($order_by, $sort);
    }

    public function getForSecretaryDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'desc')
    {
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.subject as file_subject',                
                    'files.letter_date as file_date',
                    'file_circulations.status as circulation_status',
                    'files.initial_type as initial_status',
                    'files.reference_no as reference_no',
                    'organisations.code as organisation_code',  
                    'file_recipients.status as file_recipient_status',
                    DB::raw("CONCAT(reviewers.first_name, ' ', reviewers.last_name) as reviewer_name"),

                ])
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->leftJoin('file_recipients', function ($join) {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', Auth::user()->organisation_id);
                })
                ->join('users as reviewers', 'file_circulations.to_review_file', '=', 'reviewers.id') // Join to get reviewer details
                ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
                ->where('file_circulations.to_organisation_id', Auth::user()->organisation_id);
                
            if (!empty($search)) {
                $search = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(files.subject) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
                });
            }

            return $query->orderBy($order_by, $sort);
    }

    //For Activity History - includes both files reviewed and files assigned for officer
    public function getForActivityDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'asc')
    {
            
            $q1 = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.subject as file_subject',
                    'files.letter_date as file_date',
                    'files.reference_no as reference_no',

                    // DB::raw("'review' as activity_type")
                ])
                ->selectRaw("'review' as activity_type")
                ->join('files', 'file_circulations.file_id', '=', 'files.id')
                ->join('file_recipients', function ($join) {
                    $join->on('files.id', '=', 'file_recipients.file_id')
                        ->where('file_recipients.organisation_id', Auth::user()->organisation_id)
                        ->where('file_recipients.status', 'Assigned');
                })
                ->where('file_circulations.to_review_file', Auth::id());

            $q2 = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.subject as file_subject',
                    'files.letter_date as file_date',
                    'files.reference_no as reference_no',

                    // DB::raw("'assigned' as activity_type")
                ])
                ->selectRaw("'assigned' as activity_type")
                ->join('file_circulation_officer', function ($join) {
                    $join->on('file_circulations.id', '=', 'file_circulation_officer.file_circulation_id')
                        ->where('file_circulation_officer.officer_id', Auth::id())
                        ->where('file_circulation_officer.status', 'completed');
                })
                ->join('files', 'file_circulations.file_id', '=', 'files.id');

            $union = $q1->unionAll($q2);

            // wrap union so datatables can order/search
            return DB::query()
                ->fromSub($union, 'activity_files')
                ->select('id', 'file_subject', 'file_date', 'activity_type');
                // ->orderBy($order_by, $sort);
    }

    
    public function getForAssignedDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'asc')
    {
            $query = $this->model->query()
                ->select([
                    'file_circulations.id as id',
                    'files.subject as file_subject',                
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
                ->join('file_assignments', function ($join) {
                    $join->on('file_circulations.id', '=', 'file_assignments.file_circulation_id')
                        ->where('file_assignments.officer_id', Auth::user()->id);
                        // ->where('file_assignments.status', 'pending');
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


    // public function getForActivityDataTable($search = '', $order_by = 'file_circulations.id', $sort = 'asc')
    // {
    //         $query = $this->model->query()
    //             ->select([
    //                 'file_circulations.id as id',
    //                 'files.name as file_name',                
    //                 'files.letter_date as file_date',
    //                 'organisations.code as file_organisation_code',  
    //                 'file_circulations.circulated_at as activity_date',
    //                 // DB::raw("CASE 
    //                 //     WHEN file_circulations.read_status = 0 THEN 'Circulated' 
    //                 //     WHEN file_circulations.read_status = 1 AND file_circulations.requires_action = 0 THEN 'Read' 
    //                 //     WHEN file_circulations.read_status = 1 AND file_circulations.requires_action = 1 THEN CONCAT('Action Taken: ', file_circulations.action_taken) 
    //                 //     ELSE 'Unknown' 
    //                 // END AS activity_description")
    //             ])
    //             ->join('files', 'file_circulations.file_id', '=', 'files.id')
    //             ->join('organisations', 'files.organisation_id', '=', 'organisations.id')
    //             ->join('file_recipients', function ($join) {
    //                 $join->on('files.id', '=', 'file_recipients.file_id')
    //                     ->where('file_recipients.organisation_id', Auth::user()->organisation_id);
    //             })
    //             ->where('file_circulations.to_organisation_id', Auth::user()->organisation_id)
    //             ->whereColumn('file_circulations.file_id', 'file_recipients.file_id')
    //             ->groupBy('file_circulations.id');
                
    //         if (!empty($search)) {
    //             $search = '%' . strtolower($search) . '%';
    //             $query->where(function ($q) use ($search) {
    //                 $q->whereRaw('LOWER(files.name) LIKE ?', [$search])
    //                 ->orWhereRaw('LOWER(files.letter_ref_no) LIKE ?', [$search])
    //                 ->orWhereRaw('LOWER(organisations.name) LIKE ?', [$search]); // fixed this
    //             });
    //         }

    //         return $query->orderBy($order_by, $sort);
    // }


    /**
     * Get a list of files for dropdowns
     */
    public function pluck($column = 'name', $key = 'id')
    {
        $ministryId = auth()->user()->ministry_id;
    
        return $this->model()::query()
                ->where('to_ministry_id', $ministryId)
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

    
    public function ministryCirculations($fileId, $ministryId)
    {
        return $this->model()::query()
            ->where('file_id', $fileId)
            ->where('to_ministry_id', '!=', $ministryId);
    }

    public function thisCirculation($fileId, $ministryId)
    {
        return $this->model()::query()
            ->where('file_id', $fileId)
            ->where('to_ministry_id', $ministryId)
            ->latest()
            ->first();
    }
    

}



