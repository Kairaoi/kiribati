<?php

namespace App\Repositories\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileRepository extends BaseRepository
{
    /**
     * Specify Model class name
     */
    public function model()
    {
        return File::class;
    }

    /**
     * Create a new file record
     */
    public function create(array $input)
    {
        $data = [
            // 'folder_id' => $input['folder_id'],
            'organisation_id' => $input['organisation_id'],
            'file_reference' => $input['file_reference'] ?? 'FILE-' . time() . '-' . Auth::id(),
            'subject' => $input['subject'],
            'main_file_path' => $input['main_file_path'],
            'additional_file1_path' => $input['additional_file1_path'] ?? null,
            'additional_file2_path' => $input['additional_file2_path'] ?? null,
            'additional_file3_path' => $input['additional_file3_path'] ?? null,
            'letter_date' => $input['letter_date'],
            'comments' => $input['comments'] ?? '',
            'status' => isset($input['status']) ? $input['status'] : 'draft',
            'is_active' => $input['is_active'] ?? true,
            'file_type_id' => $input['file_type_id'],
            'category_id' => $input['category_id'] ?? null,
            'letter_ref_no' => $input['letter_ref_no'] ?? '',
            'division_id' => $input['division_id'] ?? null,
            'recipient_organisations' => $input['recipient_organisations'] ?? [],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            
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
            'organisation_id' => $input['organisation_id'] ?? $model->organisation_id,
            'subject' => $input['subject'] ?? $model->subject,
            'path' => $input['path'] ?? $model->path,
            'letter_date' => $input['letter_date'] ?? $model->letter_date,
            'status' => $input['status'] ?? $model->status,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'file_type_id' => $input['file_type_id'] ?? $model->file_type_id,
            'division_id' => $input['division_id'] ?? $model->division_id,
            'category_id' => $input['category_id'] ?? $model->category_id,
            'letter_ref_no' => $input['letter_ref_no'] ?? $model->letter_ref_no,
            'recipient_organisations' => $input['recipient_organisations'] ?? $model->recipient_organisations,
            'updated_by' => Auth::id(),
        ]);

        return $model->update($data);
    }


    public function getForDataTable( ?int $ministryId = null)
    {

        return $this->model->query()
                            ->select([
                                'files.id as id',
                                'files.subject as file_subject',
                                'files.status as file_status',
                                'fc.status as circulation_status',
                                'files.reference_no as reference_no',
                                'files.letter_date as letter_date',
                                'files.ministry_id',
                                'files.due_date as due_date',
                                'dispatches.dispatch_date as dispatch_date',
                            ])
                            ->join('ministries', 'files.ministry_id', '=', 'ministries.id')

                            ->leftJoin('file_circulations as fc', function ($join) use ($ministryId) {
                                $join->on('files.id', '=', 'fc.file_id')
                                    ->where('fc.to_ministry_id', $ministryId);
                            })

                            ->leftJoin('dispatches', function ($join) {
                                $join->on('dispatches.file_id', '=', 'files.id')
                                    ->on('dispatches.id', '=', 'fc.dispatch_id');
                            })

                            ->where(function ($query) use ($ministryId) {
                                $query->where('files.ministry_id', $ministryId)
                                    ->orWhere('fc.to_ministry_id', $ministryId);
                            });
    }

    
    public function getForFilteredTable($selectedType, int $userMinistryId, array $filterOrgIds = [], $fromDate = null, $toDate = null)
    {
        return $this->model->query()
            ->forType($selectedType, $userMinistryId) //scope in Model
            ->forOrganisation($filterOrgIds)  //scope in Model
            ->forDateRange($fromDate, $toDate) //scope in Model
            ->join('organisations as from_org', 'files.organisation_id', '=', 'from_org.id')
            // ->join('file_recipients', 'files.id', '=', 'file_recipients.file_id')
            // ->join('organisations as to_org', 'file_recipients.organisation_id', '=', 'to_org.id')
            ->select([
                'files.id',
                'files.subject as file_subject',
                'files.letter_date as letter_date',
                'from_org.code as organisation_code',
            //     'to_org.name as to_organisation_name'
            ]);
    } 


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



