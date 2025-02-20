<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\Folder;
use Auth;

class FolderRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return Folder::class;
    }

    /**
     * Create a new folder record
     * 
     * @param array $input
     * @return Folder
     */
    public function create(array $input)
    {
        $data = [
            'index_no' => $input['index_no'],
            'folder_name' => $input['folder_name'],
            'folder_description' => $input['folder_description'] ?? '',
            'is_public' => $input['is_public'] ?? false,
            'is_active' => $input['is_active'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $folder = $this->model();
        $folder = new $folder($data);
        $folder->save();

        return $folder;
    }

    /**
     * Update an existing folder record
     * 
     * @param Folder $model
     * @param array $input
     * @return bool
     */
    public function update(Folder $model, array $input)
    {
        $data = [
            'index_no' => $input['index_no'],
            'folder_name' => $input['folder_name'],
            'folder_description' => $input['folder_description'] ?? $model->folder_description,
            'is_public' => $input['is_public'] ?? $model->is_public,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get folders for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
{
    $query = $this->model->query()->select([
        'id', 
        'ministry_id',
        'folder_number',
        'folder_name',
        'folder_description',
        'is_active',
        \DB::raw("CONCAT(ministry_id, '/', folder_number) AS fileindex") // Combine ministry_id and folder_number as fileindex
    ]);

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $query->where(function ($q) use ($search) {
            $q->where('folder_number', 'ILIKE', $search)
              ->orWhere('folder_name', 'ILIKE', $search)
              ->orWhere('folder_description', 'ILIKE', $search);
        });
    }

    return $query->orderBy($order_by, $sort);
}



    /**
     * Get a list of folders for dropdowns
     * 
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'folder_name', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }



}
