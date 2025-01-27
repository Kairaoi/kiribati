<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\File;
use Auth;

class InwardFileRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return File::class;
    }

    /**
     * Create a new file record
     *
     * @param array $input
     * @return File
     */
    public function create(array $input)
    {
        $data = [
            'folder_id' => $input['folder_id'],
            'ministry_id' => $input['ministry_id'],
            'division_id' => $input['division_id'],
            'name' => $input['name'],
            'path' => $input['path'],
            'receive_date' => $input['receive_date'],
            'letter_date' => $input['letter_date'],
            'letter_ref_no' => $input['letter_ref_no'],
            'details' => $input['details'] ?? '',
            'from_details_name' => $input['from_details_name'],
            'to_details_person_name' => $input['to_details_person_name'],
            'comments' => $input['comments'] ?? '',
            'security_level' => $input['security_level'] ?? 'public',
            'circulation_status' => $input['circulation_status'] ?? false,
            'is_active' => $input['is_active'] ?? true,
            'file_type_id' => $input['file_type_id'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $file = $this->model();
        $file = new $file($data);
        $file->save();

        return $file;
    }

    /**
     * Update an existing file record
     *
     * @param File $model
     * @param array $input
     * @return bool
     */
    public function update(File $model, array $input)
    {
        $data = [
            'folder_id' => $input['folder_id'] ?? $model->folder_id,
            'ministry_id' => $input['ministry_id'] ?? $model->ministry_id,
            'division_id' => $input['division_id'] ?? $model->division_id,
            'name' => $input['name'] ?? $model->name,
            'path' => $input['path'] ?? $model->path,
            'receive_date' => $input['receive_date'] ?? $model->receive_date,
            'letter_date' => $input['letter_date'] ?? $model->letter_date,
            'letter_ref_no' => $input['letter_ref_no'] ?? $model->letter_ref_no,
            'details' => $input['details'] ?? $model->details,
            'from_details_name' => $input['from_details_name'] ?? $model->from_details_name,
            'to_details_person_name' => $input['to_details_person_name'] ?? $model->to_details_person_name,
            'comments' => $input['comments'] ?? $model->comments,
            'security_level' => $input['security_level'] ?? $model->security_level,
            'circulation_status' => $input['circulation_status'] ?? $model->circulation_status,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'file_type_id' => $input['file_type_id'] ?? $model->file_type_id,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get files for data table with search and sorting
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
{
    // Get the authenticated user's ministry_id
    $ministryId = auth()->user()->ministry_id;

    $query = $this->model->query()
        ->select([
            'id',
            'folder_id',
            'ministry_id',
            'division_id',
            'name',
            'path',
            'receive_date',
            'letter_date',
            'letter_ref_no',
            'security_level',
            'is_active',
        ])
        ->with('ministry') // Eager load the ministry relationship
        ->where('ministry_id', $ministryId); // Filter by the user's ministry_id

    // Add search filter if provided
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', $search)
              ->orWhere('letter_ref_no', 'ILIKE', $search)
              ->orWhere('details', 'ILIKE', $search)
              ->orWhereHas('ministry', function ($q) use ($search) {
                  // You can add search conditions on the ministry model as well
                  $q->where('name', 'ILIKE', $search);  // Example: searching ministry name
              });
        });
    }

    // Ordering
    return $query->orderBy($order_by, $sort);
}

    /**
     * Get a list of files for dropdowns
     *
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }
}
