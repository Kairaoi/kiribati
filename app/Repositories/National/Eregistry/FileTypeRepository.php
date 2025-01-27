<?php

namespace App\Repositories\National\Eregistry;

use App\Repositories\BaseRepository;
use App\Models\National\Eregistry\FileType;
use Auth;


class FileTypeRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model()
    {
        return FileType::class;
    }

    /**
     * Create a new file type record
     * 
     * @param array $input
     * @return FileType
     */
    public function create(array $input)
    {
        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
        ];

        $fileType = $this->model();
        $fileType = new $fileType($data);
        $fileType->save();

        return $fileType;
    }

    /**
     * Update an existing file type record
     * 
     * @param FileType $model
     * @param array $input
     * @return bool
     */
    public function update(FileType $model, array $input)
    {
        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? $model->description,
        ];

        return $model->update($data);
    }

    /**
     * Get file types for data table with search and sorting
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc')
    {
        $query = $this->model->query()->select(['id', 'name', 'description']);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where('name', 'ILIKE', $search)
                  ->orWhere('description', 'ILIKE', $search);
        }

        return $query->orderBy($order_by, $sort);
    }

    /**
     * Get a list of file types for dropdowns
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
