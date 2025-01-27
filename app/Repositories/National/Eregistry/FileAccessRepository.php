<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\FileAccess;
use Auth;

class FileAccessRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FileAccess::class;
    }

    /**
     * Create a new FileAccess record
     *
     * @param array $input
     * @return FileAccess
     */
    public function create(array $input)
    {
        $data = [
            'file_id' => $input['file_id'],
            'ministry_id' => $input['ministry_id'],
            'division_id' => $input['division_id'],
            'access_type' => $input['access_type'], // Should be one of 'view', 'edit', 'full'
            'is_active' => $input['is_active'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $fileAccess = $this->model();
        $fileAccess = new $fileAccess($data);
        $fileAccess->save();

        return $fileAccess;
    }

    /**
     * Update an existing FileAccess record
     *
     * @param FileAccess $model
     * @param array $input
     * @return bool
     */
    public function update(FileAccess $model, array $input)
    {
        $data = [
            'file_id' => $input['file_id'] ?? $model->file_id,
            'ministry_id' => $input['ministry_id'] ?? $model->ministry_id,
            'division_id' => $input['division_id'] ?? $model->division_id,
            'access_type' => $input['access_type'] ?? $model->access_type,
            'is_active' => $input['is_active'] ?? $model->is_active,
            'updated_by' => Auth::id(),
        ];

        return $model->update($data);
    }

    /**
     * Get FileAccess records for data table with search and sorting
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()
            ->select([
                'id',
                'file_id',
                'ministry_id',
                'division_id',
                'access_type',
                'is_active',
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('access_type', 'ILIKE', $search)
                  ->orWhere('id', 'ILIKE', $search);
            });
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of FileAccess records for dropdowns
     *
     * @param string $column
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column = 'access_type', $key = 'id')
    {
        return $this->model->query()
            ->orderBy($column)
            ->pluck($column, $key);
    }
}
