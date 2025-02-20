<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\File;
use Auth;

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
            'folder_id' => $input['folder_id'],
            'ministry_id' => $input['ministry_id'],
            'file_reference' => $input['file_reference'] ?? 'FILE-' . time() . '-' . Auth::id(),
            'name' => $input['name'],
            'path' => $input['path'],
            'receive_date' => $input['receive_date'],
            'letter_date' => $input['letter_date'],
            'details' => $input['details'] ?? '',
            'from_details_name' => $input['from_details_name'],
            'to_details_person_name' => $input['to_details_person_name'],
            'comments' => $input['comments'] ?? '',
            'security_level' => $input['security_level'] ?? 'public',
            'status' => $input['status'] ?? 'draft',
            'circulation_status' => $input['circulation_status'] ?? false,
            'is_active' => $input['is_active'] ?? true,
            'file_type_id' => $input['file_type_id'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'division_id' => $input['division_id'] ?? null,
        ];

        $file = new File($data);
        $file->save();

        // Update file_index after ID is available
        $file->update(['file_index' => "{$file->ministry_id}/{$file->folder_id}/{$file->id}"]);

        return $file;
    }

    /**
     * Update a file record
     */
    public function update(File $model, array $input)
    {
        $data = array_merge($model->toArray(), [
            'folder_id' => $input['folder_id'] ?? $model->folder_id,
            'ministry_id' => $input['ministry_id'] ?? $model->ministry_id,
            'name' => $input['name'] ?? $model->name,
            'path' => $input['path'] ?? $model->path,
            'receive_date' => $input['receive_date'] ?? $model->receive_date,
            'letter_date' => $input['letter_date'] ?? $model->letter_date,
            'details' => $input['details'] ?? $model->details,
            'from_details_name' => $input['from_details_name'] ?? $model->from_details_name,
            'to_details_person_name' => $input['to_details_person_name'] ?? $model->to_details_person_name,
            'comments' => $input['comments'] ?? $model->comments,
            'security_level' => $input['security_level'] ?? $model->security_level,
            'circulation_status' => $input['circulation_status'] ?? $model->circulation_status,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'file_type_id' => $input['file_type_id'] ?? $model->file_type_id,
            'updated_by' => Auth::id(),
        ]);

        return $model->update($data);
    }

    /**
     * Get files for data table with search and sorting
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $ministryId = auth()->user()->ministry_id;

        $query = $this->model->query()
            ->select([
                'id', 'folder_id', 'ministry_id',  'name', 'path',
                'receive_date', 'letter_date', 'letter_ref_no', 'security_level', 'is_active',
            ])
            ->with('ministry')
            ->where('ministry_id', $ministryId)
            ->withTrashed();

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('letter_ref_no', 'ILIKE', $search)
                  ->orWhere('details', 'ILIKE', $search)
                  ->orWhereHas('ministry', function ($q) use ($search) {
                      $q->where('name', 'ILIKE', $search);
                  });
            });
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of files for dropdowns
     */
    public function pluck($column = 'name', $key = 'id')
    {
        $ministryId = auth()->user()->ministry_id;
    
        return $this->model()::query()
                ->where('ministry_id', $ministryId)
                ->where('is_active', true)  // Optional: only show active folders
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
